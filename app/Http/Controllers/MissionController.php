<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mission;
use App\Models\PathPoint;
use Illuminate\Support\Facades\Auth;

class MissionController extends Controller
{
    public function loginPage()
    {
        return view('login');
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function loginCheck(\Illuminate\Http\Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Identifiants incorrects.',
        ]);
    }

    public function apiLogin(\Illuminate\Http\Request $request)
	{
		$credentials = $request->validate([
			'email' => ['required', 'email'],
			'password' => ['required'],
		]);

		if (Auth::attempt($credentials)) {
			$user = Auth::user();
			$token = $user->createToken('api')->plainTextToken;

			return response()->json([
				'status' => 'success',
				'token' => $token,
				'user' => $user,
			]);
		}
	
		return response()->json([
			'status' => 'error',
			'message' => 'Identifiants incorrects'
		], 401);
	}

    public function dashboardPage()
    {
        $weedsCount = \App\Models\Detection::where('is_weed', 1)->count();
        $lastPoint = \App\Models\PathPoint::latest('id')->first();
        
        return view('dashboard', compact('weedsCount', 'lastPoint'));
    }

    public function statisticsPage()
    {
        $weeds = \App\Models\Detection::where('is_weed', 1)->orderBy('created_at', 'desc')->paginate(15);
        $weedsCount = \App\Models\Detection::where('is_weed', 1)->count();

        return view('statistics', compact('weeds', 'weedsCount'));
    }

    public function mapPage(\Illuminate\Http\Request $request)
    {
        $missionId = $request->get('mission_id');

        if ($missionId) {
            $mission = Mission::find($missionId);
        } else {
            // R cup re la mission en cours si elle existe
            $mission = Mission::where('statut', 'ongoing')->latest()->first();

            // Si aucune mission en cours, on prend la plus r cente pending
            if (!$mission) {
                $mission = Mission::where('statut', 'pending')->latest()->first();
            }
        }

        // R cup re les missions disponibles   charger (pas encore termin es ou annul es)
        $availableMissions = Mission::whereIn('statut', ['pending', 'ongoing', 'paused'])
            ->orderBy('date_mission', 'desc')
            ->get();

        return view('map', compact('mission', 'availableMissions'));
    }

    public function missionsPage()
    {
        $missions = Mission::with('operator')->orderBy('date_mission', 'desc')->get();
        return view('missions', compact('missions'));
    }

    public function historyPage()
    {
        $missions = Mission::where('statut', 'completed')
            ->withCount('pointsTrajet')
            ->orderBy('date_mission', 'desc')
            ->get();

        return view('history', compact('missions'));
    }

    public function show(int $id)
    {
        $mission = Mission::with(['pointsTrajet', 'detections', 'operator'])->findOrFail($id);
        return view('mission_detail', compact('mission'));
    }

    public function create()
    {
        return view('mission_create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_mission' => 'required|date',
            'culture' => 'nullable|string|max:100',
            'target_dose' => 'nullable|numeric',
            'target_speed' => 'nullable|numeric',
        ]);

        $mission = new Mission();
        $mission->nom = $validated['nom'];
        $mission->description = $validated['description'] ?? null;
        $mission->date_mission = $validated['date_mission'];
        $mission->statut = 'pending';
        $mission->culture = $validated['culture'] ?? null;
        $mission->target_dose = $validated['target_dose'] ?? null;
        $mission->target_speed = $validated['target_speed'] ?? null;
        $mission->operator_id = Auth::id();
        $mission->save();

        return redirect()->route('missions')->with('success', 'Mission cr  e avec succ s !');
    }

    public function destroy(int $id)
    {
        $mission = Mission::findOrFail($id);
        $mission->delete();

        return redirect()->route('missions')->with('success', 'Mission supprim e avec succ s !');
    }

    // --- API METHODS ---

    public function apiCurrentMission()
    {
        $mission = Mission::where('statut', 'ongoing')->latest()->first();

        if (!$mission) {
            $mission = Mission::where('statut', 'pending')->orderBy('date_mission', 'asc')->first();
        }

        return response()->json([
            'status' => 'success',
            'data' => $mission
        ]);
    }

    public function apiUpdateStatus(Request $request, \App\Services\MqttService $mqtt)
    {
        $validated = $request->validate([
            'mission_id' => 'required|exists:missions,id',
            'status' => 'required|in:pending,ongoing,completed,paused,cancelled'
        ]);

        $mission = Mission::find($validated['mission_id']);
        $oldStatus = $mission->statut;
        $mission->statut = $validated['status'];
        $mission->save();

        // Automatisation MQTT
        try {
            if ($validated['status'] === 'ongoing') {
                // D�marrage ou Reprise
                $mqtt->send('raspberry/cmd', 'run:RTKfinal');
            } elseif ($validated['status'] === 'completed' || $validated['status'] === 'cancelled') {
                // Fin de mission ou Annulation
                $mqtt->send('raspberry/cmd', 'stop:RTKfinal');
            }
        } catch (\Exception $e) {
            // On log l'erreur mais on ne bloque pas la r�ponse API si le MQTT �choue
            \Illuminate\Support\Facades\Log::error("Erreur MQTT lors du changement de statut: " . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'message' => 'Status updated']);
    }

    public function apiTelemetry(Request $request)
    {
        // Enregistrement des points de passage (Trajet)
        // Expected: lat, lon, speed, battery, mission_id
        $validated = $request->validate([
            'mission_id' => 'required|exists:missions,id',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'speed' => 'nullable|numeric',
            'battery' => 'nullable|numeric'
        ]);

        $point = new PathPoint();
        $point->mission_id = $validated['mission_id'];
        $point->latitude = $validated['lat'];
        $point->longitude = $validated['lon'];
        // $point->vitesse = $validated['speed']; // Si la colonne existe
        // $point->batterie = $validated['battery']; // Si la colonne existe
        $point->save();

        return response()->json(['status' => 'success']);
    }

    public function apiStoreDetection(Request $request)
    {
        // Enregistrement d'une d tection IA
        // Expected: mission_id, lat, lon, class_ia, confidence, image (file or base64)

        $validated = $request->validate([
            'mission_id' => 'required|exists:missions,id',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'is_weed' => 'nullable|boolean',
            'class_ia' => 'nullable|string', // Pour compatibilité avec l'ancien script
            'confidence' => 'nullable|numeric' // Pour compatibilité
        ]);

        // TODO: G rer l'upload d'image si pr sent
        // $path = $request->file('image')->store('detections');

        // Cr er l'entr e
        // Note: Il faut adapter selon le mod le Detection
        $detection = new \App\Models\Detection();
        $detection->mission_id = $validated['mission_id'];
        $detection->latitude = $validated['lat'];
        $detection->longitude = $validated['lon'];
        
        if (isset($validated['is_weed'])) {
            $detection->is_weed = $validated['is_weed'];
        } else if (isset($validated['class_ia'])) {
            $detection->is_weed = (strtolower($validated['class_ia']) === 'weed') ? 1 : 0;
        } else {
            $detection->is_weed = 0;
        }
        // $detection->photo_path = $path;
        // Note: point_trajet_id might be required by schema, need to handle nullable or find closest
        $detection->point_trajet_id = 1; // Temporary fix until logic to find closest point
        $detection->save();

        return response()->json(['status' => 'success', 'id' => $detection->id]);
    }
	
	public function optionsPage(Request $request)
	{
		$currentMode = $request->session()->get('ui_mode', 'desktop');
		return view('options', [
			'currentMode' => $currentMode,
		]);
    }
	
	public function updateUiMode(Request $request)
	{
		$validated = $request->validate([
			'ui_mode' => 'required|in:desktop,mobile',
		]);
		
		$request->session()->put('ui_mode', $validated['ui_mode']);
		
		return redirect('/options')
			->with('success', "Preferences d'affichage mises a jour.");
	}
	
    public function apiGnssData(Request $request)
    {
        // Endpoint simplifi  pour le script Python RTK
        // Ne n cessite pas de mission_id (trouve la mission en cours auto)

        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'altitude' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'pressure' => 'nullable|numeric',
            'timestamp' => 'nullable|integer',
            'fix_quality' => 'nullable|integer',
            'alerts' => 'nullable|array'
        ]);

        // 1. Trouver la mission en cours
        $mission = Mission::where('statut', 'ongoing')->latest()->first();

        // Si aucune mission en cours, on prend la derni re cr  e (ou on cr e une erreur ?)
        // Pour l'instant, on log sur la derni re mission active ou pending pour tester
        if (!$mission) {
            $mission = Mission::latest()->first();
        }

        if (!$mission) {
            // Cr ation automatique d'une mission par d faut pour le test
            $mission = new Mission();
            $mission->nom = "Mission Auto-g n r e (Test)";
            $mission->statut = "ongoing";
            $mission->date_mission = now();
            $mission->operator_id = null; // Nullable pour auto-cr ation
            $mission->save();
        }

        if (!$mission) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create mission'], 500);
        }

        // 2. Créer le point
        $point = new PathPoint();
        $point->mission_id = $mission->id;
        $point->latitude = $validated['latitude'];
        $point->longitude = $validated['longitude'];
        $point->altitude = $validated['altitude'] ?? 0.0;
        $point->speed = $validated['speed'] ?? 0.0;
        $point->pressure = $validated['pressure'] ?? 0.0;
        
        if (isset($validated['timestamp'])) {
            $point->timestamp = date('Y-m-d H:i:s', $validated['timestamp']);
        }

        $point->save();

        return response()->json(['status' => 'success', 'mission_id' => $mission->id]);
    }

    public function apiGetPath(int $id)
    {
        $mission = Mission::findOrFail($id);
        $points = $mission->pathPoints()
            ->orderBy('timestamp', 'asc')
            ->get(['latitude', 'longitude', 'timestamp']);

        return response()->json([
            'status' => 'success',
            'mission' => [
                'id' => $mission->id,
                'nom' => $mission->nom,
                'statut' => $mission->statut
            ],
            'points' => $points
        ]);
    }

    public function apiVideoPush(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|string', // Base64
            'detections' => 'nullable|array',
            'mission_id' => 'nullable|exists:missions,id',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
        ]);

        // 1. Trouver la mission
        $missionId = $validated['mission_id'] ?? Mission::where('statut', 'ongoing')->latest()->first()?->id;
        if (!$missionId) {
            $missionId = Mission::latest()->first()?->id ?? 1;
        }

        // 2. D coder et sauvegarder l'image
        $image = $validated['image'];
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = 'detection_' . time() . '_' . uniqid() . '.jpg';
        \Illuminate\Support\Facades\Storage::disk('public')->put('detections/' . $imageName, base64_decode($image));

        // 3. Cr er la d tection (si au moins une d tection YOLO est pr sente ou si on veut juste log l'image)
        $detection = new \App\Models\Detection();
        $detection->mission_id = $missionId;
        $detection->latitude = $validated['lat'] ?? 0;
        $detection->longitude = $validated['lon'] ?? 0;
        // Déduction si c'est une mauvaise herbe d'après la classe YOLO
        $detection->is_weed = (!empty($validated['detections']) && strtolower($validated['detections'][0]['name']) === 'weed') ? 1 : 0;
        $detection->point_trajet_id = 1; // Temporaire
        $detection->save();

        return response()->json(['status' => 'success', 'id' => $detection->id]);
    }
}
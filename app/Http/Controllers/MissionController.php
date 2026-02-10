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
            return response()->json([
                'status' => 'success',
                'user' => Auth::user()
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Identifiants incorrects'
        ], 401);
    }

    public function dashboardPage()
    {
        return view('dashboard');
    }

    public function mapPage(\Illuminate\Http\Request $request)
    {
        $missionId = $request->get('mission_id');

        if ($missionId) {
            $mission = Mission::find($missionId);
        } else {
            // Récupère la mission en cours si elle existe
            $mission = Mission::where('statut', 'ongoing')->latest()->first();

            // Si aucune mission en cours, on prend la plus récente pending
            if (!$mission) {
                $mission = Mission::where('statut', 'pending')->latest()->first();
            }
        }

        // Récupère les missions disponibles à charger (pas encore terminées ou annulées)
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

        return redirect()->route('missions')->with('success', 'Mission créée avec succès !');
    }

    public function destroy(int $id)
    {
        $mission = Mission::findOrFail($id);
        $mission->delete();

        return redirect()->route('missions')->with('success', 'Mission supprimée avec succès !');
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

    public function apiUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'mission_id' => 'required|exists:missions,id',
            'status' => 'required|in:pending,ongoing,completed,paused,cancelled'
        ]);

        $mission = Mission::find($validated['mission_id']);
        $mission->statut = $validated['status'];
        $mission->save();

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
        // Enregistrement d'une détection IA
        // Expected: mission_id, lat, lon, class_ia, confidence, image (file or base64)

        $validated = $request->validate([
            'mission_id' => 'required|exists:missions,id',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'class_ia' => 'required|string',
            'confidence' => 'required|numeric'
        ]);

        // TODO: Gérer l'upload d'image si présent
        // $path = $request->file('image')->store('detections');

        // Créer l'entrée
        // Note: Il faut adapter selon le modèle Detection
        $detection = new \App\Models\Detection();
        $detection->mission_id = $validated['mission_id'];
        $detection->latitude = $validated['lat'];
        $detection->longitude = $validated['lon'];
        $detection->class_ia = $validated['class_ia'];
        $detection->confidence = $validated['confidence'];
        // $detection->photo_path = $path;
        // Note: point_trajet_id might be required by schema, need to handle nullable or find closest
        $detection->point_trajet_id = 1; // Temporary fix until logic to find closest point
        $detection->save();

        return response()->json(['status' => 'success', 'id' => $detection->id]);
    }

    public function apiGnssData(Request $request)
    {
        // Endpoint simplifié pour le script Python RTK
        // Ne nécessite pas de mission_id (trouve la mission en cours auto)

        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'fix_quality' => 'nullable|integer',
            'alerts' => 'nullable|array'
        ]);

        // 1. Trouver la mission en cours
        $mission = Mission::where('statut', 'ongoing')->latest()->first();

        // Si aucune mission en cours, on prend la dernière créée (ou on crée une erreur ?)
        // Pour l'instant, on log sur la dernière mission active ou pending pour tester
        if (!$mission) {
            $mission = Mission::latest()->first();
        }

        if (!$mission) {
            // Création automatique d'une mission par défaut pour le test
            $mission = new Mission();
            $mission->nom = "Mission Auto-générée (Test)";
            $mission->statut = "ongoing";
            $mission->date_mission = now();
            $mission->operator_id = null; // Nullable pour auto-création
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
        // On pourrait stocker fix_quality ou alerts si on avait les colonnes
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

        // 2. Décoder et sauvegarder l'image
        $image = $validated['image'];
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = 'detection_' . time() . '_' . uniqid() . '.jpg';
        \Illuminate\Support\Facades\Storage::disk('public')->put('detections/' . $imageName, base64_decode($image));

        // 3. Créer la détection (si au moins une détection YOLO est présente ou si on veut juste log l'image)
        $detection = new \App\Models\Detection();
        $detection->mission_id = $missionId;
        $detection->latitude = $validated['lat'] ?? 0;
        $detection->longitude = $validated['lon'] ?? 0;
        // On prend la première classe détectée par YOLO par défaut
        $detection->class_ia = !empty($validated['detections']) ? $validated['detections'][0]['name'] : 'unknown';
        $detection->confidence = !empty($validated['detections']) ? ($validated['detections'][0]['confidence'] * 100) : 0;
        $detection->photo_path = 'detections/' . $imageName;
        $detection->point_trajet_id = 1; // Temporaire
        $detection->save();

        return response()->json(['status' => 'success', 'id' => $detection->id]);
    }
}

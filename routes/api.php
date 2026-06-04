<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MissionController;

// -----------------------------
// PUBLIC ENDPOINTS
// -----------------------------
Route::post('/login', [MissionController::class, 'apiLogin']);

Route::get('/dev/token', function () {
    $user = \App\Models\User::first();
    if (!$user) {
        return response()->json(['error' => 'No user found'], 404);
    }
    $token = $user->createToken('raspberry-pi')->plainTextToken;
    return response()->json(['token' => $token, 'user' => $user->email]);
});

// Proxy flux vidéo

// -----------------------------
// PROTECTED ENDPOINTS (token Sanctum obligatoire)
// -----------------------------
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // GNSS
    Route::post('/gnss/data', [MissionController::class, 'apiGnssData']);

    // Missions
    Route::get('/mission/current', [MissionController::class, 'apiCurrentMission']);
    Route::post('/mission/status', [MissionController::class, 'apiUpdateStatus']);
    Route::get('/mission/{id}/path', [MissionController::class, 'apiGetPath']);

    // Télémetry
    Route::post('/telemetry', [MissionController::class, 'apiTelemetry']);

    // Robot IP tracking
    Route::post('/robot/ip', function (Request $request) {
        $ip = $request->input('ip');
        if ($ip) {
            \Illuminate\Support\Facades\Cache::store('file')->put('robot_ip', $ip, now()->addHours(24));
            return response()->json(['status' => 'success', 'ip' => $ip]);
        }
        return response()->json(['error' => 'IP not provided'], 400);
    });

    // Détections IA
    Route::post('/detection', [MissionController::class, 'apiStoreDetection']);
    Route::post('/video', [MissionController::class, 'apiVideoPush']);

    // Logout
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['status' => 'success']);
    });
});
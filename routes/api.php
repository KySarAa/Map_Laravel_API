<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MissionController;

// -----------------------------
// PUBLIC ENDPOINTS (pas de token)
// -----------------------------

// Login API (génère un token si besoin)
Route::post('/login', [MissionController::class, 'apiLogin']);

// GNSS DATA — PUBLIC
Route::post('/gnss/data', [MissionController::class, 'apiGnssData']);


// -----------------------------
// PROTECTED ENDPOINTS (token Sanctum obligatoire)
// -----------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Infos utilisateur
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Missions
    Route::get('/mission/current', [MissionController::class, 'apiCurrentMission']);
    Route::post('/mission/status', [MissionController::class, 'apiUpdateStatus']);
    Route::get('/mission/{id}/path', [MissionController::class, 'apiGetPath']);

    // Télémetry (ancienne version)
    Route::post('/telemetry', [MissionController::class, 'apiTelemetry']);

    // Détections IA
    Route::post('/detection', [MissionController::class, 'apiStoreDetection']);
    Route::post('/video', [MissionController::class, 'apiVideoPush']);

    // Logout API
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['status' => 'success']);
    });
});

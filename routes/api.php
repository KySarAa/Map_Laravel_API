<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MissionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// API Authentification
Route::post('/login', [MissionController::class, 'apiLogin']);

// Mission Endpoints (Protected would be better, but keeping open for simplicity if needed by ramp without token logic yet)
// In production: Route::middleware('auth:sanctum')->group(function () { ... });

Route::get('/mission/current', [MissionController::class, 'apiCurrentMission']);
Route::post('/mission/status', [MissionController::class, 'apiUpdateStatus']);
Route::post('/telemetry', [MissionController::class, 'apiTelemetry']);
Route::post('/detection', [MissionController::class, 'apiStoreDetection']);
Route::post('/video', [MissionController::class, 'apiVideoPush']);
Route::post('/gnss/data', [MissionController::class, 'apiGnssData']);

Route::get('/mission/{id}/path', [MissionController::class, 'apiGetPath']);

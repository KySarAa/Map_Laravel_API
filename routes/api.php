<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MissionController;

Route::post('/login', [MissionController::class, 'apiLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/mission/current', [MissionController::class, 'apiCurrentMission']);
    Route::post('/mission/status', [MissionController::class, 'apiUpdateStatus']);
    Route::post('/telemetry', [MissionController::class, 'apiTelemetry']);
    Route::post('/detection', [MissionController::class, 'apiStoreDetection']);
    Route::post('/video', [MissionController::class, 'apiVideoPush']);
    Route::post('/gnss/data', [MissionController::class, 'apiGnssData']);
    Route::get('/mission/{id}/path', [MissionController::class, 'apiGetPath']);

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['status' => 'success']);
    });
});
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\MqttController;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [MissionController::class, 'apiLogin']);

Route::get('/mission/current', [MissionController::class, 'apiCurrentMission']);
Route::post('/mission/status', [MissionController::class, 'apiUpdateStatus']);
Route::post('/telemetry', [MissionController::class, 'apiTelemetry']);
Route::post('/detection', [MissionController::class, 'apiStoreDetection']);
Route::post('/video', [MissionController::class, 'apiVideoPush']);
Route::post('/gnss/data', [MissionController::class, 'apiGnssData']);

Route::get('/send-bj', [MqttController::class, 'sendBj']);
Route::get('/start-ia/{name}', [MqttController::class, 'startIA']);
Route::get('/stop-ia/{name}', [MqttController::class, 'stopIA']);

Route::get('/mission/{id}/path', [MissionController::class, 'apiGetPath']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MissionController;

Route::get('/login', [MissionController::class, 'loginPage']);
Route::post('/login', [MissionController::class, 'loginCheck'])->name('login');
Route::post('/logout', [MissionController::class, 'logout'])->name('logout');
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect('/dashboard');
    });
    Route::get('/dashboard', [MissionController::class, 'dashboardPage'])->name('dashboard');
    Route::get('/map', [MissionController::class, 'mapPage'])->name('map');
    Route::get('/missions', [MissionController::class, 'missionsPage'])->name('missions');
    Route::get('/missions/create', [MissionController::class, 'create'])->name('missions.create');
    Route::post('/missions', [MissionController::class, 'store'])->name('missions.store');
    Route::get('/missions/{id}', [MissionController::class, 'show']);
    Route::delete('/missions/{id}', [MissionController::class, 'destroy'])->name('missions.destroy');
    Route::get('/history', [MissionController::class, 'historyPage'])->name('history');

    // Route temporaire pour forcer la migration si le terminal est bloqué
    Route::get('/migrate-db', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            return "Migration réussie ! <br><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
        } catch (\Exception $e) {
            return "Erreur lors de la migration : " . $e->getMessage();
        }
    });
});

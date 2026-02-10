<?php

namespace App\Http\Controllers;

use App\Models\GpsPoint;
use Illuminate\Http\Request;

class GpsPointController extends Controller
{
    public function index($mission_id)
    {
        return GpsPoint::where('mission_id', $mission_id)->get();
    }

    public function store(Request $request, $mission_id)
    {
        return GpsPoint::create([
            'mission_id' => $mission_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'altitude' => $request->altitude,
            'vitesse' => $request->vitesse,
            'timestamp' => now(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ZoneTraitee;
use Illuminate\Http\Request;

class ZoneTraiteeController extends Controller
{
    public function index($mission_id)
    {
        return ZoneTraitee::where('mission_id', $mission_id)->get();
    }

    public function store(Request $request, $mission_id)
    {
        return ZoneTraitee::create([
            'mission_id' => $mission_id,
            'zone_geojson' => $request->zone_geojson,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MissionProgress;
use Illuminate\Http\Request;

class MissionProgressController extends Controller
{
    public function show($mission_id)
    {
        return MissionProgress::where('mission_id', $mission_id)->first();
    }

    public function update(Request $request, $mission_id)
    {
        return MissionProgress::updateOrCreate(
            ['mission_id' => $mission_id],
            ['pourcentage' => $request->pourcentage]
        );
    }
}

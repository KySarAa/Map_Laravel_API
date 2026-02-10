<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PathPoint;
use App\Models\Mission;
use App\Models\Detection;

class RTKController extends Controller
{
    public function receive(Request $request)
    {
        $data = $request->validate([
            'mission_id' => 'required|exists:missions,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'altitude' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'pressure' => 'nullable|numeric',
        ]);

        $point = PathPoint::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $point
        ]);
    }

    public function storeDetection(Request $request, $mission_id)
    {
        $data = $request->validate([
            'path_point_id' => 'required|exists:path_points,id',
            'class_ia' => 'required|string',
            'confidence' => 'required|numeric',
            'applied_quantity' => 'nullable|numeric',
            'photo_path' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $data['mission_id'] = $mission_id;
        $detection = Detection::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $detection
        ]);
    }
}

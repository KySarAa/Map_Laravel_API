<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detection extends Model
{
    protected $fillable = [
        'mission_id',
        'point_trajet_id',
        'is_weed',
        'latitude',
        'longitude'
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function pathPoint()
    {
        return $this->belongsTo(PathPoint::class);
    }
}

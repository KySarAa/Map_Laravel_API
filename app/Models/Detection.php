<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detection extends Model
{
    protected $fillable = [
        'mission_id',
        'path_point_id',
        'class_ia',
        'confidence',
        'applied_quantity',
        'photo_path',
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

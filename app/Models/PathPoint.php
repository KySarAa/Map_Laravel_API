<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PathPoint extends Model
{
    protected $fillable = [
        'mission_id',
        'latitude',
        'longitude',
        'altitude',
        'speed',
        'pressure',
        'timestamp'
    ];

    public $timestamps = false;

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function detections()
    {
        return $this->hasMany(Detection::class);
    }
}

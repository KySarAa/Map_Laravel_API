<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GpsPoint extends Model
{
    protected $fillable = [
        'mission_id', 'latitude', 'longitude', 'altitude', 'vitesse', 'timestamp'
    ];

    public $timestamps = false;
}

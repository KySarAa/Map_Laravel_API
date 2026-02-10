<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneTraitee extends Model
{
    protected $fillable = [
        'mission_id', 'zone_geojson'
    ];

    public $timestamps = false;
}

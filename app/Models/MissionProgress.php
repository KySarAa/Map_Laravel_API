<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissionProgress extends Model
{
    protected $fillable = [
        'mission_id', 'pourcentage'
    ];

    public $timestamps = false;
}

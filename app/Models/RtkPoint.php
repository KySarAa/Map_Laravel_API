<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RtkPoint extends Model
{
    protected $fillable = [
        'latitude',
        'longitude',
        'altitude',
    ];
}

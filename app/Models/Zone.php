<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'mission_id',
        'name',
        'geojson'
    ];

    protected $casts = [
        'geojson' => 'array'
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{

    protected $fillable = [
        'nom',
        'description',
        'date_mission',
        'statut',
        'operator_id',
        'culture',
        'target_dose',
        'target_speed'
    ];

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function pathPoints()
    {
        return $this->hasMany(PathPoint::class);
    }

    public function pointsTrajet()
    {
        return $this->pathPoints();
    }

    public function detections()
    {
        return $this->hasMany(Detection::class);
    }
}

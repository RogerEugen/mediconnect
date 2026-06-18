<?php
// app/Models/DoctorSpecialization.php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DoctorSpecialization extends Pivot
{
    public $timestamps = false;

    protected $table = 'doctor_specializations';

    protected $fillable = [
        'user_id',
        'specialization_id',
        'is_primary',
        'certified_at',
    ];

    protected $casts = [
        'is_primary'   => 'boolean',
        'certified_at' => 'date',
    ];
}
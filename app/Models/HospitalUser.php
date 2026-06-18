<?php
// app/Models/HospitalUser.php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class HospitalUser extends Pivot
{
    public $timestamps = false;

    protected $table = 'hospital_user';

    protected $fillable = [
        'user_id',
        'hospital_id',
        'role_at_hospital',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];
}
<?php
// app/Models/Hospital.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'license_number',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];


    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'hospital_user',
            'hospital_id',
            'user_id'
        )
            ->using(\App\Models\HospitalUser::class)
            ->withPivot('role_at_hospital', 'is_primary');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function cases()
    {
        return $this->hasMany(MedicalCase::class);
    }
}

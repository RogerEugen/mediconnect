<?php
// app/Models/Specialization.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\DoctorSpecialization;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function doctors()
    {
        return $this->belongsToMany(
            User::class,
            'doctor_specializations',
            'specialization_id',
            'user_id'
        )
            ->using(\App\Models\DoctorSpecialization::class)
            ->withPivot('is_primary', 'certified_at');
    }
    public function cases()
    {
        return $this->hasMany(MedicalCase::class);
    }
}

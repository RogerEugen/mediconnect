<?php
// app/Models/Patient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_uid',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'phone',
        'address',
        'national_id',
        'registered_by',
    ];

    protected $casts = ['date_of_birth' => 'date'];
    
    // Relationships start here 
    // Full name accessor
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function cases()
    {
        return $this->hasMany(MedicalCase::class);
    }

    // ── Static helpers ────────────────────────────────────

    public static function generateUid(): string
    {
        $year  = now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'MC-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth
            ? Carbon::parse($this->date_of_birth)->age
            : null;
    }
}

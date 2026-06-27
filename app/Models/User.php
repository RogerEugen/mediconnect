<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'hospital_id',
        'is_active',
        'approved_at',
        'approved_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    // ── Helpers ──────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isSpecialist(): bool
    {
        return $this->role === 'specialist';
    }

    // ── Relationships ─────────────────────────────────────

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(self::class, 'approved_by');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function photo()
    {
        return $this->hasOne(UserPhoto::class);
    }

    public function specializations()
    {
        return $this->belongsToMany(
            Specialization::class,
            'doctor_specializations',
            'user_id',
            'specialization_id'
        )
            ->using(DoctorSpecialization::class)
            ->withPivot('is_primary', 'certified_at');
    }

    public function hospitals()
    {
        return $this->belongsToMany(
            Hospital::class,
            'hospital_user',
            'user_id',
            'hospital_id'
        )
            ->using(HospitalUser::class)
            ->withPivot('role_at_hospital', 'is_primary');
    }

    public function registeredPatients()
    {
        return $this->hasMany(Patient::class, 'registered_by');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id');
    }

    public function postedCases()
    {
        return $this->hasMany(MedicalCase::class, 'posted_by');
    }

    public function assignedCases()
    {
        return $this->hasMany(CaseAssignment::class, 'specialist_id');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}

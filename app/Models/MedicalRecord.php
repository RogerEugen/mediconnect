<?php
// app/Models/MedicalRecord.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'hospital_id',
        'visit_date',
        'visit_type',
        'symptoms',
        'diagnosis',
        'treatment_plan',
        'prescription',
        'notes',
        'status',
    ];

    protected $casts = ['visit_date' => 'date'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function cases()
    {
        return $this->hasMany(MedicalCase::class, 'medical_record_id');
    }


    // ── Helpers ───────────────────────────────────────────

    public function getVisitTypeLabelAttribute(): string
    {
        return match($this->visit_type) {
            'outpatient'  => 'Outpatient',
            'inpatient'   => 'Inpatient',
            'emergency'   => 'Emergency',
            'follow_up'   => 'Follow-up',
            default       => ucfirst($this->visit_type),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active'   => 'blue',
            'resolved' => 'green',
            'pending'  => 'yellow',
            default    => 'gray',
        };
    }
}

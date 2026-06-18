<?php
// app/Models/MedicalCase.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalCase extends Model
{
    use HasFactory;

    protected $table = 'cases'; // 'cases' not 'medical_cases'

    protected $fillable = [
        'case_number',
        'patient_id',
        'medical_record_id',
        'posted_by',
        'hospital_id',
        'specialization_id',
        'title',
        'description',
        'symptoms',
        'prior_treatments',
        'urgency',
        'status',
        'resolution_notes',
        'resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function assignments()
    {
        return $this->hasMany(CaseAssignment::class, 'case_id');
    }
    public function activeAssignment()
    {
        return $this->hasOne(CaseAssignment::class, 'case_id')
                    ->whereNotIn('status', ['declined'])
                    ->latest();
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class, 'case_id');
    }

    // Generate unique case number
    public static function generateCaseNumber(): string
    {
        $year = now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;
        return "CASE-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

     public function getUrgencyColorAttribute(): string
    {
        return match($this->urgency) {
            'low'      => 'green',
            'medium'   => 'yellow',
            'high'     => 'orange',
            'critical' => 'red',
            default    => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open'          => 'blue',
            'assigned'      => 'purple',
            'in_discussion' => 'orange',
            'resolved'      => 'green',
            'closed'        => 'gray',
            default         => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'          => 'Open',
            'assigned'      => 'Assigned',
            'in_discussion' => 'In Discussion',
            'resolved'      => 'Resolved',
            'closed'        => 'Closed',
            default         => ucfirst($this->status),
        };
    }
}

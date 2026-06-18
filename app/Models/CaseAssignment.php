<?php
// app/Models/CaseAssignment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseAssignment extends Model
{
    protected $fillable = [
        'case_id',
        'specialist_id',
        'assigned_by',
        'status',
        'due_date',
        'decline_reason',
        'completed_at',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    public function case()
    {
        return $this->belongsTo(MedicalCase::class, 'case_id');
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}

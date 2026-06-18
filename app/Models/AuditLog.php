<?php
// app/Models/AuditLog.php

namespace App\Models;

// use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Static helper to log actions from anywhere
      public static function record(
        string  $action,
        ?string $description = null,
        ?object $model = null
    ): void {
        try {
            self::create([
                'user_id'    => Auth::id(),
                'action'     => $action,
                'model_type' => $model ? class_basename($model) : null,
                'model_id'   => $model?->id,
                'description'=> $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Never let audit logging break the app
            \Illuminate\Support\Facades\Log::error('AuditLog failed: ' . $e->getMessage());
        }
    }

    // ── Action label helper ───────────────────────────────

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'login'                   => 'Logged in',
            'logout'                  => 'Logged out',
            'viewed_patient'          => 'Viewed patient',
            'registered_patient'      => 'Registered patient',
            'updated_patient'         => 'Updated patient',
            'viewed_medical_record'   => 'Viewed medical record',
            'created_medical_record'  => 'Created medical record',
            'updated_medical_record'  => 'Updated medical record',
            'deleted_medical_record'  => 'Deleted medical record',
            'viewed_medical_records'  => 'Viewed medical history',
            'posted_case'             => 'Posted case',
            'resolved_case'           => 'Resolved case',
            'closed_case'             => 'Closed case',
            'assigned_case'           => 'Assigned specialist',
            'accepted_case'           => 'Accepted case',
            'declined_case'           => 'Declined case',
            'completed_case'          => 'Completed case',
            'posted_discussion'       => 'Posted discussion',
            'specialist_viewed_case'  => 'Viewed case (specialist)',
            default                   => ucwords(str_replace('_', ' ', $this->action)),
        };
    }

    // ── Color by action group ─────────────────────────────

    public function getActionColorAttribute(): string
    {
        if (str_contains($this->action, 'login') || str_contains($this->action, 'logout')) {
            return 'gray';
        }
        if (str_contains($this->action, 'viewed')) {
            return 'blue';
        }
        if (str_contains($this->action, 'created') || str_contains($this->action, 'registered') || str_contains($this->action, 'posted')) {
            return 'green';
        }
        if (str_contains($this->action, 'updated')) {
            return 'yellow';
        }
        if (str_contains($this->action, 'deleted') || str_contains($this->action, 'declined')) {
            return 'red';
        }
        if (str_contains($this->action, 'assigned') || str_contains($this->action, 'accepted') || str_contains($this->action, 'completed') || str_contains($this->action, 'resolved')) {
            return 'purple';
        }
        return 'gray';
    }
}
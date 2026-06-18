<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\NotificationSent;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'read_at'    => 'datetime',
        'created_at' => 'datetime',
    ];

     protected static function booted(): void
    {
        static::created(function (Notification $notification) {
            broadcast(new NotificationSent($notification));
        });
    }
    
    //relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }

    // Static helper — send a notification to a user
    public static function send(
        int    $userId,
        string $title,
        string $message,
        string $type
    ): self {
        return self::create([
            'user_id'    => $userId,
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'is_read'    => false,
            'created_at' => now(),
        ]);
    }

     // ── Icon helper based on type ─────────────────────────

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'new_case'           => 'clipboard',
            'case_assigned'      => 'user-check',
            'specialist_assigned'=> 'user-plus',
            'case_accepted'      => 'check-circle',
            'case_declined'      => 'x-circle',
            'case_completed'     => 'check-badge',
            'case_resolved'      => 'check-badge',
            'new_discussion'     => 'chat',
            default              => 'bell',
        };
    }


    // ── Color helper based on type ────────────────────────
    public function getColorAttribute(): string
    {
        return match ($this->type) {
            'new_case'            => 'blue',
            'case_assigned'       => 'purple',
            'specialist_assigned' => 'purple',
            'case_accepted'       => 'green',
            'case_declined'       => 'red',
            'case_completed'      => 'green',
            'case_resolved'       => 'green',
            'new_discussion'      => 'orange',
            default               => 'gray',
        };
    }
}

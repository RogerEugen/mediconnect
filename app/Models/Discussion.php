<?php
// app/Models/Discussion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $fillable = [
        'case_id',
        'user_id',
        'parent_id',
        'message',
        'is_expert_opinion',
        'is_edited',
    ];

    protected $casts = [
        'is_expert_opinion' => 'boolean',
        'is_edited'         => 'boolean',
    ];

    public function case()
    {
        return $this->belongsTo(MedicalCase::class, 'case_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Parent message (if this is a reply)
    public function parent()
    {
        return $this->belongsTo(Discussion::class, 'parent_id');
    }

    // Child replies to this message
    public function replies()
    {
        return $this->hasMany(Discussion::class, 'parent_id');
    }


    // ── Scopes ────────────────────────────────────────────

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}

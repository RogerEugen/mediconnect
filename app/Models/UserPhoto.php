<?php
// app/Models/UserPhoto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPhoto extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_at',
    ];

    protected $casts = ['uploaded_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

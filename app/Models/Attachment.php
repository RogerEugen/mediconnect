<?php

// app/Models/Attachment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'medical_record_id',
        'case_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function clinicalCase()
    {
        return $this->belongsTo(MedicalCase::class, 'case_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }

    public function getIsPdfAttribute(): bool
    {
        return $this->file_type === 'application/pdf';
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (! $this->file_size) {
            return '—';
        }

        return $this->file_size < 1024
            ? $this->file_size.' KB'
            : round($this->file_size / 1024, 1).' MB';
    }
}

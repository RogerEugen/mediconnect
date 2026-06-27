<?php

// app/Models/UserProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'bio',
        'staff_card_path',
        'staff_card_original_name',
    ];

    protected $casts = ['date_of_birth' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

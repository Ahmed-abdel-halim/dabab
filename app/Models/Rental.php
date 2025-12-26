<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $fillable = [
        'user_id',
        'personal_name',
        'commercial_name',
        'store_type',
        'rental_type', // scooter_only, scooter_with_driver
        'commercial_registration_file',
        'additional_details',
        'status', // pending, approved, rejected
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


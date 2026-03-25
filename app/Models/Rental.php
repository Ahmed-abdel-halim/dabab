<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'cost',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full URL for the commercial registration file
     */
    public function getCommercialRegistrationFileAttribute($value)
    {
        if ($value) {
            return Storage::disk('public')->url($value);
        }
        return null;
    }

    /**
     * Get the original file path (for internal use like deletion)
     */
    public function getOriginalFilePath()
    {
        return $this->attributes['commercial_registration_file'] ?? null;
    }
}


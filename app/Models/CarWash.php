<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarWash extends Model
{
    protected $fillable = [
        'user_id',
        'car_size', // small, large
        'wash_type', // interior_exterior, exterior, interior
        'scheduled_date',
        'scheduled_time',
        'time_period', // before_lunch, early_evening, dinner_time, late_night
        'location_id',
        'status', // pending, confirmed, completed, cancelled
        'cost',
        'payment_method',
        'payment_status',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(UserLocation::class, 'location_id');
    }
}


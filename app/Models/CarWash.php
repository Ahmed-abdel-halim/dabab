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

    protected $appends = [
        'scheduled_date_formatted',
        'scheduled_time_formatted',
    ];

    public function getScheduledDateFormattedAttribute()
    {
        return $this->scheduled_date ? $this->scheduled_date->format('Y-m-d') : null;
    }

    public function getScheduledTimeFormattedAttribute()
    {
        if ($this->scheduled_time) {
            if (is_string($this->scheduled_time)) {
                // إذا كان string، أعد HH:mm فقط
                return substr($this->scheduled_time, 0, 5);
            }
            // إذا كان Carbon instance
            if (method_exists($this->scheduled_time, 'format')) {
                return $this->scheduled_time->format('H:i');
            }
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(UserLocation::class, 'location_id');
    }
}


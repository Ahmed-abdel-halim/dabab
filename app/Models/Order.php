<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'category_id',
        'category_name',
        'details',
        'delivery_cost',
        'total_cost',
        'status', // pending, confirmed, in_progress, completed, cancelled
        'scheduled_at',
        'location_id',
        'payment_method', // cash, apple_pay, bank_card
        'payment_status', // pending, paid
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'delivery_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(UserLocation::class, 'location_id');
    }

    public function category()
    {
        return $this->belongsTo(OrderCategory::class, 'category_id');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}


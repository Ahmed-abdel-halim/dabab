<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'shipment_details',
        'sender_address',
        'sender_lat',
        'sender_lng',
        'sender_phone',
        'recipient_address',
        'recipient_lat',
        'recipient_lng',
        'recipient_phone',
        'status', // pending, in_progress, completed, cancelled
        'delivery_cost',
        'payment_method',
        'payment_status',
        'delivery_agent_id',
        'item_photo',
        'invoice_photo',
        'delivered_at',
    ];

    protected $casts = [
        'sender_lat' => 'decimal:7',
        'sender_lng' => 'decimal:7',
        'recipient_lat' => 'decimal:7',
        'recipient_lng' => 'decimal:7',
        'delivery_cost' => 'decimal:2',
        'delivered_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'delivery_agent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}


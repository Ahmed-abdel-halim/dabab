<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'category_id',
        'category_name',
        'details',
        'delivery_cost',
        'order_index',
    ];

    protected $casts = [
        'delivery_cost' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function category()
    {
        return $this->belongsTo(OrderCategory::class, 'category_id');
    }
}


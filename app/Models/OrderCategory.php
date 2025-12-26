<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCategory extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'icon',
        'fixed_price',
        'is_active',
    ];

    protected $casts = [
        'fixed_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}


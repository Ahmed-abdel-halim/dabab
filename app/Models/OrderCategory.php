<?php

namespace App\Models;

use App\Traits\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Model;

class OrderCategory extends Model
{
    use HasLocalizedAttributes;

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


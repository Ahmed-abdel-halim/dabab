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

    protected $hidden = [
        'name_ar',
        'name_en',
    ];

    protected $appends = [
        'name',
    ];

    /**
     * Get the name attribute based on current locale
     */
    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' 
            ? ($this->attributes['name_ar'] ?? null)
            : ($this->attributes['name_en'] ?? $this->attributes['name_ar'] ?? null);
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarWashPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_key',
        'time_range',
        'start_time',
        'end_time',
        'period_type',
        'is_active',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryVehicle extends Model
{
    protected $fillable = [
        'delivery_agent_profile_id',
        'vehicle_type',
        'vehicle_brand',
        'vehicle_model',
        'manufacturing_year',
        'license_plate_number',
        'license_plate_letters',
        'license_type',
    ];
}

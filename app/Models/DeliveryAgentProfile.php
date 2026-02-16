<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryAgentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nationality',
        'national_id_number',
        'birth_date',
        'status',
        'admin_comment',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->hasOne(DeliveryVehicle::class);
    }

    public function bankDetails()
    {
        return $this->hasOne(DeliveryBankDetail::class);
    }

    public function documents()
    {
        return $this->hasMany(DeliveryDocument::class);
    }
}

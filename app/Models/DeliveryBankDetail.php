<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryBankDetail extends Model
{
    protected $fillable = [
        'delivery_agent_profile_id',
        'bank_name',
        'account_holder_name',
        'iban',
    ];

    public function deliveryAgentProfile()
    {
        return $this->belongsTo(DeliveryAgentProfile::class);
    }
}

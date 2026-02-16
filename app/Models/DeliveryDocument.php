<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryDocument extends Model
{
    protected $fillable = [
        'delivery_agent_profile_id',
        'document_type',
        'file_path',
        'status',
        'rejection_reason',
    ];

    public function deliveryAgentProfile()
    {
        return $this->belongsTo(DeliveryAgentProfile::class);
    }
}

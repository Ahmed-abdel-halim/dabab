<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentCard extends Model
{
    protected $fillable = [
        'user_id',
        'card_holder_name',
        'card_number',
        'expiry_date',
        'brand',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

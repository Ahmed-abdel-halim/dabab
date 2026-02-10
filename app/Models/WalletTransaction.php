<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'transaction_type',
        'description_ar',
        'description_en',
        'reference_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

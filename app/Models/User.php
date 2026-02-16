<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'phone', 'email', 'otp', 'otp_expires_at', 'role', 'profile_photo'
    ];


    protected $hidden = [

    ];

    public function location()
    {
        return $this->hasOne(UserLocation::class)->where('is_default', true);
    }

    public function locations()
    {
        return $this->hasMany(UserLocation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function carWashes()
    {
        return $this->hasMany(CarWash::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function paymentCards()
    {
        return $this->hasMany(PaymentCard::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function deliveryAgentProfile()
    {
        return $this->hasOne(DeliveryAgentProfile::class);
    }

}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $isAr = $locale === 'ar';

        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'type' => $this->type, // credit/debit
            'transaction_type' => $this->transaction_type, // charge, refund, payment
            'description' => $isAr ? $this->description_ar : $this->description_en,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}

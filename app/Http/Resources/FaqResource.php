<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
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
            'question' => $isAr ? $this->question_ar : $this->question_en,
            'answer' => $isAr ? $this->answer_ar : $this->answer_en,
            'category' => $isAr ? $this->category_ar : $this->category_en,
            'sort_order' => $this->sort_order,
        ];
    }
}

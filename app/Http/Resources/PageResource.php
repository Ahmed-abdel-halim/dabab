<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
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
            'slug' => $this->slug,
            'title' => $isAr ? $this->title_ar : $this->title_en,
            'content' => $isAr ? $this->content_ar : $this->content_en,
            'updated_at' => $this->updated_at,
        ];
    }
}

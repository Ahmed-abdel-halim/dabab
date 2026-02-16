<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Faq;
use App\Models\Page;

use App\Http\Resources\FaqResource;
use App\Http\Resources\PageResource;
use App\Traits\ApiResponseTrait;

class InfoController extends Controller
{
    use ApiResponseTrait;

    public function getFaqs()
    {
        $faqs = Faq::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse(FaqResource::collection($faqs));
    }

    public function getPrivacyPolicy()
    {
        $page = Page::where('slug', 'privacy-policy')->first();

        if (!$page) {
            return $this->errorResponse(__('messages.info.privacy_policy_not_found'), 404);
        }

        return $this->successResponse(new PageResource($page));
    }

    public function getTermsAndConditions()
    {
        $page = Page::where('slug', 'terms-and-conditions')->first();

        if (!$page) {
            return $this->errorResponse(__('messages.info.terms_not_found'), 404);
        }

        return $this->successResponse(new PageResource($page));
    }

    public function getPage($slug)
    {
        $page = Page::where('slug', $slug)->first();

        if (!$page) {
            return $this->errorResponse(__('messages.info.page_not_found'), 404);
        }

        return $this->successResponse(new PageResource($page));
    }
}

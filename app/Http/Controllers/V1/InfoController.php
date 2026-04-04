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

    public function getFaqs(Request $request)
    {
        $type = $request->query('type', 'customer');
        
        $faqs = Faq::where('is_active', true)
            ->where('type', $type)
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse(FaqResource::collection($faqs));
    }

    public function getPrivacyPolicy(Request $request)
    {
        $type = $request->query('type', 'customer');
        $page = Page::where('slug', 'privacy-policy')->where('type', $type)->first();

        if (!$page) {
            return $this->errorResponse(__('messages.info.privacy_policy_not_found'), 404);
        }

        return $this->successResponse(new PageResource($page));
    }

    public function getTermsAndConditions(Request $request)
    {
        $type = $request->query('type', 'customer');
        $page = Page::where('slug', 'terms-and-conditions')->where('type', $type)->first();

        if (!$page) {
            return $this->errorResponse(__('messages.info.terms_not_found'), 404);
        }

        return $this->successResponse(new PageResource($page));
    }

    public function getPage(Request $request, $slug)
    {
        $type = $request->query('type', 'customer');
        $page = Page::where('slug', $slug)->where('type', $type)->first();

        if (!$page) {
            return $this->errorResponse(__('messages.info.page_not_found'), 404);
        }

        return $this->successResponse(new PageResource($page));
    }
}

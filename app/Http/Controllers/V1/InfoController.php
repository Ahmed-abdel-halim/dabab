<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Faq;
use App\Models\Page;

use App\Http\Resources\FaqResource;
use App\Http\Resources\PageResource;

class InfoController extends Controller
{
    public function getFaqs()
    {
        $faqs = Faq::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => FaqResource::collection($faqs)
        ]);
    }

    public function getPrivacyPolicy()
    {
        $page = Page::where('slug', 'privacy-policy')->first();

        if (!$page) {
            return response()->json([
                'status' => 'error',
                'message' => app()->getLocale() === 'ar' ? 'سياسة الخصوصية غير موجودة' : 'Privacy Policy not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => new PageResource($page)
        ]);
    }

    public function getTermsAndConditions()
    {
        $page = Page::where('slug', 'terms-and-conditions')->first();

        if (!$page) {
            return response()->json([
                'status' => 'error',
                'message' => app()->getLocale() === 'ar' ? 'الشروط والأحكام غير موجودة' : 'Terms and Conditions not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => new PageResource($page)
        ]);
    }

    public function getPage($slug)
    {
        $page = Page::where('slug', $slug)->first();

        if (!$page) {
            return response()->json([
                'status' => 'error',
                'message' => app()->getLocale() === 'ar' ? 'الصفحة غير موجودة' : 'Page not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => new PageResource($page)
        ]);
    }
}

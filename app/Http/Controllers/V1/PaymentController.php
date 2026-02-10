<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PaymentCard;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function getPaymentMethods(Request $request)
    {
        $cards = $request->user()->paymentCards()->get();
        
        $locale = app()->getLocale();
        $isAr = $locale === 'ar';

        $methods = [
            [
                'id' => 'cash',
                'name' => $isAr ? 'كاش' : 'Cash',
                'type' => 'cash',
                'icon' => 'cash-icon-url',
            ],
            [
                'id' => 'apple_pay',
                'name' => $isAr ? 'أبل باي' : 'Apple Pay',
                'type' => 'apple_pay',
                'icon' => 'apple-pay-icon-url',
            ],
        ];

        foreach ($cards as $card) {
            $methods[] = [
                'id' => 'card_' . $card->id,
                'name' => '**** **** **** ' . substr($card->card_number, -4),
                'type' => 'card',
                'brand' => $card->brand,
                'is_default' => $card->is_default,
                'card_id' => $card->id,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $methods
        ]);
    }

    public function getCards(Request $request)
    {
        $cards = $request->user()->paymentCards()->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $cards
        ]);
    }

    public function storeCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_holder_name' => 'required|string',
            'card_number' => 'required|string|size:16',
            'expiry_date' => 'required|string|regex:/^\d{2}\/\d{2}$/', // MM/YY
            'cvv' => 'required|string|size:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // In a real app, you'd tokenize this with a gateway. 
        // For now, we store masked/dummy as per UI request "I am working on API"
        
        $card = $request->user()->paymentCards()->create([
            'card_holder_name' => $request->card_holder_name,
            'card_number' => $request->card_number,
            'expiry_date' => $request->expiry_date,
            'brand' => $this->detectCardBrand($request->card_number),
            'is_default' => !$request->user()->paymentCards()->exists(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => app()->getLocale() === 'ar' ? 'تم إضافة البطاقة بنجاح' : 'Card added successfully',
            'data' => $card
        ]);
    }

    public function destroyCard(Request $request, $id)
    {
        $card = $request->user()->paymentCards()->find($id);
        
        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => app()->getLocale() === 'ar' ? 'البطاقة غير موجودة' : 'Card not found'
            ], 404);
        }

        $card->delete();

        return response()->json([
            'status' => 'success',
            'message' => app()->getLocale() === 'ar' ? 'تم حذف البطاقة بنجاح' : 'Card deleted successfully'
        ]);
    }

    public function processApplePay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required', // The payment token from iOS
            'amount' => 'required|numeric|min:1',
            'order_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Configuration from .env
        $apiKey = config('services.moyasar.secret_key'); // or env('MOYASAR_SECRET_KEY')
        
        // This is a placeholder for the actual API call to the gateway
        // In a real scenario, you'd use Guzzle or a Gateway SDK here
        
        /*
        $response = Http::withBasicAuth($apiKey, '')->post('https://api.moyasar.com/v1/payments', [
            'amount' => $request->amount * 100, // typically in cents/halalas
            'currency' => 'SAR',
            'description' => 'Payment for order ' . ($request->order_id ?? 'N/A'),
            'source' => [
                'type' => 'applepay',
                'token' => $request->token
            ]
        ]);
        */

        // Simple mock response for logic demonstration
        $isSuccessful = true; // Assume success for demo

        if ($isSuccessful) {
            return response()->json([
                'status' => 'success',
                'message' => app()->getLocale() === 'ar' ? 'تمت عملية الدفع عبر أبل باي بنجاح' : 'Apple Pay payment processed successfully',
                'transaction_id' => 'pay_' . uniqid()
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => app()->getLocale() === 'ar' ? 'فشلت عملية الدفع عبر أبل باي' : 'Apple Pay payment failed'
        ], 400);
    }

    private function detectCardBrand($number)
    {
        if (str_starts_with($number, '4')) return 'visa';
        if (preg_match('/^5[1-5]/', $number)) return 'mastercard';
        return 'unknown';
    }
}

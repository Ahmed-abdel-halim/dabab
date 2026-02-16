<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PaymentCard;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponseTrait;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function getPaymentMethods(Request $request)
    {
        $cards = $request->user()->paymentCards()->get();
        
        $methods = [
            [
                'id' => 'cash',
                'name' => __('messages.payment.cash'),
                'type' => 'cash',
                'icon' => 'cash-icon-url',
            ],
            [
                'id' => 'apple_pay',
                'name' => __('messages.payment.apple_pay'),
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

        return $this->successResponse($methods, __('messages.payment.methods_loaded'));
    }

    public function getCards(Request $request)
    {
        $cards = $request->user()->paymentCards()->get();
        
        return $this->successResponse($cards);
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
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $card = $request->user()->paymentCards()->create([
            'card_holder_name' => $request->card_holder_name,
            'card_number' => $request->card_number,
            'expiry_date' => $request->expiry_date,
            'brand' => $this->detectCardBrand($request->card_number),
            'is_default' => !$request->user()->paymentCards()->exists(),
        ]);

        return $this->successResponse($card, __('messages.payment.card_added'), 201);
    }

    public function destroyCard(Request $request, $id)
    {
        $card = $request->user()->paymentCards()->find($id);
        
        if (!$card) {
            return $this->errorResponse(__('messages.payment.card_not_found'), 404);
        }

        $card->delete();

        return $this->successResponse(null, __('messages.payment.card_deleted'));
    }

    public function processApplePay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required', // The payment token from iOS
            'amount' => 'required|numeric|min:1',
            'order_id' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $apiKey = config('services.moyasar.secret_key');
        if (!$apiKey) {
            return $this->errorResponse('Moyasar API key is not configured.', 500);
        }

        // وضع المحاكاة للاختبار إذا كان المفتاح افتراضياً (placeholder)
        if (str_contains($apiKey, 'xxxxxx')) {
            return $this->successResponse([
                'transaction_id' => 'pay_mock_' . date('Ymd') . '_' . uniqid(),
                'status' => 'paid',
                'amount' => $request->amount,
                'mode' => 'simulator'
            ], __('messages.payment.apple_pay_success'));
        }

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://api.moyasar.com/v1/payments', [
                'auth' => [$apiKey, ''],
                'json' => [
                    'amount' => $request->amount * 100, // Moyasar expects amount in halalas
                    'currency' => 'SAR',
                    'description' => $request->description ?? 'Order Payment #' . $request->order_id,
                    'source' => [
                        'type' => 'applepay',
                        'token' => $request->token
                    ],
                    'metadata' => [
                        'order_id' => $request->order_id,
                        'user_id' => $request->user()->id
                    ]
                ]
            ]);

            $paymentData = json_decode($response->getBody()->getContents(), true);

            if (isset($paymentData['status']) && $paymentData['status'] === 'paid') {
                return $this->successResponse([
                    'transaction_id' => $paymentData['id'],
                    'status' => $paymentData['status'],
                    'amount' => $paymentData['amount'] / 100,
                ], __('messages.payment.apple_pay_success'));
            }

            return $this->errorResponse($paymentData['message'] ?? __('messages.payment.apple_pay_failed'), 400);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorData = json_decode($e->getResponse()->getBody()->getContents(), true);
            return $this->errorResponse($errorData['message'] ?? 'Payment failed', $e->getCode());
        } catch (\Exception $e) {
            return $this->errorResponse(__('messages.error_occurred'), 500);
        }
    }

    private function detectCardBrand($number)
    {
        if (str_starts_with($number, '4')) return 'visa';
        if (preg_match('/^5[1-5]/', $number)) return 'mastercard';
        return 'unknown';
    }
}

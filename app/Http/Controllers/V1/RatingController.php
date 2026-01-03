<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Order;
use App\Models\Delivery;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    use ApiResponseTrait;

    public function createRating(Request $request)
    {
        $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'delivery_id' => 'nullable|exists:deliveries,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        if (!$request->order_id && !$request->delivery_id) {
            return $this->errorResponse(__('messages.rating.order_or_delivery_required'), 422);
        }

        if ($request->order_id) {
            $order = Order::where('user_id', $request->user()->id)
                ->where('status', 'completed')
                ->findOrFail($request->order_id);

            if ($order->rating) {
                return $this->errorResponse(__('messages.rating.already_rated'), 422);
            }
        }

        if ($request->delivery_id) {
            $delivery = Delivery::where('user_id', $request->user()->id)
                ->where('status', 'completed')
                ->findOrFail($request->delivery_id);

            if ($delivery->rating) {
                return $this->errorResponse(__('messages.rating.already_rated'), 422);
            }
        }

        $rating = Rating::create([
            'user_id' => $request->user()->id,
            'order_id' => $request->order_id,
            'delivery_id' => $request->delivery_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return $this->successResponse($rating->load('order', 'delivery'), __('messages.rating.created'));
    }

    public function getMyRatings(Request $request)
    {
        $ratings = Rating::where('user_id', $request->user()->id)
            ->with('order', 'delivery')
            ->latest()
            ->get();

        return $this->successResponse($ratings, __('messages.rating.ratings_loaded'));
    }
}


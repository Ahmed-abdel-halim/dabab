<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Rental;
use App\Models\Delivery;
use App\Models\CarWash;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ReorderController extends Controller
{
    use ApiResponseTrait;

    /**
     * إعادة طلب من أي خدمة
     * POST /v1/reorder/{type}/{id}
     * type من الـ URL: order|rental|delivery|car_wash
     */
    public function reorder(Request $request, $type, $id)
    {
        $type = strtolower($type);

        return match ($type) {
            'order' => $this->reorderOrder($request, $id),
            'rental' => $this->reorderRental($request, $id),
            'delivery' => $this->reorderDelivery($request, $id),
            'car_wash', 'car-wash' => $this->reorderCarWash($request, $id),
            default => $this->errorResponse(__('messages.reorder.invalid_type'), 400),
        };
    }

    /**
     * إعادة طلب (Orders)
     */
    private function reorderOrder(Request $request, $id)
    {
        $oldOrder = Order::where('user_id', $request->user()->id)
            ->with('items.category')
            ->findOrFail($id);

        if ($oldOrder->status === 'pending' || $oldOrder->status === 'cancelled') {
            return $this->errorResponse(__('messages.order.cannot_reorder_pending_cancelled'), 400);
        }

        $request->validate([
            'scheduled_at' => 'nullable|date',
            'location_id' => 'nullable|exists:user_locations,id',
            'payment_method' => 'nullable|in:cash,apple_pay,bank_card',
        ]);

        $newOrder = Order::create([
            'user_id' => $request->user()->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'scheduled_at' => $request->scheduled_at ? now()->parse($request->scheduled_at) : null,
            'location_id' => $request->location_id ?? $oldOrder->location_id,
            'payment_method' => $request->payment_method ?? $oldOrder->payment_method,
            'status' => 'pending',
            'delivery_cost' => 0,
            'total_cost' => 0,
        ]);

        $totalCost = 0;
        $deliveryCost = 0;

        foreach ($oldOrder->items as $index => $oldItem) {
            OrderItem::create([
                'order_id' => $newOrder->id,
                'category_id' => $oldItem->category_id,
                'details' => $oldItem->details,
                'delivery_cost' => $oldItem->delivery_cost,
                'order_index' => $index,
            ]);

            $totalCost += $oldItem->delivery_cost;
            $deliveryCost += $oldItem->delivery_cost;
        }

        $newOrder->update([
            'total_cost' => $totalCost,
            'delivery_cost' => $deliveryCost,
        ]);

        return $this->successResponse(
            [
                'type' => 'order',
                'data' => $newOrder->load('location', 'items.category')
            ],
            __('messages.order.reordered')
        );
    }

    /**
     * إعادة طلب (Rental)
     */
    private function reorderRental(Request $request, $id)
    {
        $oldRental = Rental::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($oldRental->status === 'pending' || $oldRental->status === 'cancelled') {
            return $this->errorResponse(__('messages.rental.cannot_reorder_pending_cancelled'), 400);
        }

        $request->validate([
            'personal_name' => 'nullable|string|max:255',
            'commercial_name' => 'nullable|string|max:255',
            'store_type' => 'nullable|string|max:255',
            'rental_type' => 'nullable|in:scooter_only,scooter_with_driver',
            'commercial_registration_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'additional_details' => 'nullable|string',
        ]);

        try {
            $filePath = null;
            if ($request->hasFile('commercial_registration_file')) {
                $file = $request->file('commercial_registration_file');
                $filePath = $file->store('rentals', 'public');
                
                if (!$filePath) {
                    return $this->errorResponse(__('messages.rental.file_upload_failed'), 500);
                }
            }

            $newRental = Rental::create([
                'user_id' => $request->user()->id,
                'personal_name' => $request->personal_name ?? $oldRental->personal_name,
                'commercial_name' => $request->commercial_name ?? $oldRental->commercial_name,
                'store_type' => $request->store_type ?? $oldRental->store_type,
                'rental_type' => $request->rental_type ?? $oldRental->rental_type,
                'commercial_registration_file' => $filePath ?? $oldRental->commercial_registration_file,
                'additional_details' => $request->additional_details ?? $oldRental->additional_details,
                'status' => 'pending',
            ]);

            return $this->successResponse(
                [
                    'type' => 'rental',
                    'data' => $newRental
                ],
                __('messages.rental.reordered')
            );
        } catch (\Exception $e) {
            if (isset($filePath) && $filePath) {
                Storage::disk('public')->delete($filePath);
            }
            return $this->errorResponse(__('messages.rental.reorder_error') . ': ' . $e->getMessage(), 500);
        }
    }

    /**
     * إعادة طلب (Delivery)
     */
    private function reorderDelivery(Request $request, $id)
    {
        $oldDelivery = Delivery::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($oldDelivery->status === 'pending' || $oldDelivery->status === 'cancelled') {
            return $this->errorResponse(__('messages.delivery.cannot_reorder_pending_cancelled'), 400);
        }

        $request->validate([
            'shipment_details' => 'nullable|string',
            'sender_address' => 'nullable|string',
            'sender_lat' => 'nullable|numeric',
            'sender_lng' => 'nullable|numeric',
            'sender_phone' => 'nullable|string',
            'recipient_address' => 'nullable|string',
            'recipient_lat' => 'nullable|numeric',
            'recipient_lng' => 'nullable|numeric',
            'recipient_phone' => 'nullable|string',
            'payment_method' => 'nullable|in:cash,apple_pay,bank_card',
        ]);

        $deliveryCost = $this->calculateDeliveryCost(
            $request->sender_lat ?? $oldDelivery->sender_lat,
            $request->sender_lng ?? $oldDelivery->sender_lng,
            $request->recipient_lat ?? $oldDelivery->recipient_lat,
            $request->recipient_lng ?? $oldDelivery->recipient_lng
        );

        $newDelivery = Delivery::create([
            'user_id' => $request->user()->id,
            'order_number' => 'DEL-' . strtoupper(Str::random(10)),
            'shipment_details' => $request->shipment_details ?? $oldDelivery->shipment_details,
            'sender_address' => $request->sender_address ?? $oldDelivery->sender_address,
            'sender_lat' => $request->sender_lat ?? $oldDelivery->sender_lat,
            'sender_lng' => $request->sender_lng ?? $oldDelivery->sender_lng,
            'sender_phone' => $request->sender_phone ?? $oldDelivery->sender_phone,
            'recipient_address' => $request->recipient_address ?? $oldDelivery->recipient_address,
            'recipient_lat' => $request->recipient_lat ?? $oldDelivery->recipient_lat,
            'recipient_lng' => $request->recipient_lng ?? $oldDelivery->recipient_lng,
            'recipient_phone' => $request->recipient_phone ?? $oldDelivery->recipient_phone,
            'delivery_cost' => $deliveryCost,
            'payment_method' => $request->payment_method ?? $oldDelivery->payment_method,
            'status' => 'pending',
        ]);

        return $this->successResponse(
            [
                'type' => 'delivery',
                'data' => $newDelivery
            ],
            __('messages.delivery.reordered')
        );
    }

    /**
     * إعادة طلب (Car Wash)
     */
    private function reorderCarWash(Request $request, $id)
    {
        $locale = $request->query('lang', app()->getLocale());
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $oldCarWash = CarWash::where('user_id', $request->user()->id)
            ->with('location')
            ->findOrFail($id);

        if ($oldCarWash->status === 'pending' || $oldCarWash->status === 'cancelled') {
            return $this->errorResponse(__('messages.car_wash.cannot_reorder_pending_cancelled'), 400);
        }

        $request->validate([
            'car_size' => 'nullable|in:small,large',
            'wash_type' => 'nullable|in:interior_exterior,exterior,interior',
            'scheduled_date' => 'nullable|date|after_or_equal:today',
            'scheduled_time' => 'nullable|date_format:H:i',
            'time_period' => 'nullable|in:before_lunch,early_evening,dinner_time,late_night',
            'location_id' => 'nullable|exists:user_locations,id',
        ]);

        $carSize = $request->car_size ?? $oldCarWash->car_size;
        $washType = $request->wash_type ?? $oldCarWash->wash_type;
        $cost = $this->calculateCarWashCost($carSize, $washType);

        $newCarWash = CarWash::create([
            'user_id' => $request->user()->id,
            'car_size' => $carSize,
            'wash_type' => $washType,
            'scheduled_date' => $request->scheduled_date ?? $oldCarWash->scheduled_date,
            'scheduled_time' => $request->scheduled_time ?? $oldCarWash->scheduled_time,
            'time_period' => $request->time_period ?? $oldCarWash->time_period,
            'location_id' => $request->location_id ?? $oldCarWash->location_id,
            'cost' => $cost,
            'status' => 'pending',
        ]);

        return $this->successResponse(
            [
                'type' => 'car_wash',
                'data' => $newCarWash->load('location')
            ],
            __('messages.car_wash.reordered')
        );
    }

    /**
     * حساب تكلفة التوصيل بناءً على المسافة
     */
    private function calculateDeliveryCost($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return max(5, round(5 + ($distance * 2), 2));
    }

    /**
     * حساب تكلفة غسيل السيارة
     */
    private function calculateCarWashCost($carSize, $washType)
    {
        $baseCosts = [
            'small' => [
                'exterior' => 30,
                'interior' => 40,
                'interior_exterior' => 60,
            ],
            'large' => [
                'exterior' => 50,
                'interior' => 60,
                'interior_exterior' => 100,
            ],
        ];

        return $baseCosts[$carSize][$washType] ?? 50;
    }
}

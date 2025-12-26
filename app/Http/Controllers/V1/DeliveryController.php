<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeliveryController extends Controller
{
    use ApiResponseTrait;

    public function createDelivery(Request $request)
    {
        $request->validate([
            'shipment_details' => 'required|string',
            'sender_address' => 'required|string',
            'sender_lat' => 'required|numeric',
            'sender_lng' => 'required|numeric',
            'sender_phone' => 'required|string',
            'recipient_address' => 'required|string',
            'recipient_lat' => 'required|numeric',
            'recipient_lng' => 'required|numeric',
            'recipient_phone' => 'required|string',
            'payment_method' => 'nullable|in:cash,apple_pay,bank_card',
        ]);

        // Calculate delivery cost based on distance (simplified)
        $deliveryCost = $this->calculateDeliveryCost(
            $request->sender_lat,
            $request->sender_lng,
            $request->recipient_lat,
            $request->recipient_lng
        );

        $delivery = Delivery::create([
            'user_id' => $request->user()->id,
            'order_number' => 'DEL-' . strtoupper(Str::random(10)),
            'shipment_details' => $request->shipment_details,
            'sender_address' => $request->sender_address,
            'sender_lat' => $request->sender_lat,
            'sender_lng' => $request->sender_lng,
            'sender_phone' => $request->sender_phone,
            'recipient_address' => $request->recipient_address,
            'recipient_lat' => $request->recipient_lat,
            'recipient_lng' => $request->recipient_lng,
            'recipient_phone' => $request->recipient_phone,
            'delivery_cost' => $deliveryCost,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
        ]);

        return $this->successResponse($delivery, 'تم إنشاء طلب التوصيل بنجاح');
    }

    public function getMyDeliveries(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Delivery::where('user_id', $request->user()->id)
            ->with('rating');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $deliveries = $query->latest()->get();

        return $this->successResponse($deliveries, 'تم جلب طلبات التوصيل');
    }

    public function getDelivery(Request $request, $id)
    {
        $delivery = Delivery::where('user_id', $request->user()->id)
            ->with('rating')
            ->findOrFail($id);

        return $this->successResponse($delivery, 'تم جلب طلب التوصيل');
    }

    public function updateDelivery(Request $request, $id)
    {
        $delivery = Delivery::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

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

        $updateData = $request->only([
            'shipment_details', 'sender_address', 'sender_lat', 'sender_lng',
            'sender_phone', 'recipient_address', 'recipient_lat', 'recipient_lng',
            'recipient_phone', 'payment_method'
        ]);

        if (isset($request->sender_lat) && isset($request->sender_lng) &&
            isset($request->recipient_lat) && isset($request->recipient_lng)) {
            $updateData['delivery_cost'] = $this->calculateDeliveryCost(
                $request->sender_lat,
                $request->sender_lng,
                $request->recipient_lat,
                $request->recipient_lng
            );
        }

        $delivery->update($updateData);

        return $this->successResponse($delivery, 'تم تحديث طلب التوصيل');
    }

    public function cancelDelivery(Request $request, $id)
    {
        $delivery = Delivery::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->findOrFail($id);

        $delivery->update(['status' => 'cancelled']);

        return $this->successResponse($delivery, 'تم إلغاء طلب التوصيل');
    }

    public function trackDelivery(Request $request, $id)
    {
        $delivery = Delivery::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $tracking = [
            'delivery' => $delivery,
            'status' => $delivery->status,
            'estimated_time' => $this->getEstimatedTime($delivery),
            'last_update' => $delivery->updated_at->format('Y-m-d H:i'),
        ];

        return $this->successResponse($tracking, 'معلومات تتبع التوصيل');
    }

    private function calculateDeliveryCost($lat1, $lng1, $lat2, $lng2)
    {
        // Simplified distance calculation (Haversine formula)
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        // Base cost 5 SAR + 2 SAR per km
        return max(5, round(5 + ($distance * 2), 2));
    }

    private function getEstimatedTime($delivery)
    {
        if ($delivery->status === 'pending') {
            return '30 - 45 دقيقة';
        } elseif ($delivery->status === 'in_progress') {
            return '15 - 30 دقيقة';
        }
        return null;
    }
}


<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\Rental;
use App\Models\CarWash;
use App\Models\OrderItem;
use App\Models\OrderCategory;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AllOrdersController extends Controller
{
    use ApiResponseTrait;

    public function getAllOrders(Request $request)
    {
        $status = $request->query('status', 'all'); // all, pending, completed, cancelled
        $userId = $request->user()->id;

        $allOrders = [];

        // 1. Orders
        $ordersQuery = Order::where('user_id', $userId)
            ->with(['location', 'items.category']);
        
        if ($status !== 'all') {
            $mappedStatus = $this->mapStatusForOrders($status);
            if ($status === 'pending') {
                $ordersQuery->whereIn('status', ['pending', 'confirmed', 'in_progress']);
            } else {
                $ordersQuery->where('status', $mappedStatus);
            }
        }
        
        $orders = $ordersQuery->latest()->get();
        foreach ($orders as $order) {
            $allOrders[] = [
                'id' => $order->id,
                'type' => 'order',
                'type_name' => __('messages.order.type_order'),
                'order_number' => $order->order_number,
                'status' => $this->normalizeStatus($order->status, 'order'),
                'status_display' => __('messages.order.status_' . $order->status),
                'total_cost' => (float) $order->total_cost,
                'delivery_cost' => (float) $order->delivery_cost,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'scheduled_at' => $order->scheduled_at?->format('Y-m-d H:i:s'),
                'location' => $order->location,
                'items' => $order->items,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $order->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        // 2. Deliveries
        $deliveriesQuery = Delivery::where('user_id', $userId);
        
        if ($status !== 'all') {
            $mappedStatus = $this->mapStatusForDeliveries($status);
            if ($status === 'pending') {
                $deliveriesQuery->whereIn('status', ['pending', 'in_progress']);
            } else {
                $deliveriesQuery->where('status', $mappedStatus);
            }
        }
        
        $deliveries = $deliveriesQuery->latest()->get();
        foreach ($deliveries as $delivery) {
            $allOrders[] = [
                'id' => $delivery->id,
                'type' => 'delivery',
                'type_name' => __('messages.order.type_delivery'),
                'order_number' => $delivery->order_number,
                'status' => $this->normalizeStatus($delivery->status, 'delivery'),
                'status_display' => __('messages.order.status_' . $delivery->status),
                'shipment_details' => $delivery->shipment_details,
                'sender_address' => $delivery->sender_address,
                'recipient_address' => $delivery->recipient_address,
                'delivery_cost' => (float) $delivery->delivery_cost,
                'payment_method' => $delivery->payment_method,
                'payment_status' => $delivery->payment_status,
                'created_at' => $delivery->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $delivery->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        // 3. Rentals
        $rentalsQuery = Rental::where('user_id', $userId);
        
        if ($status !== 'all') {
            $rentalsQuery->where('status', $this->mapStatusForRentals($status));
        }
        
        $rentals = $rentalsQuery->latest()->get();
        foreach ($rentals as $rental) {
            $allOrders[] = [
                'id' => $rental->id,
                'type' => 'rental',
                'type_name' => __('messages.order.type_rental'),
                'order_number' => null,
                'status' => $this->normalizeStatus($rental->status, 'rental'),
                'status_display' => __('messages.order.status_' . $rental->status),
                'personal_name' => $rental->personal_name,
                'commercial_name' => $rental->commercial_name,
                'store_type' => $rental->store_type,
                'rental_type' => $rental->rental_type,
                'commercial_registration_file' => $rental->commercial_registration_file,
                'additional_details' => $rental->additional_details,
                'created_at' => $rental->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $rental->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        // 4. Car Washes
        $carWashesQuery = CarWash::where('user_id', $userId)
            ->with('location');
        
        if ($status !== 'all') {
            $mappedStatus = $this->mapStatusForCarWashes($status);
            if ($status === 'pending') {
                $carWashesQuery->whereIn('status', ['pending', 'confirmed']);
            } else {
                $carWashesQuery->where('status', $mappedStatus);
            }
        }
        
        $carWashes = $carWashesQuery->latest()->get();
        foreach ($carWashes as $carWash) {
            $allOrders[] = [
                'id' => $carWash->id,
                'type' => 'car_wash',
                'type_name' => __('messages.order.type_car_wash'),
                'order_number' => null,
                'status' => $this->normalizeStatus($carWash->status, 'car_wash'),
                'status_display' => __('messages.order.status_' . $carWash->status),
                'car_size' => $carWash->car_size,
                'wash_type' => $carWash->wash_type,
                'scheduled_date' => $carWash->scheduled_date_formatted,
                'scheduled_time' => $carWash->scheduled_time_formatted,
                'time_period' => $carWash->time_period,
                'cost' => (float) $carWash->cost,
                'payment_method' => $carWash->payment_method,
                'payment_status' => $carWash->payment_status,
                'location' => $carWash->location,
                'created_at' => $carWash->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $carWash->updated_at->format('Y-m-d H:i:s'),
            ];
        }

        // ترتيب حسب التاريخ
        usort($allOrders, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $this->successResponse($allOrders, __('messages.order.all_orders_loaded'));
    }

    private function mapStatusForRentals($status)
    {
        $mapping = [
            'pending' => 'pending',
            'completed' => 'approved',
            'cancelled' => 'rejected',
        ];
        return $mapping[$status] ?? $status;
    }

    private function mapStatusForOrders($status)
    {
        if ($status === 'pending') return 'pending';
        if ($status === 'completed') return 'completed';
        if ($status === 'cancelled') return 'cancelled';
        return $status;
    }

    private function mapStatusForDeliveries($status)
    {
        if ($status === 'pending') return 'pending';
        if ($status === 'completed') return 'completed';
        if ($status === 'cancelled') return 'cancelled';
        return $status;
    }

    private function mapStatusForCarWashes($status)
    {
        if ($status === 'pending') return 'pending';
        if ($status === 'completed') return 'completed';
        if ($status === 'cancelled') return 'cancelled';
        return $status;
    }

    private function normalizeStatus($status, $type)
    {
        if ($type === 'rental') {
            $mapping = [
                'pending' => 'pending',
                'approved' => 'completed',
                'rejected' => 'cancelled',
            ];
            return $mapping[$status] ?? $status;
        }
        
        if (in_array($status, ['pending', 'confirmed', 'in_progress'])) {
            return 'pending';
        } elseif ($status === 'completed') {
            return 'completed';
        } elseif ($status === 'cancelled') {
            return 'cancelled';
        }
        
        return $status;
    }

    public function updateService(Request $request, $type, $id)
    {
        $userId = $request->user()->id;

        switch ($type) {
            case 'order':
                return $this->updateOrder($request, $id, $userId);
            case 'delivery':
                return $this->updateDelivery($request, $id, $userId);
            case 'rental':
                return $this->updateRental($request, $id, $userId);
            case 'car_wash':
                return $this->updateCarWash($request, $id, $userId);
            default:
                return $this->errorResponse(__('messages.invalid_service_type'), 400);
        }
    }

    public function deleteService(Request $request, $type, $id)
    {
        $userId = $request->user()->id;

        switch ($type) {
            case 'order':
                return $this->deleteOrder($request, $id, $userId);
            case 'delivery':
                return $this->deleteDelivery($request, $id, $userId);
            case 'rental':
                return $this->deleteRental($request, $id, $userId);
            case 'car_wash':
                return $this->deleteCarWash($request, $id, $userId);
            default:
                return $this->errorResponse(__('messages.invalid_service_type'), 400);
        }
    }

    private function updateOrder(Request $request, $id, $userId)
    {
        $order = Order::where('user_id', $userId)
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'items' => 'nullable|array|min:1',
            'items.*.category_id' => 'required_with:items|exists:order_categories,id',
            'items.*.details' => 'required_with:items|string',
            'scheduled_at' => 'nullable|date',
            'location_id' => 'nullable|exists:user_locations,id',
            'payment_method' => 'nullable|in:cash,apple_pay,bank_card',
        ]);

        $updateData = [];
        if ($request->has('scheduled_at')) {
            $updateData['scheduled_at'] = now()->parse($request->scheduled_at);
        }
        if ($request->has('location_id')) {
            $updateData['location_id'] = $request->location_id;
        }
        if ($request->has('payment_method')) {
            $updateData['payment_method'] = $request->payment_method;
        }

        if ($request->has('items')) {
            $order->items()->delete();
            
            $totalCost = 0;
            $deliveryCost = 0;

            foreach ($request->items as $index => $item) {
                $category = OrderCategory::findOrFail($item['category_id']);
                $itemDeliveryCost = $category->fixed_price ?? 5;

                OrderItem::create([
                    'order_id' => $order->id,
                    'category_id' => $item['category_id'],
                    'details' => $item['details'],
                    'delivery_cost' => $itemDeliveryCost,
                    'order_index' => $index,
                ]);

                $totalCost += $itemDeliveryCost;
                $deliveryCost += $itemDeliveryCost;
            }

            $updateData['total_cost'] = $totalCost;
            $updateData['delivery_cost'] = $deliveryCost;
        }

        $order->update($updateData);

        return $this->successResponse($order->load('location', 'items.category'), __('messages.order.updated'));
    }

    private function updateDelivery(Request $request, $id, $userId)
    {
        $delivery = Delivery::where('user_id', $userId)
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

        return $this->successResponse($delivery, __('messages.delivery.updated'));
    }

    private function updateRental(Request $request, $id, $userId)
    {
        $rental = Rental::where('user_id', $userId)
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'personal_name' => 'nullable|string|max:255',
            'commercial_name' => 'nullable|string|max:255',
            'store_type' => 'nullable|string|max:255',
            'rental_type' => 'nullable|in:scooter_only,scooter_with_driver',
            'commercial_registration_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'additional_details' => 'nullable|string',
        ]);

        try {
            $updateData = $request->only([
                'personal_name', 'commercial_name', 'store_type', 'rental_type', 'additional_details'
            ]);

            if ($request->hasFile('commercial_registration_file')) {
                $oldFilePath = $rental->getOriginalFilePath();
                if ($oldFilePath) {
                    Storage::disk('public')->delete($oldFilePath);
                }
                
                $file = $request->file('commercial_registration_file');
                $filePath = $file->store('rentals', 'public');
                
                if (!$filePath) {
                    return $this->errorResponse(__('messages.rental.file_upload_failed'), 500);
                }
                
                $updateData['commercial_registration_file'] = $filePath;
            }

            $rental->update($updateData);

            return $this->successResponse($rental, __('messages.rental.updated'));
        } catch (\Exception $e) {
            return $this->errorResponse(__('messages.rental.update_error') . ': ' . $e->getMessage(), 500);
        }
    }

    private function updateCarWash(Request $request, $id, $userId)
    {
        $carWash = CarWash::where('user_id', $userId)
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'car_size' => 'nullable|in:small,large',
            'wash_type' => 'nullable|in:interior_exterior,exterior,interior',
            'scheduled_date' => 'nullable|date|after_or_equal:today',
            'scheduled_time' => 'nullable|date_format:H:i',
            'time_period' => 'nullable|in:before_lunch,early_evening,dinner_time,late_night',
            'location_id' => 'nullable|exists:user_locations,id',
        ]);

        $updateData = $request->only([
            'car_size', 'wash_type', 'scheduled_date', 'scheduled_time',
            'time_period', 'location_id'
        ]);

        if ($request->has('car_size') || $request->has('wash_type')) {
            $carSize = $request->car_size ?? $carWash->car_size;
            $washType = $request->wash_type ?? $carWash->wash_type;
            $updateData['cost'] = $this->calculateCarWashCost($carSize, $washType);
        }

        $carWash->update($updateData);

        return $this->successResponse($carWash->load('location'), __('messages.car_wash.updated'));
    }

    private function deleteOrder(Request $request, $id, $userId)
    {
        $order = Order::where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->findOrFail($id);

        $order->update(['status' => 'cancelled']);

        return $this->successResponse($order, __('messages.order.cancelled'));
    }

    private function deleteDelivery(Request $request, $id, $userId)
    {
        $delivery = Delivery::where('user_id', $userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->findOrFail($id);

        $delivery->update(['status' => 'cancelled']);

        return $this->successResponse($delivery, __('messages.delivery.cancelled'));
    }

    private function deleteRental(Request $request, $id, $userId)
    {
        $rental = Rental::where('user_id', $userId)
            ->where('status', 'pending')
            ->findOrFail($id);

        $rental->update(['status' => 'rejected']);

        return $this->successResponse($rental, __('messages.rental.cancelled'));
    }

    private function deleteCarWash(Request $request, $id, $userId)
    {
        $carWash = CarWash::where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->findOrFail($id);

        $carWash->update(['status' => 'cancelled']);

        return $this->successResponse($carWash, __('messages.car_wash.cancelled'));
    }

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

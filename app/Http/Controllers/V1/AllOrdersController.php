<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\Rental;
use App\Models\CarWash;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AllOrdersController extends Controller
{
    use ApiResponseTrait;

    public function getAllOrders(Request $request)
    {
        // التأكد من تعيين اللغة من query parameter
        $locale = $request->query('lang', app()->getLocale());
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        $status = $request->query('status', 'all'); // all, pending, completed, cancelled
        $userId = $request->user()->id;

        $allOrders = [];

        // 1. Orders (الطلبات)
        $ordersQuery = Order::where('user_id', $userId)
            ->with(['location', 'items.category']);
        
        if ($status !== 'all') {
            $mappedStatus = $this->mapStatusForOrders($status);
            if ($status === 'pending') {
                // pending يشمل: pending, confirmed, in_progress
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
                'type_name' => $locale === 'ar' ? 'طلب' : 'Order',
                'order_number' => $order->order_number,
                'status' => $this->normalizeStatus($order->status, 'order'),
                'status_display' => $this->getStatusDisplay($order->status, 'order', $locale),
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

        // 2. Deliveries (التوصيل)
        $deliveriesQuery = Delivery::where('user_id', $userId);
        
        if ($status !== 'all') {
            $mappedStatus = $this->mapStatusForDeliveries($status);
            if ($status === 'pending') {
                // pending يشمل: pending, in_progress
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
                'type_name' => $locale === 'ar' ? 'توصيل' : 'Delivery',
                'order_number' => $delivery->order_number,
                'status' => $this->normalizeStatus($delivery->status, 'delivery'),
                'status_display' => $this->getStatusDisplay($delivery->status, 'delivery', $locale),
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

        // 3. Rentals (الاستئجار)
        $rentalsQuery = Rental::where('user_id', $userId);
        
        if ($status !== 'all') {
            $rentalsQuery->where('status', $this->mapStatusForRentals($status));
        }
        
        $rentals = $rentalsQuery->latest()->get();
        foreach ($rentals as $rental) {
            $allOrders[] = [
                'id' => $rental->id,
                'type' => 'rental',
                'type_name' => $locale === 'ar' ? 'استئجار' : 'Rental',
                'order_number' => null,
                'status' => $this->normalizeStatus($rental->status, 'rental'),
                'status_display' => $this->getStatusDisplay($rental->status, 'rental', $locale),
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

        // 4. Car Washes (غسيل السيارات)
        $carWashesQuery = CarWash::where('user_id', $userId)
            ->with('location');
        
        if ($status !== 'all') {
            $mappedStatus = $this->mapStatusForCarWashes($status);
            if ($status === 'pending') {
                // pending يشمل: pending, confirmed
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
                'type_name' => $locale === 'ar' ? 'غسيل سيارات' : 'Car Wash',
                'order_number' => null,
                'status' => $this->normalizeStatus($carWash->status, 'car_wash'),
                'status_display' => $this->getStatusDisplay($carWash->status, 'car_wash', $locale),
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

        // ترتيب حسب التاريخ (الأحدث أولاً)
        usort($allOrders, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $this->successResponse($allOrders, __('messages.order.all_orders_loaded'));
    }

    /**
     * تحويل status من Rental إلى status موحد
     */
    private function mapStatusForRentals($status)
    {
        $mapping = [
            'pending' => 'pending',
            'completed' => 'approved',
            'cancelled' => 'rejected',
        ];
        return $mapping[$status] ?? $status;
    }

    /**
     * تحويل status من Order إلى status موحد
     */
    private function mapStatusForOrders($status)
    {
        // Orders: pending, confirmed, in_progress, completed, cancelled
        if ($status === 'pending') {
            return 'pending';
        } elseif ($status === 'completed') {
            return 'completed';
        } elseif ($status === 'cancelled') {
            return 'cancelled';
        }
        return $status;
    }

    /**
     * تحويل status من Delivery إلى status موحد
     */
    private function mapStatusForDeliveries($status)
    {
        // Deliveries: pending, in_progress, completed, cancelled
        if ($status === 'pending') {
            return 'pending';
        } elseif ($status === 'completed') {
            return 'completed';
        } elseif ($status === 'cancelled') {
            return 'cancelled';
        }
        return $status;
    }

    /**
     * تحويل status من CarWash إلى status موحد
     */
    private function mapStatusForCarWashes($status)
    {
        // CarWashes: pending, confirmed, completed, cancelled
        if ($status === 'pending') {
            return 'pending';
        } elseif ($status === 'completed') {
            return 'completed';
        } elseif ($status === 'cancelled') {
            return 'cancelled';
        }
        return $status;
    }

    /**
     * توحيد status لجميع الخدمات
     */
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
        
        // للأنواع الأخرى: pending, completed, cancelled
        if (in_array($status, ['pending', 'confirmed', 'in_progress'])) {
            return 'pending';
        } elseif ($status === 'completed') {
            return 'completed';
        } elseif ($status === 'cancelled') {
            return 'cancelled';
        }
        
        return $status;
    }

    /**
     * الحصول على نص status بالعربية أو الإنجليزية
     */
    private function getStatusDisplay($status, $type, $locale)
    {
        if ($type === 'rental') {
            $statuses = [
                'pending' => ['ar' => 'قيد الانتظار', 'en' => 'Pending'],
                'approved' => ['ar' => 'مكتمل', 'en' => 'Completed'],
                'rejected' => ['ar' => 'ملغي', 'en' => 'Cancelled'],
            ];
        } else {
            $statuses = [
                'pending' => ['ar' => 'قيد الانتظار', 'en' => 'Pending'],
                'confirmed' => ['ar' => 'مؤكد', 'en' => 'Confirmed'],
                'in_progress' => ['ar' => 'قيد التنفيذ', 'en' => 'In Progress'],
                'completed' => ['ar' => 'مكتمل', 'en' => 'Completed'],
                'cancelled' => ['ar' => 'ملغي', 'en' => 'Cancelled'],
            ];
        }
        
        return $statuses[$status][$locale] ?? $status;
    }
}


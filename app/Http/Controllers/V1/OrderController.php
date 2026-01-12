<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderCategory;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function getCategories()
    {
        $categories = OrderCategory::where('is_active', true)->get();
        return $this->successResponse($categories, __('messages.order.categories_loaded'));
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'required|exists:order_categories,id',
            'items.*.details' => 'required|string',
            'scheduled_at' => 'nullable|date',
            'location_id' => 'nullable|exists:user_locations,id',
            'payment_method' => 'nullable|in:cash,apple_pay,bank_card',
        ]);

        // حساب التكلفة الإجمالية
        $totalCost = 0;
        $deliveryCost = 0;

        // إنشاء الطلب الرئيسي
        $order = Order::create([
            'user_id' => $request->user()->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'scheduled_at' => $request->scheduled_at ? now()->parse($request->scheduled_at) : null,
            'location_id' => $request->location_id,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'delivery_cost' => 0,
            'total_cost' => 0,
        ]);

        // إنشاء الطلبات الفرعية
        foreach ($request->items as $index => $item) {
            $category = OrderCategory::findOrFail($item['category_id']);
            // حساب delivery_cost تلقائياً من fixed_price للفئة
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

        // تحديث التكلفة الإجمالية للطلب
        $order->update([
            'total_cost' => $totalCost,
            'delivery_cost' => $deliveryCost,
        ]);

        return $this->successResponse($order->load('location', 'items.category'), __('messages.order.created'));
    }

    public function getMyOrders(Request $request)
    {
        $status = $request->query('status', 'all'); // all, pending, confirmed, in_progress, completed, cancelled

        $query = Order::where('user_id', $request->user()->id)
            ->with('location', 'rating', 'items.category');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->latest()->get();

        return $this->successResponse($orders, __('messages.order.orders_loaded'));
    }

    public function getOrder(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with('location', 'rating', 'items.category')
            ->findOrFail($id);

        return $this->successResponse($order, __('messages.order.loaded'));
    }

    public function cancelOrder(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->findOrFail($id);

        $order->update(['status' => 'cancelled']);

        return $this->successResponse($order, __('messages.order.cancelled'));
    }

    public function trackOrder(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with('location')
            ->findOrFail($id);

        $tracking = [
            'order' => $order,
            'status' => $order->status,
            'estimated_time' => $this->getEstimatedTime($order),
            'last_update' => $order->updated_at->format('Y-m-d H:i'),
        ];

        return $this->successResponse($tracking, __('messages.order.tracking_info'));
    }

    private function getEstimatedTime($order)
    {
        if ($order->status === 'pending') {
            return __('messages.order.estimated_time_pending');
        } elseif ($order->status === 'confirmed') {
            return __('messages.order.estimated_time_confirmed');
        } elseif ($order->status === 'in_progress') {
            return __('messages.order.estimated_time_in_progress');
        }
        return null;
    }

    public function confirmOrder(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,apple_pay,bank_card',
        ]);

        $order = Order::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $order->update([
            'payment_method' => $request->payment_method,
            'status' => 'confirmed',
            'payment_status' => $request->payment_method === 'cash' ? 'pending' : 'paid',
        ]);

        return $this->successResponse($order->load('location', 'items.category'), __('messages.order.confirmed'));
    }

    /**
     * إضافة طلب فرعي لطلب موجود
     */
    public function addOrderItem(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:order_categories,id',
            'details' => 'required|string',
        ]);

        $category = OrderCategory::findOrFail($request->category_id);
        // حساب delivery_cost تلقائياً من fixed_price للفئة
        $itemDeliveryCost = $category->fixed_price ?? 5;

        // الحصول على آخر order_index
        $lastIndex = OrderItem::where('order_id', $order->id)->max('order_index') ?? -1;

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'category_id' => $request->category_id,
            'details' => $request->details,
            'delivery_cost' => $itemDeliveryCost,
            'order_index' => $lastIndex + 1,
        ]);

        // تحديث التكلفة الإجمالية للطلب
        $order->increment('total_cost', $itemDeliveryCost);
        $order->increment('delivery_cost', $itemDeliveryCost);

        return $this->successResponse($orderItem->load('category'), __('messages.order.item_added'));
    }

    /**
     * تحديث طلب فرعي
     */
    public function updateOrderItem(Request $request, $orderId, $itemId)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($orderId);

        $orderItem = OrderItem::where('order_id', $order->id)
            ->findOrFail($itemId);

        $request->validate([
            'category_id' => 'nullable|exists:order_categories,id',
            'details' => 'nullable|string',
        ]);

        $oldCost = $orderItem->delivery_cost;
        $newCost = $oldCost;

        // إذا تم تغيير الفئة، احسب delivery_cost من fixed_price الجديدة
        if ($request->category_id && $request->category_id != $orderItem->category_id) {
            $category = OrderCategory::findOrFail($request->category_id);
            $newCost = $category->fixed_price ?? 5;
        }

        $categoryId = $request->category_id ?? $orderItem->category_id;
        
        $orderItem->update([
            'category_id' => $categoryId,
            'details' => $request->details ?? $orderItem->details,
            'delivery_cost' => $newCost,
        ]);

        // تحديث التكلفة الإجمالية للطلب
        $costDifference = $newCost - $oldCost;
        $order->increment('total_cost', $costDifference);
        $order->increment('delivery_cost', $costDifference);

        return $this->successResponse($orderItem->load('category'), __('messages.order.item_updated'));
    }

    /**
     * حذف طلب فرعي
     */
    public function deleteOrderItem(Request $request, $orderId, $itemId)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($orderId);

        $orderItem = OrderItem::where('order_id', $order->id)
            ->findOrFail($itemId);

        $itemCost = $orderItem->delivery_cost;

        $orderItem->delete();

        // تحديث التكلفة الإجمالية للطلب
        $order->decrement('total_cost', $itemCost);
        $order->decrement('delivery_cost', $itemCost);

        return $this->successResponse(null, __('messages.order.item_deleted'));
    }
}


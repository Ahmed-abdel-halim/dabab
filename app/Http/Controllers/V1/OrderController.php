<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
            'category_id' => 'nullable|exists:order_categories,id',
            'category_name' => 'nullable|string',
            'details' => 'required|string',
            'delivery_cost' => 'nullable|numeric|min:0',
            'scheduled_at' => 'nullable|date',
            'location_id' => 'nullable|exists:user_locations,id',
            'payment_method' => 'nullable|in:cash,apple_pay,bank_card',
        ]);

        $category = null;
        if ($request->category_id) {
            $category = OrderCategory::find($request->category_id);
        }

        $order = Order::create([
            'user_id' => $request->user()->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'category_id' => $request->category_id,
            'category_name' => $category ? $category->name_ar : $request->category_name,
            'details' => $request->details,
            'delivery_cost' => $request->delivery_cost ?? ($category ? $category->fixed_price : 5),
            'total_cost' => $request->delivery_cost ?? ($category ? $category->fixed_price : 5),
            'scheduled_at' => $request->scheduled_at ? now()->parse($request->scheduled_at) : null,
            'location_id' => $request->location_id,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
        ]);

        return $this->successResponse($order->load('location', 'category'), __('messages.order.created'));
    }

    public function getMyOrders(Request $request)
    {
        $status = $request->query('status', 'all'); // all, pending, confirmed, in_progress, completed, cancelled

        $query = Order::where('user_id', $request->user()->id)
            ->with('location', 'category', 'rating');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->latest()->get();

        return $this->successResponse($orders, __('messages.order.orders_loaded'));
    }

    public function getOrder(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with('location', 'category', 'rating')
            ->findOrFail($id);

        return $this->successResponse($order, __('messages.order.loaded'));
    }

    public function updateOrder(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'category_id' => 'nullable|exists:order_categories,id',
            'details' => 'nullable|string',
            'delivery_cost' => 'nullable|numeric|min:0',
            'scheduled_at' => 'nullable|date',
            'location_id' => 'nullable|exists:user_locations,id',
            'payment_method' => 'nullable|in:cash,apple_pay,bank_card',
        ]);

        $order->update($request->only([
            'category_id', 'details', 'delivery_cost', 'scheduled_at', 'location_id', 'payment_method'
        ]));

        if ($request->has('delivery_cost')) {
            $order->total_cost = $request->delivery_cost;
            $order->save();
        }

        return $this->successResponse($order->load('location', 'category'), __('messages.order.updated'));
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

        return $this->successResponse($order->load('location', 'category'), __('messages.order.confirmed'));
    }
}


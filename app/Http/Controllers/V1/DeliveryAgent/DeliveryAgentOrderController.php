<?php

namespace App\Http\Controllers\V1\DeliveryAgent;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\CarWash;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DeliveryAgentOrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get available tasks based on agent's service type
     */
    public function getAvailableTasks(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'delivery_agent') {
            return $this->errorResponse(__('messages.delivery_agent.not_delivery_agent'), 403);
        }

        $profile = $user->deliveryAgentProfile;
        if (!$profile) {
            return $this->errorResponse(__('messages.delivery_agent.profile_not_found'), 404);
        }

        $category = $profile->service_category;
        $requestedType = $request->query('type'); // order, delivery, car_wash
        $tasks = collect();

        if ($category === 'tawseel') {
            // Get orders if type is 'order' or null
            if (!$requestedType || $requestedType === 'order') {
                $orders = Order::whereIn('status', ['pending', 'confirmed'])
                    ->whereNull('delivery_agent_id')
                    ->with('location', 'items.category')
                    ->get()
                    ->map(function ($item) {
                        $item->task_type = 'order';
                        return $item;
                    });
                $tasks = $tasks->concat($orders);
            }

            // Get deliveries if type is 'delivery' or null
            if (!$requestedType || $requestedType === 'delivery') {
                // Deliveries are created as 'pending'
                $deliveries = Delivery::where('status', 'pending')
                    ->whereNull('delivery_agent_id')
                    ->get()
                    ->map(function ($item) {
                        $item->task_type = 'delivery';
                        return $item;
                    });
                $tasks = $tasks->concat($deliveries);
            }
        } elseif ($category === 'car_wash') {
            // Get car washes if type is null or 'car_wash'
            if (!$requestedType || $requestedType === 'car_wash') {
                // Car washes are created as 'pending'
                $carWashes = CarWash::where('status', 'pending')
                    ->whereNull('delivery_agent_id')
                    ->with('location')
                    ->get()
                    ->map(function ($item) {
                        $item->task_type = 'car_wash';
                        return $item;
                    });
                $tasks = $tasks->concat($carWashes);
            }
        }

        return $this->successResponse($tasks, __('messages.order.all_orders_loaded'));
    }

    /**
     * Accept a task
     */
    public function acceptTask(Request $request, $type, $id)
    {
        $user = auth()->user();
        $model = $this->getModelByType($type);

        if (!$model) {
            return $this->errorResponse(__('messages.reorder.invalid_type'), 422);
        }

        $validStatuses = ($type === 'order') ? ['pending', 'confirmed'] : ['pending'];

        $task = $model::where('id', $id)
            ->whereNull('delivery_agent_id')
            ->whereIn('status', $validStatuses)
            ->first();

        if (!$task) {
            return $this->errorResponse(__('messages.order.loaded'), 404); // Or already taken
        }

        $task->update([
            'delivery_agent_id' => $user->id,
            'status' => 'in_progress' // Auto-start or keep as confirmed? Usually accept means start soon. 
        ]);

        return $this->successResponse($task, __('messages.order.updated'));
    }

    /**
     * Get my active tasks
     */
    public function getMyActiveTasks(Request $request)
    {
        $user = auth()->user();
        $status = $request->query('status', 'in_progress'); // in_progress, completed

        $orders = Order::where('delivery_agent_id', $user->id)
            ->where('status', $status)
            ->with('location', 'items.category')
            ->get()->map(fn($o) => array_merge($o->toArray(), ['task_type' => 'order']));

        $deliveries = Delivery::where('delivery_agent_id', $user->id)
            ->where('status', $status)
            ->get()->map(fn($d) => array_merge($d->toArray(), ['task_type' => 'delivery']));

        $carWashes = CarWash::where('delivery_agent_id', $user->id)
            ->where('status', $status)
            ->with('location')
            ->get()->map(fn($cw) => array_merge($cw->toArray(), ['task_type' => 'car_wash']));

        $tasks = $orders->concat($deliveries)->concat($carWashes);

        return $this->successResponse($tasks, __('messages.order.orders_loaded'));
    }

    /**
     * Update task status (e.g., complete)
     */
    public function updateTaskStatus(Request $request, $type, $id)
    {
        $user = auth()->user();
        $request->validate(['status' => 'required|in:in_progress,completed,cancelled']);

        $model = $this->getModelByType($type);
        if (!$model)
            return $this->errorResponse(__('messages.reorder.invalid_type'), 422);

        $task = $model::where('id', $id)
            ->where('delivery_agent_id', $user->id)
            ->first();

        if (!$task)
            return $this->errorResponse(__('messages.order.loaded'), 404);

        $updateData = ['status' => $request->status];
        if ($request->status === 'completed') {
            $updateData['delivered_at'] = now();
            $updateData['payment_status'] = 'paid'; // Assuming completion means paid if cash
        }

        $task->update($updateData);

        return $this->successResponse($task, __('messages.order.updated'));
    }

    /**
     * Upload proof photos
     */
    public function uploadTaskProof(Request $request, $type, $id)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'item_photo' => 'nullable|image|max:10240',
            'invoice_photo' => 'nullable|image|max:10240',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $model = $this->getModelByType($type);
        if (!$model)
            return $this->errorResponse(__('messages.reorder.invalid_type'), 422);

        $task = $model::where('id', $id)
            ->where('delivery_agent_id', $user->id)
            ->first();

        if (!$task)
            return $this->errorResponse(__('messages.order.loaded'), 404);

        $data = [];
        if ($request->hasFile('item_photo')) {
            $data['item_photo'] = $request->file('item_photo')->store("tasks/{$type}/items", 'public');
        }
        if ($request->hasFile('invoice_photo')) {
            $data['invoice_photo'] = $request->file('invoice_photo')->store("tasks/{$type}/invoices", 'public');
        }

        $task->update($data);

        // Map full URLs
        if (isset($data['item_photo']))
            $task->item_photo = asset('storage/' . $data['item_photo']);
        if (isset($data['invoice_photo']))
            $task->invoice_photo = asset('storage/' . $data['invoice_photo']);

        return $this->successResponse($task, __('messages.delivery_agent.document_uploaded'));
    }

    private function getModelByType($type)
    {
        switch ($type) {
            case 'order':
                return Order::class;
            case 'delivery':
                return Delivery::class;
            case 'car_wash':
                return CarWash::class;
            default:
                return null;
        }
    }
}

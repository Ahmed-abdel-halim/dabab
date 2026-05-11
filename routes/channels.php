<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\CarWash;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('task.tracking.{type}.{id}', function ($user, $type, $id) {
    $model = null;
    
    switch ($type) {
        case 'order': $model = Order::find($id); break;
        case 'delivery': $model = Delivery::find($id); break;
        case 'car_wash': $model = CarWash::find($id); break;
    }

    if (!$model) return false;

    // Only the customer or the assigned delivery agent can listen
    return (int) $user->id === (int) $model->user_id || 
           (int) $user->id === (int) $model->delivery_agent_id;
});

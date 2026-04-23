<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Delivery::with(['user', 'agent']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('sort')) {
            if ($request->sort == 'oldest') {
                $query->oldest();
            } elseif ($request->sort == 'highest_cost') {
                $query->orderByDesc('delivery_cost');
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $deliveries = $query->paginate(15)->withQueryString();

        return view('admin.deliveries.index', compact('deliveries'));
    }

    public function show($id)
    {
        $delivery = Delivery::with(['user', 'agent'])->findOrFail($id);
        
        return view('admin.deliveries.show', compact('delivery'));
    }
}

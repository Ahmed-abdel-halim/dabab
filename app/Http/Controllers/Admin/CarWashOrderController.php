<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarWash;
use Illuminate\Http\Request;

class CarWashOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = CarWash::with('user', 'agent');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('id', 'like', "%{$search}%") // No order_number explicitly listed? Let's use id.
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
                $query->orderByDesc('cost');
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $carWashes = $query->paginate(15)->withQueryString();

        return view('admin.car_washes.index', compact('carWashes'));
    }

    public function show($id)
    {
        $carWash = CarWash::with(['user', 'agent', 'location'])->findOrFail($id);
        
        return view('admin.car_washes.show', compact('carWash'));
    }
}

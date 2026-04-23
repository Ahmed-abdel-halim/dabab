<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $query = Rental::with('user');

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
                $query->orderByDesc('cost');
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $rentals = $query->paginate(15)->withQueryString();

        return view('admin.rentals.index', compact('rentals'));
    }

    public function show($id)
    {
        $rental = Rental::with(['user', 'location'])->findOrFail($id);
        
        return view('admin.rentals.show', compact('rental'));
    }
}

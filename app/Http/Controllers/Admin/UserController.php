<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('role', '!=', 'delivery_agent');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role != 'all') {
            if ($request->role == 'client') {
                $query->where(function($q) {
                    $q->where('role', 'client')->orWhereNull('role');
                });
            } else {
                $query->where('role', $request->role);
            }
        }

        if ($request->filled('sort')) {
            if ($request->sort == 'oldest') {
                $query->oldest();
            } elseif ($request->sort == 'highest_balance') {
                $query->orderByDesc('wallet_balance');
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::withCount(['orders', 'deliveries'])->findOrFail($id);
        
        return view('admin.users.show', compact('user'));
    }
}

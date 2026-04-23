<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = \App\Models\User::count();
        $activeAgents = \App\Models\DeliveryAgentProfile::where('status', 'active')->count() ?: \App\Models\User::where('role', 'delivery')->count();

        $todayOrdersC = \App\Models\Order::whereDate('created_at', today())->count();
        $todayDeliveries = \App\Models\Delivery::whereDate('created_at', today())->count();
        $todayRentals = \App\Models\Rental::whereDate('created_at', today())->count();
        $todayCarWashes = \App\Models\CarWash::whereDate('created_at', today())->count();
        $todayOrders = $todayOrdersC + $todayDeliveries + $todayRentals + $todayCarWashes;

        $revenueOrders = \App\Models\Order::whereDate('created_at', today())->sum('total_cost') ?? 0;
        $revenueDeliveries = \App\Models\Delivery::whereDate('created_at', today())->sum('delivery_cost') ?? 0;
        $revenueRentals = \App\Models\Rental::whereDate('created_at', today())->sum('cost') ?? 0;
        $revenueCarWashes = \App\Models\CarWash::whereDate('created_at', today())->sum('cost') ?? 0;
        $todayRevenue = $revenueOrders + $revenueDeliveries + $revenueRentals + $revenueCarWashes;

        $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('total_cost') +
            \App\Models\Delivery::where('status', 'completed')->sum('delivery_cost') +
            \App\Models\Rental::where('status', 'completed')->sum('cost') +
            \App\Models\CarWash::where('status', 'completed')->sum('cost');

        $totalOrdersCount = \App\Models\Order::count() + \App\Models\Delivery::count() + \App\Models\Rental::count() + \App\Models\CarWash::count();

        $recentOrders = \App\Models\Order::with('user')->latest()->take(5)->get()->map(function ($o) {
            $o->op_type = __('Store');
            $o->type_icon = 'fa-box text-blue-500';
            $o->display_id = '#ORD-' . str_pad($o->id, 3, '0', STR_PAD_LEFT);
            return $o;
        });

        $recentDeliveries = \App\Models\Delivery::with('user')->latest()->take(5)->get()->map(function ($d) {
            $d->op_type = __('Delivery Package');
            $d->type_icon = 'fa-motorcycle text-amber-500';
            $d->display_id = '#DEL-' . str_pad($d->id, 3, '0', STR_PAD_LEFT);
            return $d;
        });

        $recentCarWashes = \App\Models\CarWash::with('user')->latest()->take(5)->get()->map(function ($c) {
            $c->op_type = __('Car Wash Activity');
            $c->type_icon = 'fa-car-side text-green-500';
            $c->display_id = '#WAS-' . str_pad($c->id, 3, '0', STR_PAD_LEFT);
            return $c;
        });

        $recentRentals = \App\Models\Rental::with('user')->latest()->take(5)->get()->map(function ($r) {
            $r->op_type = __('Bike Rental Activity');
            $r->type_icon = 'fa-key text-red-500';
            $r->display_id = '#REN-' . str_pad($r->id, 3, '0', STR_PAD_LEFT);
            return $r;
        });

        $recentOperations = $recentOrders->concat($recentDeliveries)->concat($recentCarWashes)->concat($recentRentals)->sortByDesc('created_at')->take(5);

        return view('admin.dashboard', compact('totalUsers', 'activeAgents', 'todayOrders', 'todayRevenue', 'recentOperations', 'totalRevenue', 'totalOrdersCount'));
    }
}

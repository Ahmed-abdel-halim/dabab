<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('admin.layouts.app', function ($view) {
            $view->with('pendingOrdersCount', \App\Models\Order::where('status', 'pending')->count());
            $view->with('pendingDeliveriesCount', \App\Models\Delivery::where('status', 'pending')->count());
            $view->with('pendingRentalsCount', \App\Models\Rental::where('status', 'pending')->count());
            $view->with('pendingCarWashesCount', \App\Models\CarWash::where('status', 'pending')->count());
        });
    }
}

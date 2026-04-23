<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    Route::get('lang/{locale}', function ($locale) {
        if (in_array($locale, ['ar', 'en'])) {
            session()->put('locale', $locale);
            app()->setLocale($locale);
        }
        return redirect()->back();
    })->name('lang.switch');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');

        // Agents
        Route::get('agents', [\App\Http\Controllers\Admin\AgentController::class, 'index'])->name('agents.index');
        Route::get('agents/{id}', [\App\Http\Controllers\Admin\AgentController::class, 'show'])->name('agents.show');
        Route::post('agents/{id}/status', [\App\Http\Controllers\Admin\AgentController::class, 'updateStatus'])->name('agents.status');

        // Orders
        Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');

        // Deliveries
        Route::get('deliveries', [\App\Http\Controllers\Admin\DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('deliveries/{id}', [\App\Http\Controllers\Admin\DeliveryController::class, 'show'])->name('deliveries.show');

        // Rentals
        Route::get('rentals', [\App\Http\Controllers\Admin\RentalController::class, 'index'])->name('rentals.index');
        Route::get('rentals/{id}', [\App\Http\Controllers\Admin\RentalController::class, 'show'])->name('rentals.show');

        // Car Washes
        Route::get('car-washes', [\App\Http\Controllers\Admin\CarWashOrderController::class, 'index'])->name('car_washes.index');
        Route::get('car-washes/{id}', [\App\Http\Controllers\Admin\CarWashOrderController::class, 'show'])->name('car_washes.show');

        // Ratings
        Route::get('ratings', [\App\Http\Controllers\Admin\RatingController::class, 'index'])->name('ratings.index');
        Route::delete('ratings/{id}', [\App\Http\Controllers\Admin\RatingController::class, 'destroy'])->name('ratings.destroy');

        // Wallet
        Route::get('wallet', [\App\Http\Controllers\Admin\WalletTransactionController::class, 'index'])->name('wallet.index');

        // Settings / Car Wash
        Route::get('settings/car-wash', [\App\Http\Controllers\Admin\CarWashSettingsController::class, 'index'])->name('settings.carwash');
        Route::post('settings/car-wash/prices', [\App\Http\Controllers\Admin\CarWashSettingsController::class, 'updatePrices'])->name('settings.carwash.prices');
        Route::post('settings/car-wash/periods/{period}/toggle', [\App\Http\Controllers\Admin\CarWashSettingsController::class, 'togglePeriod'])->name('settings.carwash.periods.toggle');
        Route::post('settings/car-wash/periods', [\App\Http\Controllers\Admin\CarWashSettingsController::class, 'storePeriod'])->name('settings.carwash.periods.store');

        // Settings / Categories
        Route::get('settings/categories', [\App\Http\Controllers\Admin\OrderCategoryController::class, 'index'])->name('settings.categories.index');
        Route::post('settings/categories', [\App\Http\Controllers\Admin\OrderCategoryController::class, 'store'])->name('settings.categories.store');
        Route::post('settings/categories/{id}', [\App\Http\Controllers\Admin\OrderCategoryController::class, 'update'])->name('settings.categories.update');

        // Settings / General
        Route::get('settings/general', [\App\Http\Controllers\Admin\GeneralSettingsController::class, 'index'])->name('settings.general');
        Route::post('settings/general', [\App\Http\Controllers\Admin\GeneralSettingsController::class, 'update'])->name('settings.general.update');

        // Settings / Pages
        Route::get('settings/pages', [\App\Http\Controllers\Admin\PageController::class, 'index'])->name('settings.pages.index');
        Route::get('settings/pages/{id}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])->name('settings.pages.edit');
        Route::post('settings/pages/{id}', [\App\Http\Controllers\Admin\PageController::class, 'update'])->name('settings.pages.update');

        // Settings / FAQs
        Route::get('settings/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'index'])->name('settings.faqs.index');
        Route::post('settings/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'store'])->name('settings.faqs.store');
        Route::post('settings/faqs/{id}', [\App\Http\Controllers\Admin\FaqController::class, 'update'])->name('settings.faqs.update');
        Route::delete('settings/faqs/{id}', [\App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('settings.faqs.destroy');
    });
});

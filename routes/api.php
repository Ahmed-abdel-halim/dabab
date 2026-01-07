<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\OrderController;
use App\Http\Controllers\V1\RentalController;
use App\Http\Controllers\V1\DeliveryController;
use App\Http\Controllers\V1\CarWashController;
use App\Http\Controllers\V1\RatingController;
use App\Http\Controllers\V1\AddressController;

// Public Routes
Route::post('v1/send-otp', [AuthController::class, 'sendVerificationCode']);
Route::post('v1/verify-otp', [AuthController::class, 'verifyRegistrationCode']);
Route::post('v1/complete-profile', [AuthController::class, 'completeRegistration']);
Route::post('v1/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::post('v1/logout', [AuthController::class, 'logout']);
    Route::post('v1/complete-location', [AuthController::class, 'completeLocation']);
    Route::get('v1/mylocation', [AuthController::class, 'getLocation']);
    Route::get('v1/profile', [AuthController::class, 'profile']);

    // Address Routes
    Route::prefix('v1/addresses')->group(function () {
        Route::get('/', [AddressController::class, 'getMyAddresses']);
        Route::post('/', [AddressController::class, 'createAddress']);
        Route::get('/{id}', [AddressController::class, 'getAddress']);
        Route::put('/{id}', [AddressController::class, 'updateAddress']);
        Route::delete('/{id}', [AddressController::class, 'deleteAddress']);
    });

    // Order Routes
    Route::prefix('v1/orders')->group(function () {
        Route::get('/categories', [OrderController::class, 'getCategories']);
        Route::get('/', [OrderController::class, 'getMyOrders']);
        Route::post('/', [OrderController::class, 'createOrder']);
        Route::get('/{id}', [OrderController::class, 'getOrder']);
        Route::put('/{id}', [OrderController::class, 'updateOrder']);
        Route::post('/{id}/cancel', [OrderController::class, 'cancelOrder']);
        Route::get('/{id}/track', [OrderController::class, 'trackOrder']);
        Route::post('/{id}/confirm', [OrderController::class, 'confirmOrder']);
        // Routes for order items (sub-orders)
        Route::post('/{id}/items', [OrderController::class, 'addOrderItem']);
        Route::put('/{orderId}/items/{itemId}', [OrderController::class, 'updateOrderItem']);
        Route::delete('/{orderId}/items/{itemId}', [OrderController::class, 'deleteOrderItem']);
    });

    // Rental Routes
    Route::prefix('v1/rentals')->group(function () {
        Route::get('/', [RentalController::class, 'getMyRentals']);
        Route::post('/', [RentalController::class, 'createRental']);
        Route::get('/{id}', [RentalController::class, 'getRental']);
        Route::put('/{id}', [RentalController::class, 'updateRental']);
    });

    // Delivery Routes
    Route::prefix('v1/deliveries')->group(function () {
        Route::get('/', [DeliveryController::class, 'getMyDeliveries']);
        Route::post('/', [DeliveryController::class, 'createDelivery']);
        Route::get('/{id}', [DeliveryController::class, 'getDelivery']);
        Route::put('/{id}', [DeliveryController::class, 'updateDelivery']);
        Route::post('/{id}/cancel', [DeliveryController::class, 'cancelDelivery']);
        Route::get('/{id}/track', [DeliveryController::class, 'trackDelivery']);
    });

    // Car Wash Routes
    Route::prefix('v1/car-washes')->group(function () {
        Route::get('/', [CarWashController::class, 'getMyCarWashes']);
        Route::post('/', [CarWashController::class, 'createCarWash']);
        Route::get('/{id}', [CarWashController::class, 'getCarWash']);
        Route::put('/{id}', [CarWashController::class, 'updateCarWash']);
        Route::post('/{id}/cancel', [CarWashController::class, 'cancelCarWash']);
    });

    // Rating Routes
    Route::prefix('v1/ratings')->group(function () {
        Route::get('/', [RatingController::class, 'getMyRatings']);
        Route::post('/', [RatingController::class, 'createRating']);
    });
});

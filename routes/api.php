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
use App\Http\Controllers\V1\AllOrdersController;
use App\Http\Controllers\V1\ReorderController;
use App\Http\Controllers\V1\PaymentController;
use App\Http\Controllers\V1\WalletController;
use App\Http\Controllers\V1\InfoController;
use App\Http\Controllers\V1\DeliveryAgent\DeliveryAgentRegistrationController;

// Public Routes
Route::post('v1/send-otp', [AuthController::class, 'sendVerificationCode']);
Route::post('v1/verify-otp', [AuthController::class, 'verifyRegistrationCode']);
Route::post('v1/complete-profile', [AuthController::class, 'completeRegistration']);
Route::post('v1/login', [AuthController::class, 'login']);

// Info Routes
Route::get('v1/faqs', [InfoController::class, 'getFaqs']);
Route::get('v1/privacy-policy', [InfoController::class, 'getPrivacyPolicy']);
Route::get('v1/terms-and-conditions', [InfoController::class, 'getTermsAndConditions']);
Route::get('v1/pages/{slug}', [InfoController::class, 'getPage']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::post('v1/logout', [AuthController::class, 'logout']);
    Route::post('v1/complete-location', [AuthController::class, 'completeLocation']);
    Route::get('v1/mylocation', [AuthController::class, 'getLocation']);
    Route::get('v1/profile', [AuthController::class, 'profile']);
    Route::post('v1/update-locale', [AuthController::class, 'updateLocale']);

    // Reorder Route
    Route::post('v1/reorder/{type}/{id}', [ReorderController::class, 'reorder']);

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
        Route::post('/{id}/cancel', [OrderController::class, 'cancelOrder']);
        Route::get('/{id}/track', [OrderController::class, 'trackOrder']);
        Route::post('/{id}/confirm', [OrderController::class, 'confirmOrder']);
        Route::post('/{id}/items', [OrderController::class, 'addOrderItem']);
        Route::put('/{orderId}/items/{itemId}', [OrderController::class, 'updateOrderItem']);
        Route::delete('/{orderId}/items/{itemId}', [OrderController::class, 'deleteOrderItem']);
    });

    // Rental Routes
    Route::prefix('v1/rentals')->group(function () {
        Route::get('/', [RentalController::class, 'getMyRentals']);
        Route::post('/', [RentalController::class, 'createRental']);
        Route::get('/{id}', [RentalController::class, 'getRental']);
    });

    // Delivery Routes
    Route::prefix('v1/deliveries')->group(function () {
        Route::get('/', [DeliveryController::class, 'getMyDeliveries']);
        Route::post('/', [DeliveryController::class, 'createDelivery']);
        Route::get('/{id}', [DeliveryController::class, 'getDelivery']);
        Route::post('/{id}/cancel', [DeliveryController::class, 'cancelDelivery']);
        Route::get('/{id}/track', [DeliveryController::class, 'trackDelivery']);
    });

    // Car Wash Routes
    Route::prefix('v1/car-washes')->group(function () {
        Route::get('/available-dates', [CarWashController::class, 'getAvailableDates']);
        Route::get('/time-periods', [CarWashController::class, 'getTimePeriods']);
        Route::get('/', [CarWashController::class, 'getMyCarWashes']);
        Route::post('/', [CarWashController::class, 'createCarWash']);
        Route::get('/{id}', [CarWashController::class, 'getCarWash']);
        Route::post('/{id}/cancel', [CarWashController::class, 'cancelCarWash']);
    });

    // Rating Routes
    Route::prefix('v1/ratings')->group(function () {
        Route::get('/', [RatingController::class, 'getMyRatings']);
        Route::post('/', [RatingController::class, 'createRating']);
    });

    // All Orders Routes
    Route::get('v1/all-orders', [AllOrdersController::class, 'getAllOrders']);
    Route::put('v1/all-orders/{type}/{id}', [AllOrdersController::class, 'updateService']);
    Route::delete('v1/all-orders/{type}/{id}', [AllOrdersController::class, 'deleteService']);

    // Payment/Cards Routes
    Route::get('v1/payment-methods', [PaymentController::class, 'getPaymentMethods']);
    Route::post('v1/payment/apple-pay', [PaymentController::class, 'processApplePay']);
    Route::prefix('v1/cards')->group(function () {
        Route::get('/', [PaymentController::class, 'getCards']);
        Route::post('/', [PaymentController::class, 'storeCard']);
        Route::delete('/{id}', [PaymentController::class, 'destroyCard']);
    });

    // Wallet Routes
    Route::prefix('v1/wallet')->group(function () {
        Route::get('/balance', [WalletController::class, 'getBalance']);
        Route::post('/charge', [WalletController::class, 'chargeWallet']);
    });

    // Delivery Agent Status Route (Authenticated users only)
    Route::get('v1/delivery-agent/registration-status', [DeliveryAgentRegistrationController::class, 'getRegistrationStatus']);
});

// Delivery Agent Registration Steps (Guest with temp_token)
Route::prefix('v1/delivery-agent')->group(function () {
    Route::post('/complete-profile', [DeliveryAgentRegistrationController::class, 'completeDeliveryAgentProfile']);
    Route::post('/register-vehicle', [DeliveryAgentRegistrationController::class, 'registerVehicleDetails']);
    Route::post('/register-bank', [DeliveryAgentRegistrationController::class, 'registerBankDetails']);
});

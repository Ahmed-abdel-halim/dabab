<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\AuthController;

Route::post('v1/send-otp', [AuthController::class, 'sendVerificationCode']);
Route::post('v1/verify-otp', [AuthController::class, 'verifyRegistrationCode']);
Route::post('v1/complete-profile', [AuthController::class, 'completeRegistration']);
Route::post('v1/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('v1/logout', [AuthController::class, 'logout']);

    Route::post('v1/complete-location', [AuthController::class, 'completeLocation']);
    Route::get('v1/mylocation', [AuthController::class, 'getLocation']);
    Route::get('v1/profile', [AuthController::class, 'profile']);
    Route::post('v1/location/distance', [AuthController::class, 'calculateDistance']);
});

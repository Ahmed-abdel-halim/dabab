<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLocation;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponseTrait;

    const STATIC_OTP = '1234';

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'type'  => 'nullable|in:registration,login',
        ]);

        $phone = $request->phone;
        $type  = $request->type ?? 'registration';

        $user = User::where('phone', $phone)->first();

        if ($type === 'login' && !$user) {
            return $this->errorResponse(__('messages.auth.phone_not_registered'), 404);
        }

        if ($type === 'registration' && $user) {
            return $this->errorResponse(__('messages.auth.phone_already_registered'), 422);
        }

        Cache::put(
            'otp_' . $phone . '_' . $type,
            self::STATIC_OTP,
            now()->addMinutes(10)
        );

        return $this->successResponse([
            'otp' => self::STATIC_OTP,
            'expires_in' => 10
        ], __('messages.auth.verification_code_sent'));
    }

    public function verifyRegistrationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->phone . '_registration');

        if (!$cachedOtp || $cachedOtp !== $request->code) {
            return $this->errorResponse(__('messages.auth.verification_code_invalid'), 422);
        }

        if (User::where('phone', $request->phone)->exists()) {
            return $this->errorResponse(__('messages.auth.phone_already_registered'), 422);
        }

        $tempToken = bin2hex(random_bytes(32));

        Cache::put(
            'registration_' . $tempToken,
            [
                'phone' => $request->phone,
                'verified_at' => now(),
            ],
            now()->addMinutes(30)
        );

        Cache::forget('otp_' . $request->phone . '_registration');

        return $this->successResponse([
            'temp_token' => $tempToken,
            'next_step'  => 'complete_registration'
        ], __('messages.auth.verification_success'));
    }

    public function completeRegistration(Request $request)
    {
        $request->validate([
            'temp_token' => 'required|string',
            'name'       => 'nullable|string|max:255',
            'email'      => 'nullable|email|max:255|unique:users,email',
        ]);

        $data = Cache::get('registration_' . $request->temp_token);

        if (!$data) {
            return $this->errorResponse(__('messages.auth.registration_token_invalid'), 422);
        }

        if (User::where('phone', $data['phone'])->exists()) {
            Cache::forget('registration_' . $request->temp_token);
            return $this->errorResponse(__('messages.auth.phone_already_registered'), 422);
        }

        $user = User::create([
            'name'              => $request->name ?? __('messages.auth.default_user_name') . ' ' . substr($data['phone'], -4),
            'email'             => $request->email ?? $data['phone'] . '@user.local',
            'phone'             => $data['phone'],
            'phone_verified_at' => $data['verified_at'],
            'password'          => Hash::make(uniqid()),
        ]);

        Cache::forget('registration_' . $request->temp_token);

        $token = $user->createToken('auth')->plainTextToken;

        return $this->successResponse([
            'user'  => $user,
            'token' => $token
        ], __('messages.auth.registration_success'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->phone . '_login');

        if (!$cachedOtp || $cachedOtp !== $request->code) {
            return $this->errorResponse(__('messages.auth.verification_code_invalid'), 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return $this->errorResponse(__('messages.auth.phone_not_registered'), 404);
        }

        Cache::forget('otp_' . $request->phone . '_login');

        $user->update(['phone_verified_at' => now()]);

        $token = $user->createToken('auth')->plainTextToken;

        return $this->successResponse([
            'user'  => $user,
            'token' => $token
        ], __('messages.auth.login_success'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, __('messages.auth.logout_success'));
    }

    public function profile(Request $request)
    {
        return $this->successResponse(
            $request->user()->load('location'),
            __('messages.auth.profile_loaded')
        );
    }

    public function completeLocation(Request $request)
    {
        $request->validate([
            'lat'     => 'required|numeric',
            'lng'     => 'required|numeric',
            'address' => 'required|string',
            'type'    => 'nullable|in:home,work,friend,other',
            'is_default' => 'nullable|boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->is_default) {
            UserLocation::where('user_id', $request->user()->id)
                ->update(['is_default' => false]);
        }

        $location = UserLocation::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'is_default' => true // Default location
            ],
            [
                'lat' => $request->lat,
                'lng' => $request->lng,
                'address' => $request->address,
                'type' => $request->type ?? 'home',
                'is_default' => $request->is_default ?? true,
            ]
        );

        return $this->successResponse($location, __('messages.auth.location_updated'));
    }

    public function updateLocale(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:ar,en',
        ]);

        $user = $request->user();
        $user->update(['locale' => $request->locale]);

        return $this->successResponse(
            ['locale' => $user->locale],
            __('messages.auth.locale_updated')
        );
    }
}

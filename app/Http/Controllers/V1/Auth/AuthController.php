<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLocation;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type'  => 'nullable|in:registration,login',
        ]);

        $email = $request->email;
        $type  = $request->type ?? 'registration';

        $user = User::where('email', $email)->first();

        if ($type === 'login' && !$user) {
            return $this->errorResponse(__('messages.auth.email_not_registered'), 404);
        }

        if ($type === 'registration' && $user) {
            return $this->errorResponse(__('messages.auth.email_already_registered'), 422);
        }

        // Generate Random OTP (4 digits)
        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Store OTP in cache
        Cache::put(
            'otp_' . $email . '_' . $type,
            $otp,
            now()->addMinutes(10)
        );

        // Send OTP via email using Job for faster response
        \App\Jobs\SendOtpJob::dispatch($email, $otp, $type);

        return $this->successResponse([
            'expires_in' => 10
        ], __('messages.auth.verification_code_sent'));
    }

    public function verifyRegistrationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->email . '_registration');

        if (!$cachedOtp || $cachedOtp !== $request->code) {
            return $this->errorResponse(__('messages.auth.verification_code_invalid'), 422);
        }

        if (User::where('email', $request->email)->exists()) {
            return $this->errorResponse(__('messages.auth.email_already_registered'), 422);
        }

        $tempToken = bin2hex(random_bytes(32));

        Cache::put(
            'registration_' . $tempToken,
            [
                'email' => $request->email,
                'verified_at' => now(),
            ],
            now()->addMinutes(30)
        );

        Cache::forget('otp_' . $request->email . '_registration');

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
            'phone'      => 'required|string|unique:users,phone',
        ]);

        $data = Cache::get('registration_' . $request->temp_token);

        if (!$data) {
            return $this->errorResponse(__('messages.auth.registration_token_invalid'), 422);
        }

        if (User::where('email', $data['email'])->exists()) {
            Cache::forget('registration_' . $request->temp_token);
            return $this->errorResponse(__('messages.auth.email_already_registered'), 422);
        }

        if (User::where('phone', $request->phone)->exists()) {
            return $this->errorResponse(__('messages.auth.phone_already_registered'), 422);
        }

        $user = User::create([
            'name'  => $request->name ?? __('messages.auth.default_user_name'),
            'email' => $data['email'],
            'phone' => $request->phone,
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
            'email' => 'required|email',
            'code'  => 'required|string',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->email . '_login');

        if (!$cachedOtp || $cachedOtp !== $request->code) {
            return $this->errorResponse(__('messages.auth.verification_code_invalid'), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse(__('messages.auth.email_not_registered'), 404);
        }

        Cache::forget('otp_' . $request->email . '_login');

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

    public function getLocation(Request $request)
    {
        $location = $request->user()->location;
        return $this->successResponse($location, __('messages.auth.profile_loaded'));
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

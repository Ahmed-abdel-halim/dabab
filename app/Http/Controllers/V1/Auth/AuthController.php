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
            return $this->errorResponse('رقم الهاتف غير مسجل', 404);
        }

        if ($type === 'registration' && $user) {
            return $this->errorResponse('رقم الهاتف مسجل بالفعل', 422);
        }

        Cache::put(
            'otp_' . $phone . '_' . $type,
            self::STATIC_OTP,
            now()->addMinutes(10)
        );

        return $this->successResponse([
            'otp' => self::STATIC_OTP,
            'expires_in' => 10
        ], 'تم إرسال كود التحقق');
    }

    public function verifyRegistrationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->phone . '_registration');

        if (!$cachedOtp || $cachedOtp !== $request->code) {
            return $this->errorResponse('كود التحقق غير صحيح أو منتهي', 422);
        }

        if (User::where('phone', $request->phone)->exists()) {
            return $this->errorResponse('رقم الهاتف مسجل بالفعل', 422);
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
        ], 'تم التحقق بنجاح');
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
            return $this->errorResponse('رمز التسجيل غير صالح', 422);
        }

        if (User::where('phone', $data['phone'])->exists()) {
            Cache::forget('registration_' . $request->temp_token);
            return $this->errorResponse('رقم الهاتف مسجل بالفعل', 422);
        }

        $user = User::create([
            'name'              => $request->name ?? 'مستخدم ' . substr($data['phone'], -4),
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
        ], 'تم التسجيل بنجاح');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->phone . '_login');

        if (!$cachedOtp || $cachedOtp !== $request->code) {
            return $this->errorResponse('كود التحقق غير صحيح أو منتهي', 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return $this->errorResponse('رقم الهاتف غير مسجل', 404);
        }

        Cache::forget('otp_' . $request->phone . '_login');

        $user->update(['phone_verified_at' => now()]);

        $token = $user->createToken('auth')->plainTextToken;

        return $this->successResponse([
            'user'  => $user,
            'token' => $token
        ], 'تم تسجيل الدخول');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'تم تسجيل الخروج');
    }

    public function profile(Request $request)
    {
        return $this->successResponse(
            $request->user()->load('location'),
            'تم تحميل الملف الشخصي'
        );
    }

    public function completeLocation(Request $request)
    {
        $request->validate([
            'lat'     => 'required|numeric',
            'lng'     => 'required|numeric',
            'address' => 'nullable|string',
        ]);

        $location = UserLocation::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->only('lat', 'lng', 'address')
        );

        return $this->successResponse($location, 'تم تحديث الموقع');
    }

    public function getLocation(Request $request)
    {
        return $this->successResponse(
            $request->user()->location,
            'تم جلب الموقع'
        );
    }
}

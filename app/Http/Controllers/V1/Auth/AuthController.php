<?php

namespace App\Http\Controllers\V1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use App\Traits\ApiResponseTrait;

class AuthController extends Controller
{
    use ApiResponseTrait;

  
    // SEND OTP
    // ============================
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required'
        ]);

        $otp = "1234"; 

        $user = User::updateOrCreate(
            ['phone' => $request->phone],
            [
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(5)
            ]
        );

        return $this->successResponse(
            ['otp' => $otp],
            'OTP sent successfully'
        );
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || $user->otp != $request->otp) {
            return $this->errorResponse('OTP Invalid', 400);
        }

        $token = $user->createToken('auth')->plainTextToken;

        $user->update(['otp' => null]);

        return $this->successResponse([
            'token' => $token,
            'user'  => $user
        ], 'OTP verified');
    }


    public function completeProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email',
        ]);

        $user = $request->user();

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return $this->successResponse($user, 'Profile updated successfully');
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }


    public function completeLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'address' => 'nullable|string',
        ]);

        $location = UserLocation::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'lat' => $request->lat,
                'lng' => $request->lng,
                'address' => $request->address
            ]
        );

        return $this->successResponse($location, "Location updated");
    }

    public function getLocation(Request $request)
    {
        return $this->successResponse(
            $request->user()->location,
            "Location fetched successfully"
        );
    }


    public function calculateDistance(Request $request)
    {
        $request->validate(['lat' => 'required|numeric', 'lng' => 'required|numeric']);

        $userLoc = $request->user()->location;
        if (!$userLoc) return $this->errorResponse("Location not set", 404);

        $earthRadius = 6371;
        $dLat = deg2rad($request->lat - $userLoc->lat);
        $dLng = deg2rad($request->lng - $userLoc->lng);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($userLoc->lat)) * cos(deg2rad($request->lat)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $this->successResponse([
            'distance_km' => round($distance, 2)
        ], 'Distance calculated');
    }

    public function profile(Request $request)
    {
        return $this->successResponse(
            $request->user()->load('location'),
            "Profile loaded"
        );
    }
}

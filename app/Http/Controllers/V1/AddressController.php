<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponseTrait;

    public function createAddress(Request $request)
    {
        $request->validate([
            'type' => 'required|in:home,work,friend,other',
            'address' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'is_default' => 'nullable|boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->is_default) {
            UserLocation::where('user_id', $request->user()->id)
                ->update(['is_default' => false]);
        }

        $location = UserLocation::create([
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'address' => $request->address,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'is_default' => $request->is_default ?? false,
        ]);

        return $this->successResponse($location, 'تم إضافة العنوان بنجاح');
    }

    public function getMyAddresses(Request $request)
    {
        $locations = UserLocation::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return $this->successResponse($locations, 'تم جلب العناوين');
    }

    public function getAddress(Request $request, $id)
    {
        $location = UserLocation::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return $this->successResponse($location, 'تم جلب العنوان');
    }

    public function updateAddress(Request $request, $id)
    {
        $location = UserLocation::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $request->validate([
            'type' => 'nullable|in:home,work,friend,other',
            'address' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->is_default) {
            UserLocation::where('user_id', $request->user()->id)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $location->update($request->only([
            'type', 'address', 'lat', 'lng', 'is_default'
        ]));

        return $this->successResponse($location, 'تم تحديث العنوان');
    }

    public function deleteAddress(Request $request, $id)
    {
        $location = UserLocation::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $location->delete();

        return $this->successResponse(null, 'تم حذف العنوان');
    }
}


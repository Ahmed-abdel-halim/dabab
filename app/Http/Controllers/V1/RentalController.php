<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RentalController extends Controller
{
    use ApiResponseTrait;

    public function createRental(Request $request)
    {
        $request->validate([
            'personal_name' => 'required|string|max:255',
            'commercial_name' => 'required|string|max:255',
            'store_type' => 'required|string|max:255',
            'rental_type' => 'required|in:scooter_only,scooter_with_driver',
            'commercial_registration_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'additional_details' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('commercial_registration_file')) {
            $filePath = $request->file('commercial_registration_file')->store('rentals', 'public');
        }

        $rental = Rental::create([
            'user_id' => $request->user()->id,
            'personal_name' => $request->personal_name,
            'commercial_name' => $request->commercial_name,
            'store_type' => $request->store_type,
            'rental_type' => $request->rental_type,
            'commercial_registration_file' => $filePath,
            'additional_details' => $request->additional_details,
            'status' => 'pending',
        ]);

        return $this->successResponse($rental, 'تم تقديم طلب الاستئجار بنجاح');
    }

    public function getMyRentals(Request $request)
    {
        $rentals = Rental::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return $this->successResponse($rentals, 'تم جلب طلبات الاستئجار');
    }

    public function getRental(Request $request, $id)
    {
        $rental = Rental::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return $this->successResponse($rental, 'تم جلب طلب الاستئجار');
    }

    public function updateRental(Request $request, $id)
    {
        $rental = Rental::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'personal_name' => 'nullable|string|max:255',
            'commercial_name' => 'nullable|string|max:255',
            'store_type' => 'nullable|string|max:255',
            'rental_type' => 'nullable|in:scooter_only,scooter_with_driver',
            'commercial_registration_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'additional_details' => 'nullable|string',
        ]);

        if ($request->hasFile('commercial_registration_file')) {
            if ($rental->commercial_registration_file) {
                Storage::disk('public')->delete($rental->commercial_registration_file);
            }
            $filePath = $request->file('commercial_registration_file')->store('rentals', 'public');
            $rental->commercial_registration_file = $filePath;
        }

        $rental->update($request->only([
            'personal_name', 'commercial_name', 'store_type', 'rental_type', 'additional_details'
        ]));

        return $this->successResponse($rental, 'تم تحديث طلب الاستئجار');
    }
}


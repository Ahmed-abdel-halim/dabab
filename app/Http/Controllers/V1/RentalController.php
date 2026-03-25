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

        try {
            $filePath = null;
            if ($request->hasFile('commercial_registration_file')) {
                $file = $request->file('commercial_registration_file');
                $filePath = $file->store('rentals', 'public');
                
                if (!$filePath) {
                    return $this->errorResponse(__('messages.rental.file_upload_failed'), 500);
                }
            }

            $cost = \App\Models\SystemSetting::getValue("rental_price_{$request->rental_type}", $request->rental_type === 'scooter_only' ? 150 : 250);

            $rental = Rental::create([
                'user_id' => $request->user()->id,
                'personal_name' => $request->personal_name,
                'commercial_name' => $request->commercial_name,
                'store_type' => $request->store_type,
                'rental_type' => $request->rental_type,
                'commercial_registration_file' => $filePath,
                'additional_details' => $request->additional_details,
                'status' => 'pending',
                'cost' => $cost,
            ]);

            return $this->successResponse($rental, __('messages.rental.created'));
        } catch (\Exception $e) {
            if (isset($filePath) && $filePath) {
                Storage::disk('public')->delete($filePath);
            }
            return $this->errorResponse(__('messages.rental.create_error'), 500);
        }
    }

    public function getMyRentals(Request $request)
    {
        $rentals = Rental::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return $this->successResponse($rentals, __('messages.rental.rentals_loaded'));
    }

    public function getRental(Request $request, $id)
    {
        $rental = Rental::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return $this->successResponse($rental, __('messages.rental.loaded'));
    }
}

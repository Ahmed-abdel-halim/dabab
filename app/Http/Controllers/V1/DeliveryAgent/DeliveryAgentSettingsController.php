<?php

namespace App\Http\Controllers\V1\DeliveryAgent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DeliveryAgentProfile;
use App\Models\DeliveryVehicle;
use App\Models\DeliveryBankDetail;
use App\Models\DeliveryDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponseTrait;

class DeliveryAgentSettingsController extends Controller
{
    use ApiResponseTrait;

    /**
     * Update Delivery Agent General Profile
     */
    public function updateAgentProfile(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'delivery_agent') {
            return $this->errorResponse(__('messages.delivery_agent.not_delivery_agent'), 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'nationality' => 'nullable|string|max:255',
            'national_id_number' => 'nullable|string|unique:delivery_agent_profiles,national_id_number,' . $user->deliveryAgentProfile->id,
            'birth_date' => 'nullable|date|before:today',
            'working_service' => 'nullable|string|in:dabab_tawseel,car_wash',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        try {
            DB::beginTransaction();

            $user->update($request->only(['name', 'phone']));
            
            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $user->profile_photo = $request->file('profile_photo')->store('delivery_agents/profiles', 'public');
                $user->save();
            }

            $profile = $user->deliveryAgentProfile;
            $profile->update($request->only(['nationality', 'national_id_number', 'birth_date', 'working_service']));

            DB::commit();

            return $this->successResponse([
                'user' => $user,
                'profile' => $profile
            ], __('messages.auth.profile_updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(__('messages.error_occurred'), 500);
        }
    }

    /**
     * Update Vehicle Details
     */
    public function updateVehicle(Request $request)
    {
        $user = auth()->user();
        $profile = $user->deliveryAgentProfile;
        
        if (!$profile) {
            return $this->errorResponse(__('messages.delivery_agent.profile_not_found'), 404);
        }

        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'nullable|in:car,motorcycle,scooter,bicycle,other',
            'vehicle_brand' => 'nullable|string|max:255',
            'vehicle_model' => 'nullable|string|max:255',
            'manufacturing_year' => 'nullable|string|max:4',
            'license_plate_number' => 'nullable|string|max:255',
            'license_plate_letters' => 'nullable|string|max:255',
            'license_type' => 'nullable|string|max:255',
            'vehicle_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $vehicle = $profile->vehicle ?: new DeliveryVehicle(['delivery_agent_profile_id' => $profile->id]);
        $vehicle->fill($request->only([
            'vehicle_type', 'vehicle_brand', 'vehicle_model', 
            'manufacturing_year', 'license_plate_number', 'license_plate_letters', 'license_type'
        ]));
        $vehicle->save();

        if ($request->hasFile('vehicle_photo')) {
            $path = $request->file('vehicle_photo')->store('delivery_agents/documents', 'public');
            
            // Register or update document
            DeliveryDocument::updateOrCreate(
                ['delivery_agent_profile_id' => $profile->id, 'document_type' => 'vehicle_photo'],
                ['file_path' => $path, 'status' => 'pending']
            );
        }

        return $this->successResponse($vehicle, __('messages.delivery_agent.vehicle_details_saved'));
    }

    /**
     * Update Bank Details
     */
    public function updateBankDetails(Request $request)
    {
        $user = auth()->user();
        $profile = $user->deliveryAgentProfile;

        if (!$profile) {
            return $this->errorResponse(__('messages.delivery_agent.profile_not_found'), 404);
        }

        $validator = Validator::make($request->all(), [
            'bank_name' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:34',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $bank = $profile->bankDetails ?: new DeliveryBankDetail(['delivery_agent_profile_id' => $profile->id]);
        $bank->fill($request->only(['bank_name', 'account_holder_name', 'iban']));
        $bank->save();

        return $this->successResponse($bank, __('messages.delivery_agent.bank_details_saved'));
    }

    /**
     * Update/Upload Document
     */
    public function updateDocument(Request $request)
    {
        $user = auth()->user();
        $profile = $user->deliveryAgentProfile;

        $validator = Validator::make($request->all(), [
            'document_type' => 'required|string|in:id_front,id_back,driving_license,vehicle_registration_front,vehicle_registration_back,criminal_record,medical_check,vehicle_insurance,vehicle_photo',
            'file' => 'required|image|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $path = $request->file('file')->store('delivery_agents/documents', 'public');

        $document = DeliveryDocument::updateOrCreate(
            ['delivery_agent_profile_id' => $profile->id, 'document_type' => $request->document_type],
            ['file_path' => $path, 'status' => 'pending']
        );

        return $this->successResponse($document, __('messages.delivery_agent.document_uploaded'));
    }
}

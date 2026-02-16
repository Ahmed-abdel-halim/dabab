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
use Illuminate\Support\Facades\Cache;

class DeliveryAgentRegistrationController extends Controller
{
    /**
     * Get registration data from cache
     */
    private function getRegistrationData($temp_token)
    {
        return Cache::get('registration_' . $temp_token);
    }

    /**
     * Store registration data in cache
     */
    private function storeRegistrationData($temp_token, $data)
    {
        Cache::put('registration_' . $temp_token, $data, now()->addHours(24));
    }

    /**
     * Step 1: Save personal details in cache
     */
    public function completeDeliveryAgentProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temp_token' => 'required|string',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'nationality' => 'required|string|max:255',
            'national_id_number' => 'required|string|unique:delivery_agent_profiles,national_id_number',
            'birth_date' => 'required|date|before:today',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error'),
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->getRegistrationData($request->temp_token);
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => __('messages.auth.registration_token_invalid'),
            ], 422);
        }

        // Handle profile photo upload
        $profile_photo_path = $data['personal_details']['profile_photo_path'] ?? null;
        if ($request->hasFile('profile_photo')) {
            $profile_photo_path = $request->file('profile_photo')->store('delivery_agents/profiles/temp', 'public');
        }

        $data['personal_details'] = [
            'name' => $request->name,
            'phone' => $request->phone,
            'nationality' => $request->nationality,
            'national_id_number' => $request->national_id_number,
            'birth_date' => $request->birth_date,
            'profile_photo_path' => $profile_photo_path,
        ];

        $this->storeRegistrationData($request->temp_token, $data);

        return response()->json([
            'success' => true,
            'message' => __('messages.delivery_agent.personal_details_saved'),
            'data' => [
                'next_step' => 'vehicle_details'
            ]
        ], 200);
    }

    /**
     * Step 2: Save vehicle details and documents in cache
     */
    public function registerVehicleDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temp_token' => 'required|string',
            'vehicle_type' => 'required|in:car,motorcycle,scooter,other',
            'vehicle_brand' => 'nullable|string|max:255',
            'vehicle_model' => 'nullable|string|max:255',
            'manufacturing_year' => 'nullable|string|max:4',
            'license_plate_number' => 'nullable|string|max:255',
            'license_plate_letters' => 'nullable|string|max:255',
            'license_type' => 'nullable|string|max:255',
            
            // Documents
            'id_front' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:10240',
            'id_back' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:10240',
            'driving_license' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:10240',
            'vehicle_registration_front' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:10240',
            'vehicle_registration_back' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:10240',
            'criminal_record' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:10240',
            'vehicle_insurance' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:10240',
            'vehicle_photo' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error'),
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->getRegistrationData($request->temp_token);
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => __('messages.auth.registration_token_invalid'),
            ], 422);
        }

        // Save Vehicle Data
        $data['vehicle_details'] = $request->only([
            'vehicle_type',
            'vehicle_brand',
            'vehicle_model',
            'manufacturing_year',
            'license_plate_number',
            'license_plate_letters',
            'license_type',
        ]);

        // Save Documents (Files)
        if (!isset($data['documents'])) {
            $data['documents'] = [];
        }

        $documents = [
            'id_front', 'id_back', 'driving_license', 
            'vehicle_registration_front', 'vehicle_registration_back', 
            'criminal_record', 'vehicle_insurance', 'vehicle_photo'
        ];

        foreach ($documents as $doc) {
            if ($request->hasFile($doc)) {
                $path = $request->file($doc)->store('delivery_agents/documents/temp', 'public');
                $data['documents'][$doc] = $path;
            }
        }

        $this->storeRegistrationData($request->temp_token, $data);

        return response()->json([
            'success' => true,
            'message' => __('messages.delivery_agent.vehicle_details_saved'),
            'data' => [
                'next_step' => 'bank_details'
            ]
        ], 200);
    }

    /**
     * Step 3: Register bank details and FINALIZE registration (Create models)
     */
    public function registerBankDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temp_token' => 'required|string',
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error'),
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->getRegistrationData($request->temp_token);
        if (!$data || !isset($data['personal_details'])) {
            return response()->json([
                'success' => false,
                'message' => __('messages.auth.registration_token_invalid'),
            ], 422);
        }

        // Check if previous steps exist in cache
        if (!isset($data['vehicle_details']) || !isset($data['documents'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete previous registration steps first.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $personal = $data['personal_details'];
            $bankDetails = $request->only(['bank_name', 'account_holder_name', 'iban']);

            // 1. Create User
            $user = User::create([
                'name' => $personal['name'],
                'email' => $data['email'],
                'phone' => $personal['phone'],
                'role' => 'delivery_agent',
                'profile_photo' => $personal['profile_photo_path'] ? str_replace('/temp', '', $personal['profile_photo_path']) : null,
            ]);

            // Move profile photo from temp
            if ($personal['profile_photo_path']) {
                $new_path = str_replace('/temp', '', $personal['profile_photo_path']);
                if (Storage::disk('public')->exists($personal['profile_photo_path'])) {
                    Storage::disk('public')->move($personal['profile_photo_path'], $new_path);
                }
            }

            // 2. Create Delivery Agent Profile
            $profile = DeliveryAgentProfile::create([
                'user_id' => $user->id,
                'nationality' => $personal['nationality'],
                'national_id_number' => $personal['national_id_number'],
                'birth_date' => $personal['birth_date'],
                'status' => 'documents_uploaded',
            ]);

            // 3. Create Vehicle
            DeliveryVehicle::create(array_merge(
                ['delivery_agent_profile_id' => $profile->id],
                $data['vehicle_details']
            ));

            // 4. Create Bank Details
            DeliveryBankDetail::create(array_merge(
                ['delivery_agent_profile_id' => $profile->id],
                $bankDetails
            ));

            // 5. Create Documents and move files
            foreach ($data['documents'] as $type => $temp_path) {
                $new_path = str_replace('/temp', '', $temp_path);
                if (Storage::disk('public')->exists($temp_path)) {
                    Storage::disk('public')->move($temp_path, $new_path);
                }

                DeliveryDocument::create([
                    'delivery_agent_profile_id' => $profile->id,
                    'document_type' => $type,
                    'file_path' => $new_path,
                    'status' => 'pending',
                ]);
            }

            // Clear cache
            Cache::forget('registration_' . $request->temp_token);

            DB::commit();

            // Generate token for the new user
            $token = $user->createToken('auth')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => __('messages.delivery_agent.profile_created'),
                'data' => [
                    'user' => $user,
                    'profile' => $profile,
                    'token' => $token,
                    'status' => $profile->status,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get registration status (Strictly Authenticated)
     */
    public function getRegistrationStatus(Request $request)
    {
        try {
            $user = auth()->user();
            
            if (!$user || $user->role !== 'delivery_agent') {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.delivery_agent.not_delivery_agent'),
                ], 403);
            }

            $profile = $user->deliveryAgentProfile()->with(['vehicle', 'bankDetails', 'documents'])->first();

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.delivery_agent.profile_not_found'),
                ], 404);
            }

            // Map full URLs for profile photo and documents
            if ($user->profile_photo) {
                $user->profile_photo = asset('storage/' . $user->profile_photo);
            }

            if ($profile->documents) {
                foreach ($profile->documents as $document) {
                    $document->file_path = asset('storage/' . $document->file_path);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'profile' => $profile,
                    'status' => $profile->status,
                    'admin_comment' => $profile->admin_comment,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

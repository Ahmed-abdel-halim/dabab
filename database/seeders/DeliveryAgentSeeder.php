<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delivery Agent 1 - Pending (البيانات الشخصية فقط)
        $user1 = \App\Models\User::create([
            'name' => 'أحمد محمد علي',
            'phone' => '0501111111',
            'email' => 'ahmed.delivery@example.com',
            'role' => 'delivery_agent',
        ]);

        \App\Models\DeliveryAgentProfile::create([
            'user_id' => $user1->id,
            'nationality' => 'السعودية',
            'national_id_number' => '1111111111',
            'birth_date' => '1995-01-15',
            'status' => 'pending',
        ]);

        // Delivery Agent 2 - Documents Uploaded (جميع البيانات والمستندات)
        $user2 = \App\Models\User::create([
            'name' => 'محمد عبدالله حسن',
            'phone' => '0502222222',
            'email' => 'mohammed.delivery@example.com',
            'role' => 'delivery_agent',
        ]);

        $profile2 = \App\Models\DeliveryAgentProfile::create([
            'user_id' => $user2->id,
            'nationality' => 'السعودية',
            'national_id_number' => '2222222222',
            'birth_date' => '1990-05-20',
            'status' => 'documents_uploaded',
        ]);

        \App\Models\DeliveryVehicle::create([
            'delivery_agent_profile_id' => $profile2->id,
            'vehicle_type' => 'motorcycle',
            'vehicle_brand' => 'Honda',
            'vehicle_model' => 'CG125',
            'manufacturing_year' => '2020',
            'license_plate_number' => '1234',
            'license_plate_letters' => 'أ ب ج',
            'license_type' => 'Private',
        ]);

        \App\Models\DeliveryBankDetail::create([
            'delivery_agent_profile_id' => $profile2->id,
            'bank_name' => 'البنك الأهلي السعودي',
            'account_holder_name' => 'محمد عبدالله حسن',
            'iban' => 'SA2222222222222222222222',
        ]);

        // Create all required documents
        $documentTypes = [
            'national_id_front',
            'national_id_back',
            'driving_license_front',
            'driving_license_back',
            'vehicle_registration_front',
            'vehicle_registration_back',
        ];

        foreach ($documentTypes as $docType) {
            \App\Models\DeliveryDocument::create([
                'delivery_agent_profile_id' => $profile2->id,
                'document_type' => $docType,
                'file_path' => 'delivery_agents/documents/sample_' . $docType . '.jpg',
                'status' => 'pending',
            ]);
        }

        // Delivery Agent 3 - Approved (تم قبوله)
        $user3 = \App\Models\User::create([
            'name' => 'خالد أحمد السعدي',
            'phone' => '0503333333',
            'email' => 'khalid.delivery@example.com',
            'role' => 'delivery_agent',
        ]);

        $profile3 = \App\Models\DeliveryAgentProfile::create([
            'user_id' => $user3->id,
            'nationality' => 'السعودية',
            'national_id_number' => '3333333333',
            'birth_date' => '1988-08-10',
            'status' => 'approved',
            'admin_comment' => 'تم قبول الطلب. جميع المستندات صحيحة.',
        ]);

        \App\Models\DeliveryVehicle::create([
            'delivery_agent_profile_id' => $profile3->id,
            'vehicle_type' => 'car',
            'vehicle_brand' => 'Toyota',
            'vehicle_model' => 'Corolla',
            'manufacturing_year' => '2019',
            'license_plate_number' => '5678',
            'license_plate_letters' => 'د هـ و',
            'license_type' => 'Commercial',
        ]);

        \App\Models\DeliveryBankDetail::create([
            'delivery_agent_profile_id' => $profile3->id,
            'bank_name' => 'بنك الراجحي',
            'account_holder_name' => 'خالد أحمد السعدي',
            'iban' => 'SA3333333333333333333333',
        ]);

        foreach ($documentTypes as $docType) {
            \App\Models\DeliveryDocument::create([
                'delivery_agent_profile_id' => $profile3->id,
                'document_type' => $docType,
                'file_path' => 'delivery_agents/documents/sample_' . $docType . '.jpg',
                'status' => 'approved',
            ]);
        }

        // Delivery Agent 4 - Rejected (تم رفضه)
        $user4 = \App\Models\User::create([
            'name' => 'سعد عبدالرحمن القحطاني',
            'phone' => '0504444444',
            'email' => 'saad.delivery@example.com',
            'role' => 'delivery_agent',
        ]);

        $profile4 = \App\Models\DeliveryAgentProfile::create([
            'user_id' => $user4->id,
            'nationality' => 'السعودية',
            'national_id_number' => '4444444444',
            'birth_date' => '1992-12-25',
            'status' => 'rejected',
            'admin_comment' => 'تم رفض الطلب. رخصة القيادة منتهية الصلاحية. يرجى تحديث المستندات وإعادة التقديم.',
        ]);

        \App\Models\DeliveryVehicle::create([
            'delivery_agent_profile_id' => $profile4->id,
            'vehicle_type' => 'scooter',
            'vehicle_brand' => 'Vespa',
            'vehicle_model' => 'Primavera',
            'manufacturing_year' => '2021',
            'license_plate_number' => '9999',
            'license_plate_letters' => 'ز ح ط',
            'license_type' => 'Private',
        ]);

        \App\Models\DeliveryBankDetail::create([
            'delivery_agent_profile_id' => $profile4->id,
            'bank_name' => 'البنك السعودي للاستثمار',
            'account_holder_name' => 'سعد عبدالرحمن القحطاني',
            'iban' => 'SA4444444444444444444444',
        ]);

        foreach ($documentTypes as $docType) {
            \App\Models\DeliveryDocument::create([
                'delivery_agent_profile_id' => $profile4->id,
                'document_type' => $docType,
                'file_path' => 'delivery_agents/documents/sample_' . $docType . '.jpg',
                'status' => $docType === 'driving_license_front' ? 'rejected' : 'approved',
                'rejection_reason' => $docType === 'driving_license_front' ? 'رخصة القيادة منتهية الصلاحية' : null,
            ]);
        }

        echo "✅ تم إنشاء 4 عمال توصيل بحالات مختلفة:\n";
        echo "1. أحمد محمد علي - Pending (البيانات الشخصية فقط)\n";
        echo "2. محمد عبدالله حسن - Documents Uploaded (جميع البيانات والمستندات)\n";
        echo "3. خالد أحمد السعدي - Approved (تم القبول)\n";
        echo "4. سعد عبدالرحمن القحطاني - Rejected (تم الرفض)\n";
    }
}


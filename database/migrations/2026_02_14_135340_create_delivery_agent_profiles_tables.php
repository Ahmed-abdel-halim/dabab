<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delivery Agent Profile
        Schema::create('delivery_agent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nationality')->nullable();
            $table->string('national_id_number')->nullable()->unique();
            $table->date('birth_date')->nullable();
            $table->enum('status', ['pending', 'documents_uploaded', 'approved', 'rejected'])->default('pending');
            $table->text('admin_comment')->nullable();
            $table->timestamps();
        });

        // Delivery Vehicles
        Schema::create('delivery_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_agent_profile_id')->constrained('delivery_agent_profiles')->onDelete('cascade');
            $table->enum('vehicle_type', ['car', 'motorcycle', 'scooter', 'other'])->default('motorcycle');
            $table->string('vehicle_brand')->nullable(); // e.g., Toyota, Honda
            $table->string('vehicle_model')->nullable(); // e.g., Corolla, CG125
            $table->string('manufacturing_year')->nullable();
            $table->string('license_plate_number')->nullable();
            $table->string('license_plate_letters')->nullable(); // For cars in some regions
            $table->string('license_type')->nullable(); // e.g., Private, Commercial
            $table->timestamps();
        });

        // Delivery Bank Details
        Schema::create('delivery_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_agent_profile_id')->constrained('delivery_agent_profiles')->onDelete('cascade');
            $table->string('bank_name')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('iban')->nullable();
            $table->timestamps();
        });

        // Delivery Documents
        Schema::create('delivery_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_agent_profile_id')->constrained('delivery_agent_profiles')->onDelete('cascade');
            $table->string('document_type'); // e.g., 'national_id_front', 'national_id_back', 'driving_license', 'vehicle_registration'
            $table->string('file_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_documents');
        Schema::dropIfExists('delivery_bank_details');
        Schema::dropIfExists('delivery_vehicles');
        Schema::dropIfExists('delivery_agent_profiles');
    }
};

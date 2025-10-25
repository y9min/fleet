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
        Schema::table('onboarding_drivers', function (Blueprint $table) {
            // Add custom_data if it doesn't exist
            if (!Schema::hasColumn('onboarding_drivers', 'custom_data')) {
                $table->json('custom_data')->nullable()->after('insurance_upload_path');
            }
            
            // Add license_expiry if it doesn't exist
            if (!Schema::hasColumn('onboarding_drivers', 'license_expiry')) {
                $table->date('license_expiry')->nullable()->after('license_number');
            }
            
            // Add address if it doesn't exist
            if (!Schema::hasColumn('onboarding_drivers', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            
            // Add emergency_contact if it doesn't exist
            if (!Schema::hasColumn('onboarding_drivers', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('address');
            }
            
            // Add emergency_phone if it doesn't exist
            if (!Schema::hasColumn('onboarding_drivers', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable()->after('emergency_contact');
            }
            
            // Add form_data if it doesn't exist
            if (!Schema::hasColumn('onboarding_drivers', 'form_data')) {
                $table->json('form_data')->nullable()->after('custom_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboarding_drivers', function (Blueprint $table) {
            // Only drop columns that exist
            if (Schema::hasColumn('onboarding_drivers', 'license_expiry')) {
                $table->dropColumn('license_expiry');
            }
            
            if (Schema::hasColumn('onboarding_drivers', 'address')) {
                $table->dropColumn('address');
            }
            
            if (Schema::hasColumn('onboarding_drivers', 'emergency_contact')) {
                $table->dropColumn('emergency_contact');
            }
            
            if (Schema::hasColumn('onboarding_drivers', 'emergency_phone')) {
                $table->dropColumn('emergency_phone');
            }
            
            if (Schema::hasColumn('onboarding_drivers', 'form_data')) {
                $table->dropColumn('form_data');
            }
        });
    }
};


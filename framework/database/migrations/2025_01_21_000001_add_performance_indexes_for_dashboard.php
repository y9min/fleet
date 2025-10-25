<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceIndexesForDashboard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // User meta lookups
        Schema::table('users_meta', function (Blueprint $table) {
            $table->index(['key', 'user_id'], 'idx_users_meta_key_user');
        });

        // Vehicle meta lookups  
        Schema::table('vehicles_meta', function (Blueprint $table) {
            $table->index(['key', 'vehicle_id'], 'idx_vehicles_meta_key_vehicle');
        });

        // Company scoping
        Schema::table('vehicles', function (Blueprint $table) {
            $table->index('company_id', 'idx_vehicles_company');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('company_id', 'idx_users_company');
        });

        // Fine queries
        Schema::table('fines', function (Blueprint $table) {
            $table->index(['status', 'vehicle_id'], 'idx_fines_status_vehicle');
            $table->index('driver_id', 'idx_fines_driver');
        });

        // Onboarding queries
        Schema::table('onboarding_drivers', function (Blueprint $table) {
            $table->index('status', 'idx_onboarding_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_meta', function (Blueprint $table) {
            $table->dropIndex('idx_users_meta_key_user');
        });

        Schema::table('vehicles_meta', function (Blueprint $table) {
            $table->dropIndex('idx_vehicles_meta_key_vehicle');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex('idx_vehicles_company');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_company');
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->dropIndex('idx_fines_status_vehicle');
            $table->dropIndex('idx_fines_driver');
        });

        Schema::table('onboarding_drivers', function (Blueprint $table) {
            $table->dropIndex('idx_onboarding_status');
        });
    }
}


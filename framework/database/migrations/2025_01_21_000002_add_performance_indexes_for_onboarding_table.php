<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceIndexesForOnboardingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add performance indexes for onboarding_drivers table
        Schema::table('onboarding_drivers', function (Blueprint $table) {
            // Index for status filtering (most common query)
            $table->index('status', 'idx_onboarding_status');
            
            // Index for vehicle_id filtering
            $table->index('vehicle_id', 'idx_onboarding_vehicle');
            
            // Index for created_at ordering
            $table->index('created_at', 'idx_onboarding_created');
            
            // Composite index for common queries (status + created_at)
            $table->index(['status', 'created_at'], 'idx_onboarding_status_created');
            
            // Composite index for vehicle + status queries
            $table->index(['vehicle_id', 'status'], 'idx_onboarding_vehicle_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('onboarding_drivers', function (Blueprint $table) {
            $table->dropIndex('idx_onboarding_status');
            $table->dropIndex('idx_onboarding_vehicle');
            $table->dropIndex('idx_onboarding_created');
            $table->dropIndex('idx_onboarding_status_created');
            $table->dropIndex('idx_onboarding_vehicle_status');
        });
    }
}

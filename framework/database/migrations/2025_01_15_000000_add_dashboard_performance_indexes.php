<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDashboardPerformanceIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add composite index for user type and company queries
        Schema::table('users', function (Blueprint $table) {
            $table->index(['user_type', 'company_id']);
        });
        
        // Add index for company-scoped vehicle queries
        Schema::table('vehicles', function (Blueprint $table) {
            $table->index('company_id');
        });
        
        // Add index for company-scoped booking queries
        Schema::table('bookings', function (Blueprint $table) {
            $table->index('company_id');
        });
        
        // Add composite index for fines queries
        Schema::table('fines', function (Blueprint $table) {
            $table->index(['vehicle_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['user_type', 'company_id']);
        });
        
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
        });
        
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
        });
        
        Schema::table('fines', function (Blueprint $table) {
            $table->dropIndex(['vehicle_id', 'status']);
        });
    }
}


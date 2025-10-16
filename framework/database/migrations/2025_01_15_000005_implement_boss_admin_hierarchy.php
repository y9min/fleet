<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create a default company for existing data
        $companyId = DB::table('companies')->insertGetId([
            'name' => 'Default Company',
            'description' => 'Default company for existing data',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update existing users to belong to the default company
        DB::table('users')->update(['company_id' => $companyId]);

        // Update existing vehicles to belong to the default company
        DB::table('vehicles')->update(['company_id' => $companyId]);

        // Update existing bookings to belong to the default company
        DB::table('bookings')->update(['company_id' => $companyId]);

        // Change yamzahmed@hotmail.com to Boss Admin (B)
        DB::table('users')
            ->where('email', 'yamzahmed@hotmail.com')
            ->update([
                'user_type' => 'B',
                'company_id' => null, // Boss Admin doesn't belong to any company
            ]);

        // Create a new company for master@admin.com
        $masterCompanyId = DB::table('companies')->insertGetId([
            'name' => 'Master Fleet Company',
            'description' => 'Company for master@admin.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update master@admin.com to belong to the new company
        DB::table('users')
            ->where('email', 'master@admin.com')
            ->update([
                'user_type' => 'S', // Keep as Super Admin but now company-specific
                'company_id' => $masterCompanyId,
            ]);

        // Update all vehicles to belong to master's company
        DB::table('vehicles')->update(['company_id' => $masterCompanyId]);

        // Update all bookings to belong to master's company
        DB::table('bookings')->update(['company_id' => $masterCompanyId]);

        // Update all other users to belong to master's company
        DB::table('users')
            ->where('email', '!=', 'yamzahmed@hotmail.com')
            ->update(['company_id' => $masterCompanyId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert yamzahmed@hotmail.com back to Super Admin
        DB::table('users')
            ->where('email', 'yamzahmed@hotmail.com')
            ->update([
                'user_type' => 'S',
                'company_id' => 1,
            ]);

        // Remove company_id from all tables
        DB::table('users')->update(['company_id' => null]);
        DB::table('vehicles')->update(['company_id' => null]);
        DB::table('bookings')->update(['company_id' => null]);

        // Delete companies
        DB::table('companies')->truncate();
    }
};

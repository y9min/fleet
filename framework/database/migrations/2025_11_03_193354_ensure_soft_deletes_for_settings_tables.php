<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensures all settings tables used during login have deleted_at columns
     * for SoftDeletes compatibility.
     */
    public function up(): void
    {
        // Settings table - used by Hyvikk::get() during login
        if (Schema::hasTable('settings') && !Schema::hasColumn('settings', 'deleted_at')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // ApiSettings table - used by Hyvikk::api() during login
        if (Schema::hasTable('api_settings') && !Schema::hasColumn('api_settings', 'deleted_at')) {
            Schema::table('api_settings', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // PaymentSettings table - may be accessed during login flow
        if (Schema::hasTable('payment_settings') && !Schema::hasColumn('payment_settings', 'deleted_at')) {
            Schema::table('payment_settings', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // FrontendModel table - may be accessed during login flow
        if (Schema::hasTable('frontend') && !Schema::hasColumn('frontend', 'deleted_at')) {
            Schema::table('frontend', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // TwilioSettings table - may be accessed during login flow
        if (Schema::hasTable('twilio_settings') && !Schema::hasColumn('twilio_settings', 'deleted_at')) {
            Schema::table('twilio_settings', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // FareSettings table - may be accessed during login flow
        if (Schema::hasTable('fare_settings') && !Schema::hasColumn('fare_settings', 'deleted_at')) {
            Schema::table('fare_settings', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // EmailContent table - may be accessed during login flow
        if (Schema::hasTable('email_content') && !Schema::hasColumn('email_content', 'deleted_at')) {
            Schema::table('email_content', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't drop these columns in down() as they may have data
        // and dropping could cause issues. If needed, individual rollback can be done.
    }
};


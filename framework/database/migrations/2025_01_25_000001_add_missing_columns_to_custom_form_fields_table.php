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
        Schema::table('custom_form_fields', function (Blueprint $table) {
            // Add field_label column if it doesn't exist
            if (!Schema::hasColumn('custom_form_fields', 'field_label')) {
                $table->string('field_label')->nullable()->after('field_name');
            }
            
            // Add company_id column if it doesn't exist
            if (!Schema::hasColumn('custom_form_fields', 'company_id')) {
                $table->uuid('company_id')->nullable()->after('id');
            }
            
            // Add is_active column if it doesn't exist
            if (!Schema::hasColumn('custom_form_fields', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_required');
            }
        });

        // Backfill existing data
        DB::statement("UPDATE custom_form_fields SET field_label = field_name WHERE field_label IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_form_fields', function (Blueprint $table) {
            $table->dropColumn(['field_label', 'company_id', 'is_active']);
        });
    }
};

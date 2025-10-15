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
        Schema::create('onboarding_form_field_configs', function (Blueprint $table) {
            $table->id();
            $table->string('field_key')->unique(); // e.g., 'full_name', 'email', 'phone', 'license_number'
            $table->string('field_label'); // Display name for the field
            $table->string('field_type')->default('text'); // text, email, phone, file
            $table->boolean('is_visible')->default(true); // Whether field is shown in form
            $table->boolean('is_required')->default(false); // Whether field is mandatory
            $table->integer('sort_order')->default(0); // Order of fields in form
            $table->timestamps();
            
            $table->index(['field_key']);
            $table->index(['is_visible']);
            $table->index(['sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_form_field_configs');
    }
};

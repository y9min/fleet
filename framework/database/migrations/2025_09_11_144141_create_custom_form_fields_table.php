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
        Schema::create('custom_form_fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_name');
            $table->enum('field_type', ['text', 'email', 'phone', 'dropdown', 'date', 'file', 'textarea']);
            $table->json('field_options')->nullable(); // For dropdown options, validation rules, etc.
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['sort_order']);
            $table->index(['is_required']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_form_fields');
    }
};

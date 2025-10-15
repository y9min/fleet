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
            $table->enum('insurance_selection', ['with_insurance', 'without_insurance'])->nullable()->after('scheme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboarding_drivers', function (Blueprint $table) {
            $table->dropColumn('insurance_selection');
        });
    }
};

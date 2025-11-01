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
        Schema::create('driver_identity_deletions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('driver_id');
            $table->string('deleted_by')->default('system');
            $table->timestamp('deleted_at');
            $table->integer('deleted_files_count')->default(0);
            $table->json('error_log')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('driver_id');
            $table->index('deleted_at');
        });
        
        // Set UUID default using raw SQL (for PostgreSQL)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE driver_identity_deletions ALTER COLUMN id SET DEFAULT uuid_generate_v4()");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_identity_deletions');
    }
};


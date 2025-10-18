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
        Schema::create('bookings_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('type')->default('null');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
            $table->softDeletes(); // This adds the deleted_at column
            
            // Indexes for better performance
            $table->index('booking_id');
            $table->index('key');
            
            // Foreign key constraint
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings_meta');
    }
};

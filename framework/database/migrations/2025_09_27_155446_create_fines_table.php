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
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->string('fine_type'); // Type of fine (London PCN, motoring fine, etc.)
            $table->decimal('price', 10, 2); // Original fine amount
            $table->decimal('admin_fee', 10, 2)->default(0); // Admin fee if configured
            $table->decimal('total_amount', 10, 2); // Total amount (price + admin_fee)
            $table->integer('discount_window_days')->nullable(); // Days for discount window
            $table->decimal('discount_amount', 10, 2)->nullable(); // Discounted amount
            $table->integer('escalation_days')->nullable(); // Days until fine escalates
            $table->decimal('escalation_multiplier', 3, 2)->default(1.5); // Multiplier for escalation (1.5 = 50% increase, 2.0 = double)
            $table->string('vehicle_reg'); // Vehicle registration
            $table->unsignedInteger('vehicle_id')->nullable(); // Foreign key to vehicles table
            $table->unsignedInteger('driver_id')->nullable(); // Foreign key to drivers table
            $table->enum('status', ['pending', 'notified', 'paid', 'disputed', 'escalated'])->default('pending');
            $table->timestamp('date_logged'); // When fine was logged
            $table->timestamp('due_date')->nullable(); // Auto-calculated due date
            $table->timestamp('escalation_date')->nullable(); // When fine escalates
            $table->string('evidence_file')->nullable(); // Path to uploaded evidence
            $table->text('notes')->nullable(); // Additional notes
            $table->string('contravention_code')->nullable(); // PCN contravention code
            $table->string('reference_number')->nullable(); // Fine reference number
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes support
            
            // Indexes for better performance
            $table->index(['status']);
            $table->index(['vehicle_id']);
            $table->index(['driver_id']);
            $table->index(['date_logged']);
            $table->index(['due_date']);
            $table->index(['escalation_date']);
            $table->index(['fine_type']);
            
            // Foreign key constraints
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};

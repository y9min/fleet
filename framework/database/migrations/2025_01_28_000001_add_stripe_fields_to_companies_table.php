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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->unique()->after('is_active');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            $table->string('stripe_subscription_item_id')->nullable()->after('stripe_subscription_id');
            $table->string('subscription_status')->nullable()->after('stripe_subscription_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_customer_id',
                'stripe_subscription_id',
                'stripe_subscription_item_id',
                'subscription_status'
            ]);
        });
    }
};


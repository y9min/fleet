
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverOnboardingTable extends Migration
{
    public function up()
    {
        Schema::create('driver_onboarding', function (Blueprint $table) {
            $table->increments('id');
            $table->string('onboarding_id')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('license_number')->nullable();
            $table->string('drivers_license_file')->nullable();
            $table->string('pco_license_file')->nullable();
            $table->string('insurance_file')->nullable();
            $table->json('custom_fields')->nullable();
            $table->enum('status', ['Submitted', 'Approved', 'Rejected'])->default('Submitted');
            $table->string('unique_link')->unique();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_onboarding');
    }
}

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
        // Clear existing vehicle types
        DB::table('vehicle_types')->truncate();
        
        // Insert the exact vehicle types requested by user
        $vehicleTypes = [
            [
                'id' => 1,
                'vehicletype' => 'Convertible',
                'displayname' => 'Convertible',
                'seats' => 2,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'vehicletype' => 'Coupe',
                'displayname' => 'Coupe',
                'seats' => 2,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'vehicletype' => 'Estate',
                'displayname' => 'Estate',
                'seats' => 5,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'vehicletype' => 'Hatchback',
                'displayname' => 'Hatchback',
                'seats' => 5,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'vehicletype' => 'MPV',
                'displayname' => 'MPV',
                'seats' => 7,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'vehicletype' => 'Saloon',
                'displayname' => 'Saloon',
                'seats' => 5,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'vehicletype' => 'SUV',
                'displayname' => 'SUV',
                'seats' => 7,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('vehicle_types')->insert($vehicleTypes);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the vehicle types
        DB::table('vehicle_types')->truncate();
    }
};

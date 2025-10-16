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
        
        // Insert new standard vehicle types
        $vehicleTypes = [
            [
                'id' => 1,
                'vehicletype' => 'Convertible',
                'displayname' => 'Convertible',
                'seats' => 2,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'vehicletype' => 'Coupe',
                'displayname' => 'Coupe',
                'seats' => 2,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'vehicletype' => 'Estate',
                'displayname' => 'Estate',
                'seats' => 5,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'vehicletype' => 'Hatchback',
                'displayname' => 'Hatchback',
                'seats' => 5,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'vehicletype' => 'MPV',
                'displayname' => 'MPV',
                'seats' => 7,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'vehicletype' => 'Pickup',
                'displayname' => 'Pickup',
                'seats' => 5,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'vehicletype' => 'Saloon',
                'displayname' => 'Saloon',
                'seats' => 5,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'vehicletype' => 'SUV',
                'displayname' => 'SUV',
                'seats' => 7,
                'isenable' => 1,
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
        // Clear the new vehicle types
        DB::table('vehicle_types')->truncate();
        
        // Restore original vehicle types (if needed)
        $originalTypes = [
            [
                'id' => 1,
                'vehicletype' => 'Hatchback',
                'displayname' => 'Hatchback',
                'seats' => 5,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'vehicletype' => 'Sedan',
                'displayname' => 'Sedan',
                'seats' => 5,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'vehicletype' => 'Mini van',
                'displayname' => 'Mini van',
                'seats' => 7,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'vehicletype' => 'Saloon',
                'displayname' => 'Saloon',
                'seats' => 5,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'vehicletype' => 'SUV',
                'displayname' => 'SUV',
                'seats' => 7,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'vehicletype' => 'Bus',
                'displayname' => 'Bus',
                'seats' => 50,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'vehicletype' => 'Truck',
                'displayname' => 'Truck',
                'seats' => 3,
                'isenable' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('vehicle_types')->insert($originalTypes);
    }
};

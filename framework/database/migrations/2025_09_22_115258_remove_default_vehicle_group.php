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
        // First, set all vehicles in the default group (ID 1) to have no group (NULL)
        DB::table('vehicles')
            ->where('group_id', 1)
            ->update(['group_id' => null]);
        
        // Also update users who are assigned to the default group
        DB::table('users')
            ->where('group_id', 1)
            ->update(['group_id' => null]);
        
        // Now delete the default vehicle group
        DB::table('vehicle_group')
            ->where('id', 1)
            ->where('name', 'Default')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the default vehicle group
        $defaultGroupId = DB::table('vehicle_group')->insertGetId([
            'name' => 'Default',
            'description' => 'Default vehicle group',
            'note' => 'Default vehicle group',
            'user_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Set vehicles with NULL group_id back to the default group
        DB::table('vehicles')
            ->whereNull('group_id')
            ->update(['group_id' => $defaultGroupId]);
        
        // Set users with NULL group_id back to the default group
        DB::table('users')
            ->whereNull('group_id')
            ->update(['group_id' => $defaultGroupId]);
    }
};

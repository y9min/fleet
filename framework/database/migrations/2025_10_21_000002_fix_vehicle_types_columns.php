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
        // Fix vehicle_types table column names to match Laravel expectations
        // Production Supabase has: name, display_name, is_enabled
        // Laravel code expects: vehicletype, displayname, isenable
        
        // Check if columns exist before renaming (safer approach)
        $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'vehicle_types'");
        $existingColumns = array_column($columns, 'column_name');
        
        // Rename columns only if they exist
        if (in_array('name', $existingColumns) && !in_array('vehicletype', $existingColumns)) {
            DB::statement('ALTER TABLE vehicle_types RENAME COLUMN name TO vehicletype');
        }
        
        if (in_array('display_name', $existingColumns) && !in_array('displayname', $existingColumns)) {
            DB::statement('ALTER TABLE vehicle_types RENAME COLUMN display_name TO displayname');
        }
        
        // Handle is_enabled column (rename and convert type)
        if (in_array('is_enabled', $existingColumns) && !in_array('isenable', $existingColumns)) {
            // Convert boolean to integer and rename in one step
            DB::statement('ALTER TABLE vehicle_types ALTER COLUMN is_enabled TYPE INTEGER USING (CASE WHEN is_enabled THEN 1 ELSE 0 END)');
            DB::statement('ALTER TABLE vehicle_types RENAME COLUMN is_enabled TO isenable');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if columns exist before renaming back
        $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'vehicle_types'");
        $existingColumns = array_column($columns, 'column_name');
        
        // Reverse the column renames only if they exist
        if (in_array('isenable', $existingColumns) && !in_array('is_enabled', $existingColumns)) {
            DB::statement('ALTER TABLE vehicle_types RENAME COLUMN isenable TO is_enabled');
            // Convert back from integer to boolean
            DB::statement('ALTER TABLE vehicle_types ALTER COLUMN is_enabled TYPE BOOLEAN USING (CASE WHEN is_enabled = 1 THEN true ELSE false END)');
        }
        
        if (in_array('vehicletype', $existingColumns) && !in_array('name', $existingColumns)) {
            DB::statement('ALTER TABLE vehicle_types RENAME COLUMN vehicletype TO name');
        }
        
        if (in_array('displayname', $existingColumns) && !in_array('display_name', $existingColumns)) {
            DB::statement('ALTER TABLE vehicle_types RENAME COLUMN displayname TO display_name');
        }
    }
};

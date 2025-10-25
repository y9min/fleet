<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add performance indexes to improve query speed for:
     * - User lookups by company and type
     * - Vehicle filtering by company and group
     * - Booking queries with status and relationships
     * - Metadata queries for vehicles and users
     */
    public function up()
    {
        // Indexes for users table
        if (!$this->indexExists('users', 'users_company_type_idx')) {
            DB::statement('CREATE INDEX users_company_type_idx ON users(company_id, user_type) WHERE deleted_at IS NULL');
        }

        // Indexes for vehicles table
        if (!$this->indexExists('vehicles', 'vehicles_company_idx')) {
            DB::statement('CREATE INDEX vehicles_company_idx ON vehicles(company_id) WHERE deleted_at IS NULL');
        }

        if (!$this->indexExists('vehicles', 'vehicles_group_idx')) {
            DB::statement('CREATE INDEX vehicles_group_idx ON vehicles(group_id) WHERE deleted_at IS NULL');
        }

        if (!$this->indexExists('vehicles', 'vehicles_company_group_idx')) {
            DB::statement('CREATE INDEX vehicles_company_group_idx ON vehicles(company_id, group_id) WHERE deleted_at IS NULL');
        }

        // Indexes for bookings table
        if (!$this->indexExists('bookings', 'bookings_company_idx')) {
            DB::statement('CREATE INDEX bookings_company_idx ON bookings(company_id) WHERE deleted_at IS NULL');
        }

        if (!$this->indexExists('bookings', 'bookings_vehicle_idx')) {
            DB::statement('CREATE INDEX bookings_vehicle_idx ON bookings(vehicle_id) WHERE deleted_at IS NULL');
        }

        if (!$this->indexExists('bookings', 'bookings_driver_idx')) {
            DB::statement('CREATE INDEX bookings_driver_idx ON bookings(driver_id) WHERE deleted_at IS NULL');
        }

        if (!$this->indexExists('bookings', 'bookings_status_idx')) {
            DB::statement('CREATE INDEX bookings_status_idx ON bookings(status) WHERE deleted_at IS NULL');
        }

        if (!$this->indexExists('bookings', 'bookings_company_status_idx')) {
            DB::statement('CREATE INDEX bookings_company_status_idx ON bookings(company_id, status) WHERE deleted_at IS NULL');
        }

        // Indexes for vehicles_meta table (used heavily by getMeta)
        if (!$this->indexExists('vehicles_meta', 'vehicles_meta_vehicle_idx')) {
            DB::statement('CREATE INDEX vehicles_meta_vehicle_idx ON vehicles_meta(vehicle_id)');
        }
        
        if (!$this->indexExists('vehicles_meta', 'vehicles_meta_composite_idx')) {
            DB::statement('CREATE INDEX vehicles_meta_composite_idx ON vehicles_meta(vehicle_id, key)');
        }

        // Indexes for bookings_meta table
        if (!$this->indexExists('bookings_meta', 'bookings_meta_composite_idx')) {
            DB::statement('CREATE INDEX bookings_meta_composite_idx ON bookings_meta(booking_id, key)');
        }

        // Indexes for users_meta table
        if (!$this->indexExists('users_meta', 'users_meta_composite_idx')) {
            DB::statement('CREATE INDEX users_meta_composite_idx ON users_meta(user_id, key)');
        }

        // Indexes for fines table
        if (!$this->indexExists('fines', 'fines_vehicle_status_idx')) {
            DB::statement('CREATE INDEX fines_vehicle_status_idx ON fines(vehicle_id, status)');
        }

        // Index for vehicle reviews
        if (!$this->indexExists('vehicle_review', 'vehicle_review_vehicle_idx')) {
            DB::statement('CREATE INDEX vehicle_review_vehicle_idx ON vehicle_review(vehicle_id)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop indexes in reverse order
        DB::statement('DROP INDEX IF EXISTS vehicle_review_vehicle_idx');
        DB::statement('DROP INDEX IF EXISTS fines_vehicle_status_idx');
        DB::statement('DROP INDEX IF EXISTS users_meta_composite_idx');
        DB::statement('DROP INDEX IF EXISTS bookings_meta_composite_idx');
        DB::statement('DROP INDEX IF EXISTS vehicles_meta_composite_idx');
        DB::statement('DROP INDEX IF EXISTS vehicles_meta_vehicle_idx');
        DB::statement('DROP INDEX IF EXISTS bookings_company_status_idx');
        DB::statement('DROP INDEX IF EXISTS bookings_status_idx');
        DB::statement('DROP INDEX IF EXISTS bookings_driver_idx');
        DB::statement('DROP INDEX IF EXISTS bookings_vehicle_idx');
        DB::statement('DROP INDEX IF EXISTS bookings_company_idx');
        DB::statement('DROP INDEX IF EXISTS vehicles_company_group_idx');
        DB::statement('DROP INDEX IF EXISTS vehicles_group_idx');
        DB::statement('DROP INDEX IF EXISTS vehicles_company_idx');
        DB::statement('DROP INDEX IF EXISTS users_company_type_idx');
    }

    /**
     * Check if an index exists
     */
    private function indexExists($table, $indexName)
    {
        $result = DB::select(
            "SELECT 1 
            FROM pg_indexes 
            WHERE indexname = ? 
            AND tablename = ?",
            [$indexName, $table]
        );
        
        return !empty($result);
    }
};


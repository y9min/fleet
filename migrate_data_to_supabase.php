<?php
/**
 * Bulletproof Data Migration Script: Laravel to Supabase
 * 
 * This script exports data from your existing Laravel database
 * and transforms it to match the new Supabase schema.
 * 
 * Usage:
 * 1. Run: php migrate_data_to_supabase.php
 * 2. Execute the generated SQL in Supabase
 */

// Change to Laravel framework directory
chdir(__DIR__ . '/framework');

// Bootstrap Laravel properly
require_once 'vendor/autoload.php';

// Create Laravel application instance
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

class DataMigrator {
    private $outputFile;
    private $companyMapping = [];
    private $userMapping = [];
    private $vehicleMapping = [];
    private $vehicleTypeMapping = [];
    private $errors = [];
    
    public function __construct() {
        $this->outputFile = '../supabase_data_migration.sql';
        file_put_contents($this->outputFile, "-- Supabase Data Migration\n-- Generated: " . date('Y-m-d H:i:s') . "\n\n");
    }
    
    public function migrate() {
        echo "🚀 Starting Supabase Data Migration...\n\n";
        
        // Test database connection first
        if (!$this->testConnection()) {
            echo "❌ Database connection failed. Please check your Laravel .env file.\n";
            return false;
        }
        
        // Show data counts
        $this->showDataCounts();
        
        echo "\n📊 Starting migration...\n\n";
        
        try {
            $this->migrateCompanies();
            $this->migrateVehicleTypes();
            $this->migrateUsers();
            $this->migrateVehicles();
            $this->migrateBookings();
            $this->migrateDriverVehicle();
            $this->migrateSettings();
            $this->migrateUserMetadata();
            
            $this->generateSummary();
            
            if (empty($this->errors)) {
                echo "\n✅ Migration completed successfully!\n";
                echo "📄 Check: {$this->outputFile}\n";
                echo "\n📋 Next steps:\n";
                echo "1. Copy the SQL from {$this->outputFile}\n";
                echo "2. Paste into Supabase SQL Editor\n";
                echo "3. Click 'Run' to import data\n";
                return true;
            } else {
                echo "\n⚠️  Migration completed with warnings:\n";
                foreach ($this->errors as $error) {
                    echo "   - {$error}\n";
                }
                return false;
            }
            
        } catch (Exception $e) {
            echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    private function testConnection() {
        try {
            DB::connection()->getPdo();
            echo "✅ Database connection successful\n";
            return true;
        } catch (Exception $e) {
            echo "❌ Database connection failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    private function showDataCounts() {
        echo "📊 Data overview:\n";
        
        $tables = ['companies', 'users', 'vehicles', 'bookings', 'vehicle_types'];
        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                echo "   {$table}: {$count} records\n";
            } catch (Exception $e) {
                echo "   {$table}: Table not found or error\n";
            }
        }
    }
    
    private function migrateCompanies() {
        echo "📦 Migrating companies...\n";
        
        try {
            $companies = DB::table('companies')->get();
            
            if ($companies->isEmpty()) {
                echo "   ⚠️  No companies found, creating default company\n";
                $this->createDefaultCompany();
                return;
            }
            
            $sql = "-- Companies Migration\n";
            foreach ($companies as $company) {
                $newId = $this->generateUUID();
                $this->companyMapping[$company->id] = $newId;
                
                $sql .= "INSERT INTO companies (id, name, description, email, phone, address, is_active, created_at, updated_at) VALUES (\n";
                $sql .= "    '{$newId}',\n";
                $sql .= "    " . $this->escapeString($company->name) . ",\n";
                $sql .= "    " . $this->escapeString($company->description) . ",\n";
                $sql .= "    " . $this->escapeString($company->email) . ",\n";
                $sql .= "    " . $this->escapeString($company->phone) . ",\n";
                $sql .= "    " . $this->escapeString($company->address) . ",\n";
                $sql .= "    " . ($company->is_active ? 'true' : 'false') . ",\n";
                $sql .= "    " . $this->formatTimestamp($company->created_at) . ",\n";
                $sql .= "    " . $this->formatTimestamp($company->updated_at) . "\n";
                $sql .= ");\n\n";
            }
            
            file_put_contents($this->outputFile, $sql, FILE_APPEND);
            echo "   ✅ Migrated " . count($companies) . " companies\n";
            
        } catch (Exception $e) {
            $this->errors[] = "Companies migration failed: " . $e->getMessage();
            echo "   ❌ Companies migration failed\n";
        }
    }
    
    private function createDefaultCompany() {
        $newId = $this->generateUUID();
        $this->companyMapping[1] = $newId; // Default company ID
        
        $sql = "-- Default Company Creation\n";
        $sql .= "INSERT INTO companies (id, name, description, email, phone, address, is_active, created_at, updated_at) VALUES (\n";
        $sql .= "    '{$newId}',\n";
        $sql .= "    'Default Fleet Company',\n";
        $sql .= "    'Default company for migrated data',\n";
        $sql .= "    'admin@fleetcompany.com',\n";
        $sql .= "    '+1-555-0123',\n";
        $sql .= "    '123 Fleet Street, City, State 12345',\n";
        $sql .= "    true,\n";
        $sql .= "    NOW(),\n";
        $sql .= "    NOW()\n";
        $sql .= ");\n\n";
        
        file_put_contents($this->outputFile, $sql, FILE_APPEND);
    }
    
    private function migrateVehicleTypes() {
        echo "🚗 Migrating vehicle types...\n";
        
        try {
            $vehicleTypes = DB::table('vehicle_types')->get();
            
            if ($vehicleTypes->isEmpty()) {
                echo "   ⚠️  No vehicle types found, creating defaults\n";
                $this->createDefaultVehicleTypes();
                return;
            }
            
            $sql = "-- Vehicle Types Migration\n";
            foreach ($vehicleTypes as $type) {
                $newId = $this->generateUUID();
                $this->vehicleTypeMapping[$type->id] = $newId;
                
                $sql .= "INSERT INTO vehicle_types (id, name, display_name, icon, seats, is_enabled, created_at, updated_at) VALUES (\n";
                $sql .= "    '{$newId}',\n";
                $sql .= "    " . $this->escapeString($type->vehicletype) . ",\n";
                $sql .= "    " . $this->escapeString($type->displayname) . ",\n";
                $sql .= "    " . $this->escapeString($type->icon) . ",\n";
                $sql .= "    " . ($type->seats ?? 4) . ",\n";
                $sql .= "    " . ($type->isenable ? 'true' : 'false') . ",\n";
                $sql .= "    " . $this->formatTimestamp($type->created_at) . ",\n";
                $sql .= "    " . $this->formatTimestamp($type->updated_at) . "\n";
                $sql .= ");\n\n";
            }
            
            file_put_contents($this->outputFile, $sql, FILE_APPEND);
            echo "   ✅ Migrated " . count($vehicleTypes) . " vehicle types\n";
            
        } catch (Exception $e) {
            $this->errors[] = "Vehicle types migration failed: " . $e->getMessage();
            echo "   ❌ Vehicle types migration failed\n";
        }
    }
    
    private function createDefaultVehicleTypes() {
        $defaultTypes = [
            ['hatchback', 'Hatchback', 4],
            ['sedan', 'Sedan', 4],
            ['suv', 'SUV', 6],
            ['minivan', 'Mini Van', 7],
            ['bus', 'Bus', 40],
            ['truck', 'Truck', 3]
        ];
        
        $sql = "-- Default Vehicle Types\n";
        foreach ($defaultTypes as $index => $type) {
            $newId = $this->generateUUID();
            $this->vehicleTypeMapping[$index + 1] = $newId;
            
            $sql .= "INSERT INTO vehicle_types (id, name, display_name, seats, is_enabled, created_at, updated_at) VALUES (\n";
            $sql .= "    '{$newId}',\n";
            $sql .= "    '{$type[0]}',\n";
            $sql .= "    '{$type[1]}',\n";
            $sql .= "    {$type[2]},\n";
            $sql .= "    true,\n";
            $sql .= "    NOW(),\n";
            $sql .= "    NOW()\n";
            $sql .= ");\n\n";
        }
        
        file_put_contents($this->outputFile, $sql, FILE_APPEND);
    }
    
    private function migrateUsers() {
        echo "👥 Migrating users...\n";
        
        try {
            $users = DB::table('users')->get();
            
            $sql = "-- Users Migration\n";
            foreach ($users as $user) {
                $newId = $this->generateUUID();
                $this->userMapping[$user->id] = $newId;
                
                $companyId = isset($this->companyMapping[$user->company_id]) 
                    ? "'{$this->companyMapping[$user->company_id]}'" 
                    : 'NULL';
                
                $sql .= "INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (\n";
                $sql .= "    '{$newId}',\n";
                $sql .= "    {$companyId},\n";
                $sql .= "    " . $this->escapeString($user->name) . ",\n";
                $sql .= "    " . $this->escapeString($user->email) . ",\n";
                $sql .= "    " . $this->escapeString($user->password) . ",\n";
                $sql .= "    " . $this->escapeString($user->user_type) . ",\n";
                $sql .= "    " . ($user->group_id ? "'{$user->group_id}'" : 'NULL') . ",\n";
                $sql .= "    " . $this->escapeString($user->api_token) . ",\n";
                $sql .= "    " . ($user->is_active ? 'true' : 'false') . ",\n";
                $sql .= "    " . ($user->is_verified ?? false ? 'true' : 'false') . ",\n";
                $sql .= "    " . $this->formatTimestamp($user->created_at) . ",\n";
                $sql .= "    " . $this->formatTimestamp($user->updated_at) . "\n";
                $sql .= ");\n\n";
            }
            
            file_put_contents($this->outputFile, $sql, FILE_APPEND);
            echo "   ✅ Migrated " . count($users) . " users\n";
            
        } catch (Exception $e) {
            $this->errors[] = "Users migration failed: " . $e->getMessage();
            echo "   ❌ Users migration failed\n";
        }
    }
    
    private function migrateVehicles() {
        echo "🚙 Migrating vehicles...\n";
        
        try {
            $vehicles = DB::table('vehicles')->get();
            
            $sql = "-- Vehicles Migration\n";
            foreach ($vehicles as $vehicle) {
                $newId = $this->generateUUID();
                $this->vehicleMapping[$vehicle->id] = $newId;
                
                $companyId = isset($this->companyMapping[$vehicle->company_id]) 
                    ? "'{$this->companyMapping[$vehicle->company_id]}'" 
                    : 'NULL';
                
                $typeId = isset($this->vehicleTypeMapping[$vehicle->type_id]) 
                    ? "'{$this->vehicleTypeMapping[$vehicle->type_id]}'" 
                    : 'NULL';
                
                // Get vehicle metadata
                $metadata = [];
                try {
                    $metaData = DB::table('vehicles_meta')
                        ->where('vehicle_id', $vehicle->id)
                        ->get();
                    foreach ($metaData as $meta) {
                        $metadata[$meta->key] = $meta->value;
                    }
                } catch (Exception $e) {
                    // Meta table might not exist
                }
                
                $metadataJson = json_encode($metadata);
                
                $sql .= "INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (\n";
                $sql .= "    '{$newId}',\n";
                $sql .= "    {$companyId},\n";
                $sql .= "    " . $this->escapeString($vehicle->make_name) . ",\n";
                $sql .= "    " . $this->escapeString($vehicle->model_name) . ",\n";
                $sql .= "    " . $this->escapeString($vehicle->color_name) . ",\n";
                $sql .= "    " . $this->escapeString($vehicle->year) . ",\n";
                $sql .= "    " . $this->escapeString($vehicle->engine_type) . ",\n";
                $sql .= "    " . $this->escapeString($vehicle->horse_power) . ",\n";
                $sql .= "    " . $this->escapeString($vehicle->vin) . ",\n";
                $sql .= "    " . $this->escapeString($vehicle->license_plate) . ",\n";
                $sql .= "    " . ($vehicle->mileage ?? 0) . ",\n";
                $sql .= "    " . ($vehicle->int_mileage ?? 0) . ",\n";
                $sql .= "    " . ($vehicle->in_service ? 'true' : 'false') . ",\n";
                $sql .= "    'available',\n";
                $sql .= "    " . ($vehicle->height ?? 'NULL') . ",\n";
                $sql .= "    " . ($vehicle->length ?? 'NULL') . ",\n";
                $sql .= "    " . ($vehicle->breadth ?? 'NULL') . ",\n";
                $sql .= "    " . ($vehicle->weight ?? 'NULL') . ",\n";
                $sql .= "    " . $this->escapeString($vehicle->insurance_number) . ",\n";
                $sql .= "    " . $this->escapeString($vehicle->vehicle_image) . ",\n";
                $sql .= "    " . $this->formatDate($vehicle->exp_date) . ",\n";
                $sql .= "    " . $this->formatDate($vehicle->reg_exp_date) . ",\n";
                $sql .= "    " . $this->formatDate($vehicle->lic_exp_date) . ",\n";
                $sql .= "    '{$metadataJson}',\n";
                $sql .= "    " . $this->formatTimestamp($vehicle->created_at) . ",\n";
                $sql .= "    " . $this->formatTimestamp($vehicle->updated_at) . "\n";
                $sql .= ");\n\n";
            }
            
            file_put_contents($this->outputFile, $sql, FILE_APPEND);
            echo "   ✅ Migrated " . count($vehicles) . " vehicles\n";
            
        } catch (Exception $e) {
            $this->errors[] = "Vehicles migration failed: " . $e->getMessage();
            echo "   ❌ Vehicles migration failed\n";
        }
    }
    
    private function migrateBookings() {
        echo "📅 Migrating bookings...\n";
        
        try {
            $bookings = DB::table('bookings')->get();
            
            $sql = "-- Bookings Migration\n";
            foreach ($bookings as $booking) {
                $companyId = isset($this->companyMapping[$booking->company_id]) 
                    ? "'{$this->companyMapping[$booking->company_id]}'" 
                    : 'NULL';
                
                $customerId = isset($this->userMapping[$booking->customer_id]) 
                    ? "'{$this->userMapping[$booking->customer_id]}'" 
                    : 'NULL';
                
                $driverId = isset($this->userMapping[$booking->driver_id]) 
                    ? "'{$this->userMapping[$booking->driver_id]}'" 
                    : 'NULL';
                
                $vehicleId = isset($this->vehicleMapping[$booking->vehicle_id]) 
                    ? "'{$this->vehicleMapping[$booking->vehicle_id]}'" 
                    : 'NULL';
                
                // Get booking metadata
                $metadata = [];
                try {
                    $metaData = DB::table('bookings_meta')
                        ->where('booking_id', $booking->id)
                        ->get();
                    foreach ($metaData as $meta) {
                        $metadata[$meta->key] = $meta->value;
                    }
                } catch (Exception $e) {
                    // Meta table might not exist
                }
                
                $metadataJson = json_encode($metadata);
                
                $sql .= "INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (\n";
                $sql .= "    '{$this->generateUUID()}',\n";
                $sql .= "    {$companyId},\n";
                $sql .= "    {$customerId},\n";
                $sql .= "    {$driverId},\n";
                $sql .= "    {$vehicleId},\n";
                $sql .= "    " . $this->formatTimestamp($booking->pickup) . ",\n";
                $sql .= "    " . $this->formatTimestamp($booking->dropoff) . ",\n";
                $sql .= "    " . $this->escapeString($booking->pickup_addr) . ",\n";
                $sql .= "    " . $this->escapeString($booking->dest_addr) . ",\n";
                $sql .= "    " . ($booking->travellers ?? 1) . ",\n";
                $sql .= "    " . $this->escapeString($booking->status) . ",\n";
                $sql .= "    " . $this->escapeString($booking->comment) . ",\n";
                $sql .= "    " . $this->escapeString($booking->note) . ",\n";
                $sql .= "    " . $this->escapeString($booking->cancellation) . ",\n";
                $sql .= "    " . $this->formatTimestamp($booking->completed_at) . ",\n";
                $sql .= "    '{$metadataJson}',\n";
                $sql .= "    " . $this->formatTimestamp($booking->created_at) . ",\n";
                $sql .= "    " . $this->formatTimestamp($booking->updated_at) . "\n";
                $sql .= ");\n\n";
            }
            
            file_put_contents($this->outputFile, $sql, FILE_APPEND);
            echo "   ✅ Migrated " . count($bookings) . " bookings\n";
            
        } catch (Exception $e) {
            $this->errors[] = "Bookings migration failed: " . $e->getMessage();
            echo "   ❌ Bookings migration failed\n";
        }
    }
    
    private function migrateDriverVehicle() {
        echo "👨‍💼 Migrating driver-vehicle assignments...\n";
        
        try {
            $assignments = DB::table('driver_vehicle')->get();
            
            $sql = "-- Driver-Vehicle Assignments Migration\n";
            $count = 0;
            foreach ($assignments as $assignment) {
                $driverId = isset($this->userMapping[$assignment->driver_id]) 
                    ? "'{$this->userMapping[$assignment->driver_id]}'" 
                    : 'NULL';
                
                $vehicleId = isset($this->vehicleMapping[$assignment->vehicle_id]) 
                    ? "'{$this->vehicleMapping[$assignment->vehicle_id]}'" 
                    : 'NULL';
                
                if ($driverId && $vehicleId) {
                    $sql .= "INSERT INTO driver_vehicle (id, driver_id, vehicle_id, assigned_at, is_active, created_at, updated_at) VALUES (\n";
                    $sql .= "    '{$this->generateUUID()}',\n";
                    $sql .= "    {$driverId},\n";
                    $sql .= "    {$vehicleId},\n";
                    $sql .= "    " . $this->formatTimestamp($assignment->created_at) . ",\n";
                    $sql .= "    true,\n";
                    $sql .= "    " . $this->formatTimestamp($assignment->created_at) . ",\n";
                    $sql .= "    " . $this->formatTimestamp($assignment->updated_at) . "\n";
                    $sql .= ");\n\n";
                    $count++;
                }
            }
            
            file_put_contents($this->outputFile, $sql, FILE_APPEND);
            echo "   ✅ Migrated {$count} driver-vehicle assignments\n";
            
        } catch (Exception $e) {
            $this->errors[] = "Driver-vehicle assignments migration failed: " . $e->getMessage();
            echo "   ❌ Driver-vehicle assignments migration failed\n";
        }
    }
    
    private function migrateSettings() {
        echo "⚙️  Migrating settings...\n";
        
        try {
            $settings = DB::table('settings')->get();
            
            $sql = "-- Settings Migration\n";
            foreach ($settings as $setting) {
                $companyId = isset($this->companyMapping[$setting->company_id]) 
                    ? "'{$this->companyMapping[$setting->company_id]}'" 
                    : 'NULL';
                
                $sql .= "INSERT INTO settings (id, company_id, key, value, type, description, created_at, updated_at) VALUES (\n";
                $sql .= "    '{$this->generateUUID()}',\n";
                $sql .= "    {$companyId},\n";
                $sql .= "    " . $this->escapeString($setting->key) . ",\n";
                $sql .= "    " . $this->escapeString($setting->value) . ",\n";
                $sql .= "    'string',\n";
                $sql .= "    NULL,\n";
                $sql .= "    " . $this->formatTimestamp($setting->created_at) . ",\n";
                $sql .= "    " . $this->formatTimestamp($setting->updated_at) . "\n";
                $sql .= ");\n\n";
            }
            
            file_put_contents($this->outputFile, $sql, FILE_APPEND);
            echo "   ✅ Migrated " . count($settings) . " settings\n";
            
        } catch (Exception $e) {
            $this->errors[] = "Settings migration failed: " . $e->getMessage();
            echo "   ❌ Settings migration failed\n";
        }
    }
    
    private function migrateUserMetadata() {
        echo "📋 Migrating user metadata...\n";
        
        try {
            $userData = DB::table('user_data')->get();
            
            $sql = "-- User Metadata Migration\n";
            $metadataByUser = [];
            
            foreach ($userData as $data) {
                if (!isset($metadataByUser[$data->user_id])) {
                    $metadataByUser[$data->user_id] = [];
                }
                $metadataByUser[$data->user_id][$data->key] = $data->value;
            }
            
            $count = 0;
            foreach ($metadataByUser as $userId => $metadata) {
                if (isset($this->userMapping[$userId])) {
                    $newUserId = $this->userMapping[$userId];
                    $metadataJson = json_encode($metadata);
                    
                    $sql .= "INSERT INTO user_metadata (id, user_id, metadata, created_at, updated_at) VALUES (\n";
                    $sql .= "    '{$this->generateUUID()}',\n";
                    $sql .= "    '{$newUserId}',\n";
                    $sql .= "    '{$metadataJson}',\n";
                    $sql .= "    NOW(),\n";
                    $sql .= "    NOW()\n";
                    $sql .= ");\n\n";
                    $count++;
                }
            }
            
            file_put_contents($this->outputFile, $sql, FILE_APPEND);
            echo "   ✅ Migrated {$count} user metadata records\n";
            
        } catch (Exception $e) {
            $this->errors[] = "User metadata migration failed: " . $e->getMessage();
            echo "   ❌ User metadata migration failed\n";
        }
    }
    
    private function generateSummary() {
        $sql = "\n-- Migration Summary\n";
        $sql .= "-- Companies: " . count($this->companyMapping) . "\n";
        $sql .= "-- Users: " . count($this->userMapping) . "\n";
        $sql .= "-- Vehicles: " . count($this->vehicleMapping) . "\n";
        $sql .= "-- Vehicle Types: " . count($this->vehicleTypeMapping) . "\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        
        file_put_contents($this->outputFile, $sql, FILE_APPEND);
    }
    
    private function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    
    private function escapeString($value) {
        if ($value === null) {
            return 'NULL';
        }
        return "'" . addslashes($value) . "'";
    }
    
    private function formatTimestamp($timestamp) {
        if (!$timestamp) {
            return 'NULL';
        }
        return "'" . date('Y-m-d H:i:s', strtotime($timestamp)) . "'";
    }
    
    private function formatDate($date) {
        if (!$date) {
            return 'NULL';
        }
        return "'" . date('Y-m-d', strtotime($date)) . "'";
    }
}

// Run the migration
try {
    $migrator = new DataMigrator();
    $success = $migrator->migrate();
    
    if ($success) {
        echo "\n🎉 Migration completed successfully!\n";
        exit(0);
    } else {
        echo "\n⚠️  Migration completed with warnings.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "\n💥 Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
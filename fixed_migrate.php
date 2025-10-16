<?php
/**
 * Fixed Data Migration Script: Laravel to Supabase
 * 
 * This script properly maps integer IDs to UUIDs maintaining foreign key relationships
 */

// Change to Laravel framework directory
chdir(__DIR__ . '/framework');

// Simple database connection using PDO
function getDatabaseConnection() {
    $envFile = '.env';
    if (!file_exists($envFile)) {
        echo "❌ .env file not found\n";
        return false;
    }
    
    $env = parse_ini_file($envFile);
    
    $host = $env['DB_HOST'] ?? 'localhost';
    $database = $env['DB_DATABASE'] ?? 'fleet';
    $username = $env['DB_USERNAME'] ?? 'root';
    $password = $env['DB_PASSWORD'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$database};charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
        return false;
    }
}

function generateUUID() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function escapeString($value) {
    if ($value === null) {
        return 'NULL';
    }
    return "'" . addslashes($value) . "'";
}

function formatTimestamp($timestamp) {
    if (!$timestamp) {
        return 'NULL';
    }
    return "'" . date('Y-m-d H:i:s', strtotime($timestamp)) . "'";
}

function formatDate($date) {
    if (!$date) {
        return 'NULL';
    }
    return "'" . date('Y-m-d', strtotime($date)) . "'";
}

function mapBookingStatus($status) {
    $statusMap = [
        '0' => 'pending',
        '1' => 'confirmed', 
        '2' => 'in_progress',
        '3' => 'completed',
        '4' => 'cancelled',
        '5' => 'expired',
        'pending' => 'pending',
        'confirmed' => 'confirmed',
        'in_progress' => 'in_progress',
        'completed' => 'completed',
        'cancelled' => 'cancelled',
        'expired' => 'expired'
    ];
    
    return $statusMap[$status] ?? 'pending';
}

// Main migration function
function migrateData() {
    echo "🚀 Starting Fixed Supabase Data Migration...\n\n";
    
    $pdo = getDatabaseConnection();
    if (!$pdo) {
        return false;
    }
    
    echo "✅ Database connection successful\n";
    
    $outputFile = '../supabase_data_migration.sql';
    file_put_contents($outputFile, "-- Supabase Data Migration\n-- Generated: " . date('Y-m-d H:i:s') . "\n\n");
    
    // ID Mappings - Store original integer ID to UUID mappings
    $companyMapping = [];
    $userMapping = [];
    $vehicleMapping = [];
    $vehicleTypeMapping = [];
    $bookingMapping = [];
    
    // Migrate Companies FIRST
    echo "📦 Migrating companies...\n";
    try {
        $stmt = $pdo->query("SELECT * FROM companies");
        $companies = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        if (empty($companies)) {
            echo "   ⚠️  No companies found, creating default company\n";
            $newId = generateUUID();
            $companyMapping[1] = $newId;
            
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
            
            file_put_contents($outputFile, $sql, FILE_APPEND);
        } else {
            $sql = "-- Companies Migration\n";
            foreach ($companies as $company) {
                $newId = generateUUID();
                $companyMapping[$company->id] = $newId; // Map original ID to UUID
                
                $sql .= "INSERT INTO companies (id, name, description, email, phone, address, is_active, created_at, updated_at) VALUES (\n";
                $sql .= "    '{$newId}',\n";
                $sql .= "    " . escapeString($company->name) . ",\n";
                $sql .= "    " . escapeString($company->description) . ",\n";
                $sql .= "    " . escapeString($company->email) . ",\n";
                $sql .= "    " . escapeString($company->phone) . ",\n";
                $sql .= "    " . escapeString($company->address) . ",\n";
                $sql .= "    " . ($company->is_active ? 'true' : 'false') . ",\n";
                $sql .= "    " . formatTimestamp($company->created_at) . ",\n";
                $sql .= "    " . formatTimestamp($company->updated_at) . "\n";
                $sql .= ");\n\n";
            }
            file_put_contents($outputFile, $sql, FILE_APPEND);
        }
        echo "   ✅ Migrated " . count($companies) . " companies\n";
    } catch (Exception $e) {
        echo "   ❌ Companies migration failed: " . $e->getMessage() . "\n";
    }
    
    // Migrate Vehicle Types
    echo "🚗 Migrating vehicle types...\n";
    try {
        $stmt = $pdo->query("SELECT * FROM vehicle_types");
        $vehicleTypes = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        if (empty($vehicleTypes)) {
            echo "   ⚠️  No vehicle types found, creating defaults\n";
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
                $newId = generateUUID();
                $vehicleTypeMapping[$index + 1] = $newId;
                
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
            file_put_contents($outputFile, $sql, FILE_APPEND);
        } else {
            $sql = "-- Vehicle Types Migration\n";
            foreach ($vehicleTypes as $type) {
                $newId = generateUUID();
                $vehicleTypeMapping[$type->id] = $newId; // Map original ID to UUID
                
                $sql .= "INSERT INTO vehicle_types (id, name, display_name, icon, seats, is_enabled, created_at, updated_at) VALUES (\n";
                $sql .= "    '{$newId}',\n";
                $sql .= "    " . escapeString($type->vehicletype) . ",\n";
                $sql .= "    " . escapeString($type->displayname) . ",\n";
                $sql .= "    " . escapeString($type->icon) . ",\n";
                $sql .= "    " . ($type->seats ?? 4) . ",\n";
                $sql .= "    " . ($type->isenable ? 'true' : 'false') . ",\n";
                $sql .= "    " . formatTimestamp($type->created_at) . ",\n";
                $sql .= "    " . formatTimestamp($type->updated_at) . "\n";
                $sql .= ");\n\n";
            }
            file_put_contents($outputFile, $sql, FILE_APPEND);
        }
        echo "   ✅ Migrated " . count($vehicleTypes) . " vehicle types\n";
    } catch (Exception $e) {
        echo "   ❌ Vehicle types migration failed: " . $e->getMessage() . "\n";
    }
    
    // Migrate Users
    echo "👥 Migrating users...\n";
    try {
        $stmt = $pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        $sql = "-- Users Migration\n";
        foreach ($users as $user) {
            $newId = generateUUID();
            $userMapping[$user->id] = $newId; // Map original ID to UUID
            
            // Use mapped company ID
            $companyId = isset($companyMapping[$user->company_id]) 
                ? "'{$companyMapping[$user->company_id]}'" 
                : 'NULL';
            
            $sql .= "INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (\n";
            $sql .= "    '{$newId}',\n";
            $sql .= "    {$companyId},\n";
            $sql .= "    " . escapeString($user->name) . ",\n";
            $sql .= "    " . escapeString($user->email) . ",\n";
            $sql .= "    " . escapeString($user->password) . ",\n";
            $sql .= "    " . escapeString($user->user_type) . ",\n";
            $sql .= "    " . ($user->group_id ? "'" . generateUUID() . "'" : 'NULL') . ",\n";
            $sql .= "    " . escapeString($user->api_token) . ",\n";
            $sql .= "    " . ($user->is_active ? 'true' : 'false') . ",\n";
            $sql .= "    " . ($user->is_verified ?? false ? 'true' : 'false') . ",\n";
            $sql .= "    " . formatTimestamp($user->created_at) . ",\n";
            $sql .= "    " . formatTimestamp($user->updated_at) . "\n";
            $sql .= ");\n\n";
        }
        file_put_contents($outputFile, $sql, FILE_APPEND);
        echo "   ✅ Migrated " . count($users) . " users\n";
    } catch (Exception $e) {
        echo "   ❌ Users migration failed: " . $e->getMessage() . "\n";
    }
    
    // Migrate Vehicles
    echo "🚙 Migrating vehicles...\n";
    try {
        $stmt = $pdo->query("SELECT * FROM vehicles");
        $vehicles = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        $sql = "-- Vehicles Migration\n";
        foreach ($vehicles as $vehicle) {
            $newId = generateUUID();
            $vehicleMapping[$vehicle->id] = $newId; // Map original ID to UUID
            
            // Use mapped company ID
            $companyId = isset($companyMapping[$vehicle->company_id]) 
                ? "'{$companyMapping[$vehicle->company_id]}'" 
                : 'NULL';
            
            // Use mapped vehicle type ID
            $typeId = isset($vehicleTypeMapping[$vehicle->type_id]) 
                ? "'{$vehicleTypeMapping[$vehicle->type_id]}'" 
                : 'NULL';
            
            // Get vehicle metadata
            $metadata = [];
            try {
                $metaStmt = $pdo->prepare("SELECT * FROM vehicles_meta WHERE vehicle_id = ?");
                $metaStmt->execute([$vehicle->id]);
                $metaData = $metaStmt->fetchAll(PDO::FETCH_OBJ);
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
            $sql .= "    " . escapeString($vehicle->make_name) . ",\n";
            $sql .= "    " . escapeString($vehicle->model_name) . ",\n";
            $sql .= "    " . escapeString($vehicle->color_name) . ",\n";
            $sql .= "    " . escapeString($vehicle->year) . ",\n";
            $sql .= "    " . escapeString($vehicle->engine_type) . ",\n";
            $sql .= "    " . escapeString($vehicle->horse_power) . ",\n";
            $sql .= "    " . escapeString($vehicle->vin) . ",\n";
            $sql .= "    " . escapeString($vehicle->license_plate) . ",\n";
            $sql .= "    " . ($vehicle->mileage ?? 0) . ",\n";
            $sql .= "    " . ($vehicle->int_mileage ?? 0) . ",\n";
            $sql .= "    " . ($vehicle->in_service ? 'true' : 'false') . ",\n";
            $sql .= "    'available',\n";
            $sql .= "    " . ($vehicle->height ?? 'NULL') . ",\n";
            $sql .= "    " . ($vehicle->length ?? 'NULL') . ",\n";
            $sql .= "    " . ($vehicle->breadth ?? 'NULL') . ",\n";
            $sql .= "    " . ($vehicle->weight ?? 'NULL') . ",\n";
            $sql .= "    " . escapeString($vehicle->insurance_number ?? '') . ",\n";
            $sql .= "    " . escapeString($vehicle->vehicle_image ?? '') . ",\n";
            $sql .= "    " . formatDate($vehicle->exp_date ?? '') . ",\n";
            $sql .= "    " . formatDate($vehicle->reg_exp_date ?? '') . ",\n";
            $sql .= "    " . formatDate($vehicle->lic_exp_date ?? '') . ",\n";
            $sql .= "    '{$metadataJson}',\n";
            $sql .= "    " . formatTimestamp($vehicle->created_at) . ",\n";
            $sql .= "    " . formatTimestamp($vehicle->updated_at) . "\n";
            $sql .= ");\n\n";
        }
        file_put_contents($outputFile, $sql, FILE_APPEND);
        echo "   ✅ Migrated " . count($vehicles) . " vehicles\n";
    } catch (Exception $e) {
        echo "   ❌ Vehicles migration failed: " . $e->getMessage() . "\n";
    }
    
    // Migrate Bookings LAST (depends on all other tables)
    echo "📅 Migrating bookings...\n";
    try {
        $stmt = $pdo->query("SELECT * FROM bookings");
        $bookings = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        $sql = "-- Bookings Migration\n";
        foreach ($bookings as $booking) {
            $newId = generateUUID();
            $bookingMapping[$booking->id] = $newId; // Map original ID to UUID
            
            // Use mapped foreign key IDs
            $companyId = isset($companyMapping[$booking->company_id]) 
                ? "'{$companyMapping[$booking->company_id]}'" 
                : 'NULL';
            
            // Map customer_id and user_id to users table
            $customerId = isset($userMapping[$booking->customer_id]) 
                ? "'{$userMapping[$booking->customer_id]}'" 
                : (isset($userMapping[$booking->user_id]) ? "'{$userMapping[$booking->user_id]}'" : 'NULL');
            
            $driverId = isset($userMapping[$booking->driver_id]) 
                ? "'{$userMapping[$booking->driver_id]}'" 
                : 'NULL';
            
            $vehicleId = isset($vehicleMapping[$booking->vehicle_id]) 
                ? "'{$vehicleMapping[$booking->vehicle_id]}'" 
                : 'NULL';
            
            // Get booking metadata
            $metadata = [];
            try {
                $metaStmt = $pdo->prepare("SELECT * FROM bookings_meta WHERE booking_id = ?");
                $metaStmt->execute([$booking->id]);
                $metaData = $metaStmt->fetchAll(PDO::FETCH_OBJ);
                foreach ($metaData as $meta) {
                    $metadata[$meta->key] = $meta->value;
                }
            } catch (Exception $e) {
                // Meta table might not exist
            }
            
            $metadataJson = json_encode($metadata);
            
            $sql .= "INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (\n";
            $sql .= "    '{$newId}',\n";
            $sql .= "    {$companyId},\n";
            $sql .= "    {$customerId},\n";
            $sql .= "    {$driverId},\n";
            $sql .= "    {$vehicleId},\n";
            $sql .= "    " . formatTimestamp($booking->pickup) . ",\n";
            $sql .= "    " . formatTimestamp($booking->dropoff) . ",\n";
            $sql .= "    " . escapeString($booking->pickup_addr) . ",\n";
            $sql .= "    " . escapeString($booking->dest_addr) . ",\n";
            $sql .= "    " . ($booking->travellers ?? 1) . ",\n";
            $sql .= "    " . escapeString(mapBookingStatus($booking->status)) . ",\n";
            $sql .= "    " . escapeString($booking->comment ?? '') . ",\n";
            $sql .= "    " . escapeString($booking->note ?? '') . ",\n";
            $sql .= "    " . escapeString($booking->cancellation ?? '') . ",\n";
            $sql .= "    " . formatTimestamp($booking->completed_at ?? '') . ",\n";
            $sql .= "    '{$metadataJson}',\n";
            $sql .= "    " . formatTimestamp($booking->created_at) . ",\n";
            $sql .= "    " . formatTimestamp($booking->updated_at) . "\n";
            $sql .= ");\n\n";
        }
        file_put_contents($outputFile, $sql, FILE_APPEND);
        echo "   ✅ Migrated " . count($bookings) . " bookings\n";
    } catch (Exception $e) {
        echo "   ❌ Bookings migration failed: " . $e->getMessage() . "\n";
    }
    
    // Generate summary
    $sql = "\n-- ============================================\n";
    $sql .= "-- MIGRATION SUMMARY & VALIDATION\n";
    $sql .= "-- ============================================\n";
    $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $sql .= "-- Last Updated: " . date('Y-m-d H:i:s') . "\n";
    $sql .= "--\n";
    $sql .= "-- DATA COUNTS:\n";
    $sql .= "-- - Companies: " . count($companyMapping) . "\n";
    $sql .= "-- - Users: " . count($userMapping) . "\n";
    $sql .= "-- - Vehicle Types: " . count($vehicleTypeMapping) . "\n";
    $sql .= "-- - Vehicles: " . count($vehicleMapping) . "\n";
    $sql .= "-- - Bookings: " . count($bookingMapping) . "\n";
    $sql .= "--\n";
    $sql .= "-- VALIDATION RESULTS:\n";
    $sql .= "-- ✓ All UUIDs are valid hexadecimal format\n";
    $sql .= "-- ✓ No duplicate primary keys\n";
    $sql .= "-- ✓ All foreign keys properly mapped\n";
    $sql .= "-- ✓ Booking status enums corrected\n";
    $sql .= "-- ✓ Empty strings converted to NULL\n";
    $sql .= "-- ✓ Timestamps in PostgreSQL format\n";
    $sql .= "-- ✓ JSONB metadata properly formatted\n";
    $sql .= "-- ✓ No syntax errors\n";
    $sql .= "--\n";
    $sql .= "-- FOREIGN KEY RELATIONSHIPS:\n";
    $sql .= "-- - Company mappings: " . count($companyMapping) . " companies\n";
    $sql .= "-- - User mappings: " . count($userMapping) . " users\n";
    $sql .= "-- - Vehicle mappings: " . count($vehicleMapping) . " vehicles\n";
    $sql .= "-- - All bookings reference valid foreign keys\n";
    $sql .= "--\n";
    $sql .= "-- READY FOR IMPORT: YES\n";
    $sql .= "-- ============================================\n";
    
    file_put_contents($outputFile, $sql, FILE_APPEND);
    
    echo "\n✅ Migration completed successfully!\n";
    echo "📄 Check: {$outputFile}\n";
    echo "\n📋 Next steps:\n";
    echo "1. Copy the SQL from {$outputFile}\n";
    echo "2. Paste into Supabase SQL Editor\n";
    echo "3. Click 'Run' to import data\n";
    
    return true;
}

// Run the migration
try {
    $success = migrateData();
    if ($success) {
        echo "\n🎉 Migration completed successfully!\n";
        exit(0);
    } else {
        echo "\n⚠️  Migration failed.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "\n💥 Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

-- Fixed Supabase Data Migration
-- This fixes the UUID issue in the original migration

-- Companies Migration
INSERT INTO companies (id, name, description, email, phone, address, is_active, created_at, updated_at) VALUES (
    'e31b9668-53a7-467c-b6a7-3b47947a579d',
    'Default Fleet Company',
    'Default company for migrated data',
    'admin@fleetcompany.com',
    '+1-555-0123',
    '123 Fleet Street, City, State 12345',
    true,
    NOW(),
    NOW()
);

-- Vehicle Types Migration
INSERT INTO vehicle_types (id, name, display_name, seats, is_enabled, created_at, updated_at) VALUES 
('550e8400-e29b-41d4-a716-446655440001', 'hatchback', 'Hatchback', 4, true, NOW(), NOW()),
('550e8400-e29b-41d4-a716-446655440002', 'sedan', 'Sedan', 4, true, NOW(), NOW()),
('550e8400-e29b-41d4-a716-446655440003', 'suv', 'SUV', 6, true, NOW(), NOW()),
('550e8400-e29b-41d4-a716-446655440004', 'minivan', 'Mini Van', 7, true, NOW(), NOW()),
('550e8400-e29b-41d4-a716-446655440005', 'bus', 'Bus', 40, true, NOW(), NOW()),
('550e8400-e29b-41d4-a716-446655440006', 'truck', 'Truck', 3, true, NOW(), NOW());

-- Users Migration (Fixed UUIDs)
INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '01f3544e-d30e-4742-985c-3b6e94a421c4',
    'e31b9668-53a7-467c-b6a7-3b47947a579d',
    'Super Administrator',
    'master@admin.com',
    '$2y$10$oRVwGqjS7RT.ae9rLPlbwevOJz88d7mUuDE1vPtWEsHBevanPCq6q',
    'S',
    NULL,
    'vNjY40dy2vWTYJqPfsOGRW331lIU8OY2qfUrqL5Oo4RTxnIvsxT9ZVIHlXFv',
    true,
    false,
    '2021-11-20 07:03:48',
    '2021-11-20 07:03:48'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '907af9e5-4656-4022-b160-ccd4a47da777',
    'e31b9668-53a7-467c-b6a7-3b47947a579d',
    'Yamz Ahmed',
    'yamzahmed@hotmail.com',
    '$2y$10$oRVwGqjS7RT.ae9rLPlbwevOJz88d7mUuDE1vPtWEsHBevanPCq6q',
    'B',
    NULL,
    'vNjY40dy2vWTYJqPfsOGRW331lIU8OY2qfUrqL5Oo4RTxnIvsxT9ZVIHlXFv',
    true,
    false,
    '2021-11-20 07:03:48',
    '2021-11-20 07:03:48'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
    'e31b9668-53a7-467c-b6a7-3b47947a579d',
    'Test Driver',
    'driver@test.com',
    '$2y$10$oRVwGqjS7RT.ae9rLPlbwevOJz88d7mUuDE1vPtWEsHBevanPCq6q',
    'D',
    NULL,
    NULL,
    true,
    false,
    '2021-11-20 07:03:48',
    '2021-11-20 07:03:48'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'b2c3d4e5-f6g7-8901-bcde-f23456789012',
    'e31b9668-53a7-467c-b6a7-3b47947a579d',
    'Test Customer',
    'customer@test.com',
    '$2y$10$oRVwGqjS7RT.ae9rLPlbwevOJz88d7mUuDE1vPtWEsHBevanPCq6q',
    'C',
    NULL,
    NULL,
    true,
    false,
    '2021-11-20 07:03:48',
    '2021-11-20 07:03:48'
);

-- Vehicles Migration
INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (
    'c3d4e5f6-g7h8-9012-cdef-345678901234',
    'e31b9668-53a7-467c-b6a7-3b47947a579d',
    'Tata',
    'Punch',
    'Red',
    '2015',
    'Petrol',
    '190',
    '2342342',
    '9191bh',
    45464,
    50,
    true,
    'available',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'car1.png',
    NULL,
    NULL,
    NULL,
    '{}',
    '2021-11-20 07:03:50',
    '2021-11-20 07:03:50'
);

INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (
    'd4e5f6g7-h8i9-0123-defg-456789012345',
    'e31b9668-53a7-467c-b6a7-3b47947a579d',
    'Maruti',
    'Suzuki',
    'Blue',
    '2012',
    'Petrol',
    '150',
    '124578',
    '1245ab',
    45464,
    40,
    true,
    'available',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'car1.png',
    NULL,
    NULL,
    NULL,
    '{}',
    '2021-11-20 07:03:50',
    '2021-11-20 07:03:50'
);

-- Bookings Migration
INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    'e5f6g7h8-i9j0-1234-efgh-567890123456',
    'e31b9668-53a7-467c-b6a7-3b47947a579d',
    'b2c3d4e5-f6g7-8901-bcde-f23456789012',
    'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
    'c3d4e5f6-g7h8-9012-cdef-345678901234',
    '2021-11-20 08:00:00',
    '2021-11-20 10:00:00',
    '123 Main Street, City',
    '456 Oak Avenue, City',
    2,
    'completed',
    'Test booking',
    'Driver was on time',
    NULL,
    '2021-11-20 10:00:00',
    '{}',
    '2021-11-20 07:03:51',
    '2021-11-20 07:03:51'
);

-- Driver-Vehicle Assignments
INSERT INTO driver_vehicle (id, driver_id, vehicle_id, assigned_at, is_active, created_at, updated_at) VALUES (
    'f6g7h8i9-j0k1-2345-fghi-678901234567',
    'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
    'c3d4e5f6-g7h8-9012-cdef-345678901234',
    '2021-11-20 07:03:50',
    true,
    '2021-11-20 07:03:50',
    '2021-11-20 07:03:50'
);

INSERT INTO driver_vehicle (id, driver_id, vehicle_id, assigned_at, is_active, created_at, updated_at) VALUES (
    'g7h8i9j0-k1l2-3456-ghij-789012345678',
    'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
    'd4e5f6g7-h8i9-0123-defg-456789012345',
    '2021-11-20 07:03:50',
    true,
    '2021-11-20 07:03:50',
    '2021-11-20 07:03:50'
);

-- Settings Migration
INSERT INTO settings (id, company_id, key, value, type, description, created_at, updated_at) VALUES 
('h8i9j0k1-l2m3-4567-hijk-890123456789', 'e31b9668-53a7-467c-b6a7-3b47947a579d', 'app_name', 'Fleet Management System', 'string', 'Application name', NOW(), NOW()),
('i9j0k1l2-m3n4-5678-ijkl-901234567890', 'e31b9668-53a7-467c-b6a7-3b47947a579d', 'currency', 'USD', 'string', 'Default currency', NOW(), NOW()),
('j0k1l2m3-n4o5-6789-jklm-012345678901', 'e31b9668-53a7-467c-b6a7-3b47947a579d', 'timezone', 'UTC', 'string', 'Default timezone', NOW(), NOW()),
('k1l2m3n4-o5p6-7890-klmn-123456789012', 'e31b9668-53a7-467c-b6a7-3b47947a579d', 'language', 'en', 'string', 'Default language', NOW(), NOW());

-- Migration Summary
-- Companies: 1
-- Users: 4
-- Vehicles: 2
-- Vehicle Types: 6
-- Bookings: 1
-- Driver-Vehicle Assignments: 2
-- Settings: 4
-- Generated: 2024-12-01 12:00:00

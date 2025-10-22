-- Fix Vehicle Types Database
-- This script ensures the vehicle_types table has the correct data

-- First, let's see what's currently in the table
SELECT 'Current vehicle_types:' as status;
SELECT * FROM vehicle_types ORDER BY name;

-- Clear existing vehicle types (using DELETE instead of TRUNCATE to avoid foreign key issues)
DELETE FROM vehicle_types;

-- Insert the exact vehicle types requested by user with UUIDs
-- Note: Using correct column names from actual schema: name, display_name, seats, is_enabled
INSERT INTO vehicle_types (id, name, display_name, seats, is_enabled, created_at, updated_at) VALUES
(gen_random_uuid(), 'Convertible', 'Convertible', 2, true, NOW(), NOW()),
(gen_random_uuid(), 'Coupe', 'Coupe', 2, true, NOW(), NOW()),
(gen_random_uuid(), 'Estate', 'Estate', 5, true, NOW(), NOW()),
(gen_random_uuid(), 'Hatchback', 'Hatchback', 5, true, NOW(), NOW()),
(gen_random_uuid(), 'MPV', 'MPV', 7, true, NOW(), NOW()),
(gen_random_uuid(), 'Saloon', 'Saloon', 5, true, NOW(), NOW()),
(gen_random_uuid(), 'SUV', 'SUV', 7, true, NOW(), NOW());

-- Verify the final data
SELECT 'Final vehicle_types:' as status;
SELECT * FROM vehicle_types ORDER BY name;

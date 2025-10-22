-- Fix Vehicle Types Database
-- This script ensures the vehicle_types table has the correct data

-- Clear existing vehicle types
TRUNCATE TABLE vehicle_types;

-- Insert the exact vehicle types requested by user
INSERT INTO vehicle_types (id, vehicletype, displayname, seats, is_enabled, created_at, updated_at) VALUES
(1, 'Convertible', 'Convertible', 2, 1, NOW(), NOW()),
(2, 'Coupe', 'Coupe', 2, 1, NOW(), NOW()),
(3, 'Estate', 'Estate', 5, 1, NOW(), NOW()),
(4, 'Hatchback', 'Hatchback', 5, 1, NOW(), NOW()),
(5, 'MPV', 'MPV', 7, 1, NOW(), NOW()),
(6, 'Saloon', 'Saloon', 5, 1, NOW(), NOW()),
(7, 'SUV', 'SUV', 7, 1, NOW(), NOW());

-- Verify the data
SELECT * FROM vehicle_types ORDER BY id;

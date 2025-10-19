-- IMMEDIATE DATABASE FIX SQL - CREATE MISSING TABLES
-- Run this directly in your Supabase SQL Editor

-- Create users_meta table
CREATE TABLE IF NOT EXISTS users_meta (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL,
    type VARCHAR(255) DEFAULT 'null',
    key VARCHAR(255) NOT NULL,
    value TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- Create indexes for users_meta
CREATE INDEX IF NOT EXISTS idx_users_meta_user_id ON users_meta(user_id);
CREATE INDEX IF NOT EXISTS idx_users_meta_key ON users_meta(key);

-- Create vehicles_meta table
CREATE TABLE IF NOT EXISTS vehicles_meta (
    id BIGSERIAL PRIMARY KEY,
    vehicle_id BIGINT NOT NULL,
    type VARCHAR(255) DEFAULT 'null',
    key VARCHAR(255) NOT NULL,
    value TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- Create indexes for vehicles_meta
CREATE INDEX IF NOT EXISTS idx_vehicles_meta_vehicle_id ON vehicles_meta(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_vehicles_meta_key ON vehicles_meta(key);

-- Create bookings_meta table
CREATE TABLE IF NOT EXISTS bookings_meta (
    id BIGSERIAL PRIMARY KEY,
    booking_id BIGINT NOT NULL,
    type VARCHAR(255) DEFAULT 'null',
    key VARCHAR(255) NOT NULL,
    value TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- Create indexes for bookings_meta
CREATE INDEX IF NOT EXISTS idx_bookings_meta_booking_id ON bookings_meta(booking_id);
CREATE INDEX IF NOT EXISTS idx_bookings_meta_key ON bookings_meta(key);

-- Verify all tables were created
SELECT 
    table_name,
    'Table created successfully' as status
FROM information_schema.tables 
WHERE table_name IN ('users_meta', 'vehicles_meta', 'bookings_meta')
ORDER BY table_name;

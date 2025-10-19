-- COMPLETE DATABASE FIX FOR SUPABASE - ALL CORE TABLES
-- Run this in your Supabase SQL Editor to create ALL missing tables

-- 1. Create companies table (if missing)
CREATE TABLE IF NOT EXISTS companies (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    email VARCHAR(255),
    phone VARCHAR(255),
    address TEXT,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- 2. Create users table (if missing) - CRITICAL TABLE
CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    user_id INTEGER DEFAULT NULL,
    name VARCHAR(255),
    email VARCHAR(95) UNIQUE,
    password VARCHAR(255),
    user_type VARCHAR(255),
    group_id INTEGER DEFAULT NULL,
    company_id BIGINT DEFAULT NULL,
    api_token VARCHAR(60) NOT NULL UNIQUE,
    remember_token VARCHAR(100),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- 3. Create vehicles table (if missing) - CRITICAL TABLE
CREATE TABLE IF NOT EXISTS vehicles (
    id BIGSERIAL PRIMARY KEY,
    make_name VARCHAR(100),
    model_name VARCHAR(100),
    color_name VARCHAR(100),
    year VARCHAR(255),
    group_id INTEGER DEFAULT NULL,
    company_id BIGINT DEFAULT NULL,
    lic_exp_date DATE,
    reg_exp_date DATE,
    vehicle_image VARCHAR(255),
    engine_type VARCHAR(255),
    horse_power VARCHAR(255),
    vin VARCHAR(255),
    license_plate VARCHAR(255) NOT NULL,
    mileage INTEGER,
    int_mileage INTEGER,
    in_service SMALLINT DEFAULT 0,
    user_id INTEGER DEFAULT NULL,
    type_id INTEGER DEFAULT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- 4. Create bookings table (if missing) - CRITICAL TABLE WITH driver_id
CREATE TABLE IF NOT EXISTS bookings (
    id BIGSERIAL PRIMARY KEY,
    customer_id INTEGER DEFAULT NULL,
    user_id INTEGER DEFAULT NULL,
    vehicle_id INTEGER DEFAULT NULL,
    driver_id INTEGER DEFAULT NULL,  -- THIS IS THE MISSING COLUMN!
    pickup TIMESTAMP NULL,
    dropoff TIMESTAMP NULL,
    duration INTEGER DEFAULT NULL,
    pickup_addr VARCHAR(255),
    dest_addr VARCHAR(255),
    note TEXT,
    travellers INTEGER NOT NULL DEFAULT 1,
    cancellation INTEGER NOT NULL DEFAULT 0,
    status INTEGER NOT NULL DEFAULT 0,
    payment INTEGER NOT NULL DEFAULT 0,
    company_id BIGINT DEFAULT NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- 5. Create users_meta table (CRITICAL - this was causing your original error)
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

-- 6. Create vehicles_meta table
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

-- 7. Create bookings_meta table
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

-- 8. Create fines table (with driver_id)
CREATE TABLE IF NOT EXISTS fines (
    id BIGSERIAL PRIMARY KEY,
    fine_type VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    admin_fee DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    discount_window_days INTEGER,
    discount_amount DECIMAL(10,2),
    escalation_days INTEGER,
    escalation_multiplier DECIMAL(3,2) DEFAULT 1.5,
    vehicle_reg VARCHAR(255) NOT NULL,
    vehicle_id INTEGER,
    driver_id INTEGER,  -- THIS COLUMN WAS MISSING!
    status VARCHAR(50) DEFAULT 'pending' CHECK (status IN ('pending', 'notified', 'paid', 'disputed', 'escalated')),
    date_logged TIMESTAMP NOT NULL,
    due_date TIMESTAMP,
    escalation_date TIMESTAMP,
    evidence_file VARCHAR(255),
    notes TEXT,
    contravention_code VARCHAR(255),
    reference_number VARCHAR(255),
    date_issued TIMESTAMP,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- 9. Create additional essential tables
CREATE TABLE IF NOT EXISTS custom_form_fields (
    id BIGSERIAL PRIMARY KEY,
    field_name VARCHAR(255) NOT NULL,
    field_type VARCHAR(50) NOT NULL CHECK (field_type IN ('text', 'email', 'phone', 'dropdown', 'date', 'file', 'textarea')),
    field_options JSON,
    is_required BOOLEAN DEFAULT false,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS onboarding_drivers (
    id BIGSERIAL PRIMARY KEY,
    driver_id BIGINT NOT NULL,
    vehicle_id BIGINT,
    scheme VARCHAR(255),
    insurance_selection VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS onboarding_links (
    id BIGSERIAL PRIMARY KEY,
    link_url TEXT NOT NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS onboarding_form_field_configs (
    id BIGSERIAL PRIMARY KEY,
    field_name VARCHAR(255) NOT NULL,
    field_type VARCHAR(50) NOT NULL,
    is_required BOOLEAN DEFAULT false,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_user_type ON users(user_type);
CREATE INDEX IF NOT EXISTS idx_users_company_id ON users(company_id);
CREATE INDEX IF NOT EXISTS idx_users_api_token ON users(api_token);

CREATE INDEX IF NOT EXISTS idx_vehicles_license_plate ON vehicles(license_plate);
CREATE INDEX IF NOT EXISTS idx_vehicles_group_id ON vehicles(group_id);
CREATE INDEX IF NOT EXISTS idx_vehicles_company_id ON vehicles(company_id);
CREATE INDEX IF NOT EXISTS idx_vehicles_type_id ON vehicles(type_id);

CREATE INDEX IF NOT EXISTS idx_bookings_customer_id ON bookings(customer_id);
CREATE INDEX IF NOT EXISTS idx_bookings_driver_id ON bookings(driver_id);  -- CRITICAL INDEX!
CREATE INDEX IF NOT EXISTS idx_bookings_vehicle_id ON bookings(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);
CREATE INDEX IF NOT EXISTS idx_bookings_company_id ON bookings(company_id);

CREATE INDEX IF NOT EXISTS idx_users_meta_user_id ON users_meta(user_id);
CREATE INDEX IF NOT EXISTS idx_users_meta_key ON users_meta(key);
CREATE INDEX IF NOT EXISTS idx_vehicles_meta_vehicle_id ON vehicles_meta(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_vehicles_meta_key ON vehicles_meta(key);
CREATE INDEX IF NOT EXISTS idx_bookings_meta_booking_id ON bookings_meta(booking_id);
CREATE INDEX IF NOT EXISTS idx_bookings_meta_key ON bookings_meta(key);

CREATE INDEX IF NOT EXISTS idx_fines_vehicle_reg ON fines(vehicle_reg);
CREATE INDEX IF NOT EXISTS idx_fines_status ON fines(status);
CREATE INDEX IF NOT EXISTS idx_fines_driver_id ON fines(driver_id);  -- CRITICAL INDEX!

-- Verify all critical tables were created successfully
SELECT 
    table_name,
    'Table created successfully' as status
FROM information_schema.tables 
WHERE table_name IN (
    'companies',
    'users',
    'vehicles', 
    'bookings',
    'users_meta', 
    'vehicles_meta', 
    'bookings_meta',
    'fines',
    'custom_form_fields',
    'onboarding_drivers',
    'onboarding_links',
    'onboarding_form_field_configs'
)
ORDER BY table_name;

-- Show the specific tables that were causing your errors
SELECT 
    'users_meta table is now ready!' as message,
    COUNT(*) as column_count
FROM information_schema.columns 
WHERE table_name = 'users_meta';

SELECT 
    'bookings table with driver_id is now ready!' as message,
    COUNT(*) as column_count
FROM information_schema.columns 
WHERE table_name = 'bookings' AND column_name = 'driver_id';

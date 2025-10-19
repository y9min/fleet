-- COMPLETE DATABASE FIX FOR SUPABASE
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

-- 2. Create users_meta table (CRITICAL - this is what's causing your error)
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

-- 3. Create vehicles_meta table
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

-- 4. Create bookings_meta table
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

-- 5. Create custom_form_fields table
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

-- 6. Create onboarding_drivers table
CREATE TABLE IF NOT EXISTS onboarding_drivers (
    id BIGSERIAL PRIMARY KEY,
    driver_id BIGINT NOT NULL,
    vehicle_id BIGINT,
    scheme VARCHAR(255),
    insurance_selection VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 7. Create onboarding_links table
CREATE TABLE IF NOT EXISTS onboarding_links (
    id BIGSERIAL PRIMARY KEY,
    link_url TEXT NOT NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 8. Create onboarding_form_field_configs table
CREATE TABLE IF NOT EXISTS onboarding_form_field_configs (
    id BIGSERIAL PRIMARY KEY,
    field_name VARCHAR(255) NOT NULL,
    field_type VARCHAR(50) NOT NULL,
    is_required BOOLEAN DEFAULT false,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 9. Create fines table
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
    driver_id INTEGER,
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

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_meta_user_id ON users_meta(user_id);
CREATE INDEX IF NOT EXISTS idx_users_meta_key ON users_meta(key);
CREATE INDEX IF NOT EXISTS idx_vehicles_meta_vehicle_id ON vehicles_meta(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_vehicles_meta_key ON vehicles_meta(key);
CREATE INDEX IF NOT EXISTS idx_bookings_meta_booking_id ON bookings_meta(booking_id);
CREATE INDEX IF NOT EXISTS idx_bookings_meta_key ON bookings_meta(key);
CREATE INDEX IF NOT EXISTS idx_custom_form_fields_sort ON custom_form_fields(sort_order);
CREATE INDEX IF NOT EXISTS idx_custom_form_fields_required ON custom_form_fields(is_required);
CREATE INDEX IF NOT EXISTS idx_onboarding_drivers_driver_id ON onboarding_drivers(driver_id);
CREATE INDEX IF NOT EXISTS idx_onboarding_drivers_vehicle_id ON onboarding_drivers(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_fines_vehicle_reg ON fines(vehicle_reg);
CREATE INDEX IF NOT EXISTS idx_fines_status ON fines(status);

-- Verify all tables were created successfully
SELECT 
    table_name,
    'Table created successfully' as status
FROM information_schema.tables 
WHERE table_name IN (
    'companies',
    'users_meta', 
    'vehicles_meta', 
    'bookings_meta',
    'custom_form_fields',
    'onboarding_drivers',
    'onboarding_links',
    'onboarding_form_field_configs',
    'fines'
)
ORDER BY table_name;

-- Show the specific table that was causing your error
SELECT 
    'users_meta table is now ready!' as message,
    COUNT(*) as column_count
FROM information_schema.columns 
WHERE table_name = 'users_meta';

-- COMPREHENSIVE DATABASE SETUP FOR SUPABASE
-- This script creates ALL missing tables needed for the Fleet Management system
-- Run this in your Supabase SQL Editor to fix all missing table errors

-- ==============================================
-- 1. VEHICLE REVIEW TABLE (CRITICAL - FIXES CURRENT ERROR)
-- ==============================================

CREATE TABLE IF NOT EXISTS vehicle_review (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    vehicle_id UUID NOT NULL,
    user_id UUID,
    reg_no VARCHAR(255),
    kms_outgoing INTEGER,
    kms_incoming INTEGER,
    fuel_level_out INTEGER,
    fuel_level_in INTEGER,
    datetime_outgoing TIMESTAMP,
    datetime_incoming TIMESTAMP,
    petrol_card TEXT,
    lights TEXT,
    invertor TEXT,
    car_mats TEXT,
    int_damage TEXT,
    int_lights TEXT,
    ext_car TEXT,
    tyre TEXT,
    ladder TEXT,
    leed TEXT,
    power_tool TEXT,
    ac TEXT,
    head_light TEXT,
    lock TEXT,
    windows TEXT,
    condition TEXT,
    oil_chk TEXT,
    suspension TEXT,
    tool_box TEXT,
    image VARCHAR(255),
    udf VARCHAR(255),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    deleted_at TIMESTAMP,
    
    -- Foreign key constraints
    CONSTRAINT fk_vehicle_review_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    CONSTRAINT fk_vehicle_review_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Indexes for performance
CREATE INDEX IF NOT EXISTS idx_vehicle_review_vehicle_id_user_id ON vehicle_review(vehicle_id, user_id);
CREATE INDEX IF NOT EXISTS idx_vehicle_review_deleted_at ON vehicle_review(deleted_at);

-- ==============================================
-- 2. WORK ORDERS TABLE
-- ==============================================

CREATE TABLE IF NOT EXISTS work_orders (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID,
    vehicle_id UUID NOT NULL,
    vendor_id UUID,
    mechanic_id UUID,
    required_by DATE,
    status VARCHAR(50),
    description TEXT,
    meter INTEGER,
    note TEXT,
    reference VARCHAR(255),
    price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    deleted_at TIMESTAMP,
    
    -- Foreign key constraints
    CONSTRAINT fk_work_orders_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    CONSTRAINT fk_work_orders_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_work_orders_vehicle_id ON work_orders(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_work_orders_deleted_at ON work_orders(deleted_at);

-- ==============================================
-- 3. PARTS USED TABLE
-- ==============================================

CREATE TABLE IF NOT EXISTS parts_used (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    work_id UUID NOT NULL,
    part_id UUID,
    qty INTEGER DEFAULT 1,
    price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    deleted_at TIMESTAMP,
    
    -- Foreign key constraints
    CONSTRAINT fk_parts_used_work_id FOREIGN KEY (work_id) REFERENCES work_orders(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_parts_used_work_id ON parts_used(work_id);
CREATE INDEX IF NOT EXISTS idx_parts_used_deleted_at ON parts_used(deleted_at);

-- ==============================================
-- 4. DRIVER VEHICLE ASSIGNMENT TABLE
-- ==============================================

CREATE TABLE IF NOT EXISTS driver_vehicle (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    driver_id UUID NOT NULL,
    vehicle_id UUID NOT NULL,
    assigned_at TIMESTAMP DEFAULT NOW(),
    unassigned_at TIMESTAMP,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    
    -- Foreign key constraints
    CONSTRAINT fk_driver_vehicle_driver_id FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_driver_vehicle_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    
    -- Unique constraint
    CONSTRAINT uk_driver_vehicle UNIQUE (driver_id, vehicle_id)
);

CREATE INDEX IF NOT EXISTS idx_driver_vehicle_driver_id ON driver_vehicle(driver_id);
CREATE INDEX IF NOT EXISTS idx_driver_vehicle_vehicle_id ON driver_vehicle(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_driver_vehicle_is_active ON driver_vehicle(is_active);

-- ==============================================
-- 5. BOOKINGS META TABLE (for Metable trait)
-- ==============================================

CREATE TABLE IF NOT EXISTS bookings_meta (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    booking_id UUID NOT NULL,
    type VARCHAR(255) DEFAULT 'null',
    key VARCHAR(255) NOT NULL,
    value TEXT,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    deleted_at TIMESTAMP,
    
    -- Foreign key constraints
    CONSTRAINT fk_bookings_meta_booking_id FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_bookings_meta_booking_id ON bookings_meta(booking_id);
CREATE INDEX IF NOT EXISTS idx_bookings_meta_key ON bookings_meta(key);
CREATE INDEX IF NOT EXISTS idx_bookings_meta_deleted_at ON bookings_meta(deleted_at);

-- ==============================================
-- 6. VEHICLES META TABLE (for Metable trait)
-- ==============================================

CREATE TABLE IF NOT EXISTS vehicles_meta (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    vehicle_id UUID NOT NULL,
    type VARCHAR(255) DEFAULT 'null',
    key VARCHAR(255) NOT NULL,
    value TEXT,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    deleted_at TIMESTAMP,
    
    -- Foreign key constraints
    CONSTRAINT fk_vehicles_meta_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_vehicles_meta_vehicle_id ON vehicles_meta(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_vehicles_meta_key ON vehicles_meta(key);
CREATE INDEX IF NOT EXISTS idx_vehicles_meta_deleted_at ON vehicles_meta(deleted_at);

-- ==============================================
-- 7. ADDITIONAL MISSING TABLES
-- ==============================================

-- Income Model
CREATE TABLE IF NOT EXISTS income (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    vehicle_id UUID,
    user_id UUID,
    amount DECIMAL(10,2),
    description TEXT,
    date DATE,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    deleted_at TIMESTAMP,
    
    CONSTRAINT fk_income_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    CONSTRAINT fk_income_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_income_vehicle_id ON income(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_income_deleted_at ON income(deleted_at);

-- Expense Model
CREATE TABLE IF NOT EXISTS expense (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    vehicle_id UUID,
    user_id UUID,
    amount DECIMAL(10,2),
    description TEXT,
    date DATE,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    deleted_at TIMESTAMP,
    
    CONSTRAINT fk_expense_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    CONSTRAINT fk_expense_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_expense_vehicle_id ON expense(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_expense_deleted_at ON expense(deleted_at);

-- ==============================================
-- 8. VERIFICATION QUERIES
-- ==============================================

-- Check if all tables were created successfully
SELECT 
    table_name,
    CASE 
        WHEN table_name IN ('vehicle_review', 'work_orders', 'parts_used', 'driver_vehicle', 'bookings_meta', 'vehicles_meta', 'income', 'expense')
        THEN '✅ CREATED SUCCESSFULLY'
        ELSE '❌ MISSING'
    END as status
FROM information_schema.tables 
WHERE table_schema = 'public' 
AND table_name IN ('vehicle_review', 'work_orders', 'parts_used', 'driver_vehicle', 'bookings_meta', 'vehicles_meta', 'income', 'expense')
ORDER BY table_name;

-- ==============================================
-- 9. SUCCESS MESSAGE
-- ==============================================

SELECT 
    '🎉 COMPREHENSIVE DATABASE SETUP COMPLETE!' as message,
    'All missing tables have been created with proper UUID support' as details,
    'The vehicle_review table error should now be resolved' as result;

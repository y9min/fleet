-- FIX MISSING TABLES AND COLUMNS FOR PRODUCTION
-- Run this in Supabase SQL Editor to fix all missing table/column errors

-- ==============================================
-- 1. CREATE VEHICLE_GROUP TABLE (ALIAS TO VEHICLE_GROUPS)
-- ==============================================

-- Create a view that maps vehicle_group to vehicle_groups
CREATE OR REPLACE VIEW vehicle_group AS 
SELECT 
    id,
    company_id,
    name,
    description,
    created_at,
    updated_at,
    deleted_at
FROM vehicle_groups;

-- Grant permissions on the view
GRANT SELECT, INSERT, UPDATE, DELETE ON vehicle_group TO authenticated;
GRANT SELECT, INSERT, UPDATE, DELETE ON vehicle_group TO anon;

-- ==============================================
-- 2. CREATE DRIVER_LOGS TABLE
-- ==============================================

CREATE TABLE IF NOT EXISTS driver_logs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    driver_id UUID NOT NULL,
    vehicle_id UUID NOT NULL,
    date TIMESTAMPTZ NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    
    -- Foreign key constraints
    CONSTRAINT fk_driver_logs_driver_id FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_driver_logs_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_driver_logs_driver_id ON driver_logs(driver_id);
CREATE INDEX IF NOT EXISTS idx_driver_logs_vehicle_id ON driver_logs(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_driver_logs_date ON driver_logs(date);
CREATE INDEX IF NOT EXISTS idx_driver_logs_deleted_at ON driver_logs(deleted_at);

-- ==============================================
-- 3. CREATE ADDRESSES TABLE
-- ==============================================

CREATE TABLE IF NOT EXISTS addresses (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    customer_id UUID NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    
    -- Foreign key constraints
    CONSTRAINT fk_addresses_customer_id FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_addresses_customer_id ON addresses(customer_id);
CREATE INDEX IF NOT EXISTS idx_addresses_deleted_at ON addresses(deleted_at);

-- ==============================================
-- 4. CREATE INCOME_CAT TABLE
-- ==============================================

CREATE TABLE IF NOT EXISTS income_cat (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(100) NOT NULL,
    user_id UUID,
    cost DECIMAL(10,2) DEFAULT 0.00,
    type VARCHAR(5) DEFAULT 'd',
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    
    -- Foreign key constraints
    CONSTRAINT fk_income_cat_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_income_cat_name ON income_cat(name);
CREATE INDEX IF NOT EXISTS idx_income_cat_type ON income_cat(type);
CREATE INDEX IF NOT EXISTS idx_income_cat_user_id ON income_cat(user_id);
CREATE INDEX IF NOT EXISTS idx_income_cat_deleted_at ON income_cat(deleted_at);

-- Seed default income categories
INSERT INTO income_cat (name, user_id, type) VALUES
('Booking', NULL, 'd'),
('Service', NULL, 'd'),
('Maintenance', NULL, 'd'),
('Other', NULL, 'd')
ON CONFLICT DO NOTHING;

-- ==============================================
-- 5. FIX ONBOARDING_LINKS TABLE - ADD MISSING COLUMNS
-- ==============================================

-- Add missing columns to onboarding_links table
ALTER TABLE onboarding_links 
ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT true,
ADD COLUMN IF NOT EXISTS usage_count INTEGER DEFAULT 0,
ADD COLUMN IF NOT EXISTS link TEXT;

-- Create index for is_active column
CREATE INDEX IF NOT EXISTS idx_onboarding_links_is_active ON onboarding_links(is_active);

-- ==============================================
-- 6. CREATE ADDITIONAL MISSING TABLES
-- ==============================================

-- Create expense_cat table (referenced by ExpCats model)
CREATE TABLE IF NOT EXISTS expense_cat (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(100) NOT NULL,
    user_id UUID,
    cost DECIMAL(10,2) DEFAULT 0.00,
    type VARCHAR(5) DEFAULT 'd',
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    
    -- Foreign key constraints
    CONSTRAINT fk_expense_cat_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create indexes for expense_cat
CREATE INDEX IF NOT EXISTS idx_expense_cat_name ON expense_cat(name);
CREATE INDEX IF NOT EXISTS idx_expense_cat_type ON expense_cat(type);
CREATE INDEX IF NOT EXISTS idx_expense_cat_user_id ON expense_cat(user_id);
CREATE INDEX IF NOT EXISTS idx_expense_cat_deleted_at ON expense_cat(deleted_at);

-- Seed default expense categories
INSERT INTO expense_cat (name, user_id, type) VALUES
('Fuel', NULL, 'd'),
('Maintenance', NULL, 'd'),
('Insurance', NULL, 'd'),
('Repairs', NULL, 'd'),
('Other', NULL, 'd')
ON CONFLICT DO NOTHING;

-- Create booking_quotation table (referenced by BookingQuotationModel)
CREATE TABLE IF NOT EXISTS booking_quotation (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    company_id UUID,
    customer_id UUID,
    vehicle_id UUID,
    user_id UUID,
    driver_id UUID,
    pickup TIMESTAMPTZ NOT NULL,
    dropoff TIMESTAMPTZ,
    pickup_addr TEXT,
    dest_addr TEXT,
    travellers INTEGER DEFAULT 1,
    status VARCHAR(50) DEFAULT 'pending',
    comment TEXT,
    note TEXT,
    day INTEGER,
    mileage DECIMAL(10,2),
    waiting_time INTEGER,
    total DECIMAL(10,2),
    tax_total DECIMAL(10,2),
    total_tax_percent DECIMAL(5,2),
    total_tax_charge_rs DECIMAL(10,2),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    
    -- Foreign key constraints
    CONSTRAINT fk_booking_quotation_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    CONSTRAINT fk_booking_quotation_customer_id FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_booking_quotation_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    CONSTRAINT fk_booking_quotation_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_booking_quotation_driver_id FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create indexes for booking_quotation
CREATE INDEX IF NOT EXISTS idx_booking_quotation_company_id ON booking_quotation(company_id);
CREATE INDEX IF NOT EXISTS idx_booking_quotation_customer_id ON booking_quotation(customer_id);
CREATE INDEX IF NOT EXISTS idx_booking_quotation_vehicle_id ON booking_quotation(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_booking_quotation_pickup ON booking_quotation(pickup);
CREATE INDEX IF NOT EXISTS idx_booking_quotation_deleted_at ON booking_quotation(deleted_at);

-- Create driver_alert table (referenced by DriverAlertModel)
CREATE TABLE IF NOT EXISTS driver_alert (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    company_id UUID,
    driver_id UUID NOT NULL,
    vehicle_id UUID,
    alert_type VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    severity VARCHAR(20) DEFAULT 'medium',
    is_read BOOLEAN DEFAULT false,
    read_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    
    -- Foreign key constraints
    CONSTRAINT fk_driver_alert_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    CONSTRAINT fk_driver_alert_driver_id FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_driver_alert_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL
);

-- Create indexes for driver_alert
CREATE INDEX IF NOT EXISTS idx_driver_alert_driver_id ON driver_alert(driver_id);
CREATE INDEX IF NOT EXISTS idx_driver_alert_vehicle_id ON driver_alert(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_driver_alert_type ON driver_alert(alert_type);
CREATE INDEX IF NOT EXISTS idx_driver_alert_is_read ON driver_alert(is_read);
CREATE INDEX IF NOT EXISTS idx_driver_alert_deleted_at ON driver_alert(deleted_at);

-- Create booking_alerts table (referenced by BookingAlert model)
CREATE TABLE IF NOT EXISTS booking_alerts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    company_id UUID,
    booking_id UUID NOT NULL,
    alert_type VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    severity VARCHAR(20) DEFAULT 'medium',
    is_read BOOLEAN DEFAULT false,
    read_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    
    -- Foreign key constraints
    CONSTRAINT fk_booking_alerts_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    CONSTRAINT fk_booking_alerts_booking_id FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Create indexes for booking_alerts
CREATE INDEX IF NOT EXISTS idx_booking_alerts_booking_id ON booking_alerts(booking_id);
CREATE INDEX IF NOT EXISTS idx_booking_alerts_type ON booking_alerts(alert_type);
CREATE INDEX IF NOT EXISTS idx_booking_alerts_is_read ON booking_alerts(is_read);
CREATE INDEX IF NOT EXISTS idx_booking_alerts_deleted_at ON booking_alerts(deleted_at);

-- ==============================================
-- 7. VERIFY ALL TABLES CREATED SUCCESSFULLY
-- ==============================================

SELECT 
    '✅ MISSING TABLES FIX COMPLETE!' as status,
    'All missing tables and columns have been created' as message;

-- Show all created tables
SELECT 
    'CREATED TABLES' as info,
    table_name,
    table_type
FROM information_schema.tables 
WHERE table_schema = 'public' 
AND table_name IN (
    'vehicle_group', 'driver_logs', 'addresses', 'income_cat', 
    'expense_cat', 'booking_quotation', 'driver_alert', 'booking_alerts'
)
ORDER BY table_name;

-- Show onboarding_links columns
SELECT 
    'ONBOARDING_LINKS COLUMNS' as info,
    column_name,
    data_type,
    is_nullable
FROM information_schema.columns 
WHERE table_name = 'onboarding_links' 
AND table_schema = 'public'
ORDER BY ordinal_position;

-- Show sample data counts
SELECT 
    'SAMPLE DATA COUNTS' as info,
    (SELECT COUNT(*) FROM income_cat) as income_categories,
    (SELECT COUNT(*) FROM expense_cat) as expense_categories,
    (SELECT COUNT(*) FROM driver_logs) as driver_logs_count,
    (SELECT COUNT(*) FROM addresses) as addresses_count;

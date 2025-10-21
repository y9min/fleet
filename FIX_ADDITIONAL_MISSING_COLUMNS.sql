-- ADDITIONAL MISSING COLUMNS AND TABLES FIX
-- Run this in Supabase SQL Editor to fix remaining missing columns and tables

-- ==============================================
-- 1. ADD MISSING COLUMNS TO ONBOARDING_DRIVERS TABLE
-- ==============================================

-- Add missing columns to onboarding_drivers table
ALTER TABLE onboarding_drivers 
ADD COLUMN IF NOT EXISTS license_upload_path VARCHAR(255),
ADD COLUMN IF NOT EXISTS insurance_upload_path VARCHAR(255),
ADD COLUMN IF NOT EXISTS unique_token VARCHAR(255) UNIQUE;

-- Create index for unique_token
CREATE INDEX IF NOT EXISTS idx_onboarding_drivers_unique_token ON onboarding_drivers(unique_token);

-- ==============================================
-- 2. CREATE REASONS TABLE
-- ==============================================

CREATE TABLE IF NOT EXISTS reasons (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    reason TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    deleted_at TIMESTAMPTZ
);

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_reasons_deleted_at ON reasons(deleted_at);

-- Seed default reasons
INSERT INTO reasons (reason) VALUES
('No fuel'),
('Tire punctured'),
('Vehicle breakdown'),
('Driver unavailable'),
('Weather conditions'),
('Traffic delays'),
('Customer cancellation'),
('Other')
ON CONFLICT DO NOTHING;

-- ==============================================
-- 3. VERIFY ALL FIXES APPLIED SUCCESSFULLY
-- ==============================================

SELECT 
    '✅ ADDITIONAL FIXES COMPLETE!' as status,
    'All missing columns and tables have been created' as message;

-- Show onboarding_drivers columns
SELECT 
    'ONBOARDING_DRIVERS COLUMNS' as info,
    column_name,
    data_type,
    is_nullable
FROM information_schema.columns 
WHERE table_name = 'onboarding_drivers' 
AND table_schema = 'public'
ORDER BY ordinal_position;

-- Show reasons table
SELECT 
    'REASONS TABLE CREATED' as info,
    COUNT(*) as reason_count
FROM reasons;

-- Show sample reasons
SELECT 
    'SAMPLE REASONS' as info,
    id,
    reason
FROM reasons 
ORDER BY created_at 
LIMIT 5;

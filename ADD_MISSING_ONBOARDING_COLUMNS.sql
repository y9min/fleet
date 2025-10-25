-- Add Missing Columns to onboarding_drivers Table
-- Run this in Supabase SQL Editor to fix the database schema

-- This SQL adds all missing columns that the application expects
-- Safe to run multiple times (checks for existence first)

-- Add custom_data column if it doesn't exist
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'onboarding_drivers' 
        AND column_name = 'custom_data'
    ) THEN
        ALTER TABLE onboarding_drivers 
        ADD COLUMN custom_data JSONB;
    END IF;
END $$;

-- Add license_expiry column if it doesn't exist
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'onboarding_drivers' 
        AND column_name = 'license_expiry'
    ) THEN
        ALTER TABLE onboarding_drivers 
        ADD COLUMN license_expiry DATE;
    END IF;
END $$;

-- Add address column if it doesn't exist
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'onboarding_drivers' 
        AND column_name = 'address'
    ) THEN
        ALTER TABLE onboarding_drivers 
        ADD COLUMN address TEXT;
    END IF;
END $$;

-- Add emergency_contact column if it doesn't exist
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'onboarding_drivers' 
        AND column_name = 'emergency_contact'
    ) THEN
        ALTER TABLE onboarding_drivers 
        ADD COLUMN emergency_contact VARCHAR(255);
    END IF;
END $$;

-- Add emergency_phone column if it doesn't exist
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'onboarding_drivers' 
        AND column_name = 'emergency_phone'
    ) THEN
        ALTER TABLE onboarding_drivers 
        ADD COLUMN emergency_phone VARCHAR(255);
    END IF;
END $$;

-- Add form_data column if it doesn't exist
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'onboarding_drivers' 
        AND column_name = 'form_data'
    ) THEN
        ALTER TABLE onboarding_drivers 
        ADD COLUMN form_data JSONB;
    END IF;
END $$;

-- Verify all columns were added
SELECT 
    column_name, 
    data_type, 
    is_nullable
FROM information_schema.columns 
WHERE table_name = 'onboarding_drivers' 
AND column_name IN (
    'custom_data', 
    'license_expiry', 
    'address', 
    'emergency_contact', 
    'emergency_phone', 
    'form_data'
)
ORDER BY column_name;


-- Diagnostic Script for Vehicle Types Table
-- Run this to check the current state of the vehicle_types table

-- =============================================================================
-- CURRENT STATE DIAGNOSTIC
-- =============================================================================

-- 1. Check what columns currently exist in vehicle_types table
SELECT 
    column_name, 
    data_type, 
    is_nullable,
    column_default
FROM information_schema.columns 
WHERE table_name = 'vehicle_types' 
ORDER BY ordinal_position;

-- =============================================================================

-- 2. Check if the table exists at all
SELECT 
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.tables 
            WHERE table_name = 'vehicle_types'
        ) THEN 'EXISTS'
        ELSE 'DOES NOT EXIST'
    END as table_status;

-- =============================================================================

-- 3. Check current data in vehicle_types (if any)
SELECT COUNT(*) as total_records FROM vehicle_types;

-- If there are records, show a sample
SELECT * FROM vehicle_types LIMIT 3;

-- =============================================================================

-- 4. Check what Laravel expects vs what exists
SELECT 
    'Expected by Laravel' as source,
    'vehicletype' as column_name,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'vehicletype'
        ) THEN 'EXISTS'
        ELSE 'MISSING'
    END as status
UNION ALL
SELECT 
    'Expected by Laravel' as source,
    'displayname' as column_name,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'displayname'
        ) THEN 'EXISTS'
        ELSE 'MISSING'
    END as status
UNION ALL
SELECT 
    'Expected by Laravel' as source,
    'isenable' as column_name,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'isenable'
        ) THEN 'EXISTS'
        ELSE 'MISSING'
    END as status
UNION ALL
SELECT 
    'Current Production' as source,
    'name' as column_name,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'name'
        ) THEN 'EXISTS'
        ELSE 'MISSING'
    END as status
UNION ALL
SELECT 
    'Current Production' as source,
    'display_name' as column_name,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'display_name'
        ) THEN 'EXISTS'
        ELSE 'MISSING'
    END as status
UNION ALL
SELECT 
    'Current Production' as source,
    'is_enabled' as column_name,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'is_enabled'
        ) THEN 'EXISTS'
        ELSE 'MISSING'
    END as status;

-- =============================================================================

-- 5. Migration readiness check
SELECT 
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'name'
        ) AND EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'display_name'
        ) AND EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'is_enabled'
        ) THEN 'READY FOR MIGRATION'
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'vehicletype'
        ) AND EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'displayname'
        ) AND EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'isenable'
        ) THEN 'ALREADY MIGRATED'
        ELSE 'UNKNOWN STATE - CHECK MANUALLY'
    END as migration_status;

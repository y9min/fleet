-- SQL Verification Script for Critical Schema Fixes
-- Run this after migrations to verify the fixes are applied correctly

-- =============================================================================
-- VERIFICATION QUERIES
-- =============================================================================

-- 1. Verify remember_token column exists in users table
SELECT 
    column_name, 
    data_type, 
    is_nullable,
    character_maximum_length
FROM information_schema.columns 
WHERE table_name = 'users' 
  AND column_name = 'remember_token';

-- Expected result: remember_token | character varying | YES | 100

-- =============================================================================

-- 2. Verify vehicle_types columns have correct names and types
SELECT 
    column_name, 
    data_type, 
    is_nullable
FROM information_schema.columns 
WHERE table_name = 'vehicle_types' 
  AND column_name IN ('vehicletype', 'displayname', 'isenable')
ORDER BY column_name;

-- Expected results:
-- displayname | character varying | YES
-- isenable    | integer          | YES  
-- vehicletype  | character varying | YES

-- =============================================================================

-- 3. Verify no old column names exist in vehicle_types
SELECT 
    column_name, 
    data_type
FROM information_schema.columns 
WHERE table_name = 'vehicle_types' 
  AND column_name IN ('name', 'display_name', 'is_enabled');

-- Expected result: No rows returned (old columns should be gone)

-- =============================================================================

-- 4. Test data integrity - check if vehicle_types data is accessible
SELECT 
    id,
    vehicletype,
    displayname,
    isenable,
    seats,
    created_at
FROM vehicle_types 
LIMIT 5;

-- Expected result: Should return data with new column names

-- =============================================================================

-- 5. Test remember_token functionality (should not error)
SELECT 
    id,
    email,
    remember_token,
    created_at
FROM users 
WHERE remember_token IS NOT NULL
LIMIT 3;

-- Expected result: Should execute without error (may return 0 rows initially)

-- =============================================================================
-- SUMMARY CHECK
-- =============================================================================

-- Overall verification summary
SELECT 
    'Schema Fix Verification' as check_type,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'users' AND column_name = 'remember_token'
        ) THEN 'PASS' 
        ELSE 'FAIL' 
    END as remember_token_check,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'vehicletype'
        ) AND EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'displayname'
        ) AND EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'isenable'
        ) THEN 'PASS'
        ELSE 'FAIL'
    END as vehicle_types_check,
    CASE 
        WHEN NOT EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name IN ('name', 'display_name', 'is_enabled')
        ) THEN 'PASS'
        ELSE 'FAIL'
    END as old_columns_removed_check;

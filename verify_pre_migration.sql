-- PRE-MIGRATION VERIFICATION SCRIPT
-- Run this BEFORE running migrations to confirm the database is ready

-- =============================================================================
-- CURRENT STATE CHECK (BEFORE MIGRATION)
-- =============================================================================

-- 1. Check if remember_token column exists in users table (should NOT exist yet)
SELECT 
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'users' AND column_name = 'remember_token'
        ) THEN 'EXISTS (already migrated?)'
        ELSE 'MISSING (ready for migration)'
    END as remember_token_status;

-- =============================================================================

-- 2. Check vehicle_types table current columns
SELECT 
    column_name, 
    data_type, 
    is_nullable
FROM information_schema.columns 
WHERE table_name = 'vehicle_types' 
ORDER BY ordinal_position;

-- Expected BEFORE migration:
-- - name (character varying)
-- - display_name (character varying) 
-- - is_enabled (boolean)
-- - id, seats, icon, created_at, updated_at, deleted_at

-- =============================================================================

-- 3. Check if Laravel-expected columns exist (should NOT exist yet)
SELECT 
    'vehicletype' as expected_column,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'vehicletype'
        ) THEN 'EXISTS'
        ELSE 'MISSING (ready for migration)'
    END as status
UNION ALL
SELECT 
    'displayname' as expected_column,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'displayname'
        ) THEN 'EXISTS'
        ELSE 'MISSING (ready for migration)'
    END as status
UNION ALL
SELECT 
    'isenable' as expected_column,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'isenable'
        ) THEN 'EXISTS'
        ELSE 'MISSING (ready for migration)'
    END as status;

-- =============================================================================

-- 4. Test current vehicle_types data access (using current column names)
SELECT 
    id,
    name,
    display_name,
    is_enabled,
    seats,
    created_at
FROM vehicle_types 
LIMIT 3;

-- Expected result: Should return data with CURRENT column names

-- =============================================================================

-- 5. Migration readiness summary
SELECT 
    'PRE-MIGRATION STATUS' as check_type,
    CASE 
        WHEN NOT EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'users' AND column_name = 'remember_token'
        ) THEN 'READY'
        ELSE 'ALREADY MIGRATED'
    END as remember_token_migration,
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
        ) AND NOT EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'vehicletype'
        ) THEN 'READY'
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'vehicle_types' AND column_name = 'vehicletype'
        ) THEN 'ALREADY MIGRATED'
        ELSE 'UNKNOWN STATE'
    END as vehicle_types_migration;

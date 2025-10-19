-- DIAGNOSTIC QUERIES FOR SUPABASE DATABASE
-- Run these queries in your Supabase SQL Editor to verify data

-- ==============================================
-- 1. CHECK TABLE EXISTENCE
-- ==============================================

SELECT 
    table_name,
    CASE 
        WHEN table_name IN ('users', 'users_meta', 'settings', 'frontend', 'companies', 'vehicles', 'vehicle_types', 'vehicle_groups') 
        THEN 'REQUIRED TABLE EXISTS'
        ELSE 'OPTIONAL TABLE EXISTS'
    END as status
FROM information_schema.tables 
WHERE table_schema = 'public' 
AND table_name IN ('users', 'users_meta', 'settings', 'frontend', 'companies', 'vehicles', 'vehicle_types', 'vehicle_groups', 'bookings', 'bookings_meta', 'vehicles_meta')
ORDER BY table_name;

-- ==============================================
-- 2. CHECK DATA COUNTS
-- ==============================================

SELECT 'USERS' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'USERS_META', COUNT(*) FROM users_meta
UNION ALL
SELECT 'SETTINGS', COUNT(*) FROM settings
UNION ALL
SELECT 'FRONTEND', COUNT(*) FROM frontend
UNION ALL
SELECT 'COMPANIES', COUNT(*) FROM companies
UNION ALL
SELECT 'VEHICLES', COUNT(*) FROM vehicles
UNION ALL
SELECT 'VEHICLE_TYPES', COUNT(*) FROM vehicle_types
UNION ALL
SELECT 'VEHICLE_GROUPS', COUNT(*) FROM vehicle_groups
UNION ALL
SELECT 'BOOKINGS', COUNT(*) FROM bookings
UNION ALL
SELECT 'BOOKINGS_META', COUNT(*) FROM bookings_meta
UNION ALL
SELECT 'VEHICLES_META', COUNT(*) FROM vehicles_meta;

-- ==============================================
-- 3. CHECK USER ID TYPES
-- ==============================================

SELECT 
    'USERS TABLE' as table_name,
    CASE 
        WHEN EXISTS (SELECT 1 FROM users WHERE id::text ~ '^[0-9]+$') THEN 'INTEGER IDs FOUND'
        WHEN EXISTS (SELECT 1 FROM users WHERE id::text ~ '^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$') THEN 'UUID IDs FOUND'
        ELSE 'NO USERS FOUND'
    END as id_type,
    COUNT(*) as user_count
FROM users;

-- ==============================================
-- 4. CHECK USERS_META TABLE STRUCTURE
-- ==============================================

SELECT 
    column_name,
    data_type,
    is_nullable,
    column_default
FROM information_schema.columns 
WHERE table_name = 'users_meta' 
AND table_schema = 'public'
ORDER BY ordinal_position;

-- ==============================================
-- 5. CHECK SAMPLE USERS
-- ==============================================

SELECT 
    id,
    email,
    name,
    user_type,
    CASE 
        WHEN id::text ~ '^[0-9]+$' THEN 'INTEGER'
        WHEN id::text ~ '^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$' THEN 'UUID'
        ELSE 'UNKNOWN'
    END as id_type
FROM users 
ORDER BY created_at DESC 
LIMIT 5;

-- ==============================================
-- 6. CHECK REQUIRED SETTINGS
-- ==============================================

SELECT 
    name,
    value,
    CASE 
        WHEN name IN ('language', 'currency', 'app_name', 'email', 'driver_doc_verification') 
        THEN 'REQUIRED SETTING'
        ELSE 'OPTIONAL SETTING'
    END as status
FROM settings 
WHERE name IN ('language', 'currency', 'app_name', 'email', 'driver_doc_verification', 'vehicle_interval')
ORDER BY name;

-- ==============================================
-- 7. CHECK FRONTEND SETTINGS
-- ==============================================

SELECT 
    key_name,
    key_value,
    CASE 
        WHEN key_name IN ('app_name', 'app_description', 'app_keywords') 
        THEN 'REQUIRED FRONTEND SETTING'
        ELSE 'OPTIONAL FRONTEND SETTING'
    END as status
FROM frontend 
ORDER BY key_name;

-- ==============================================
-- 8. CHECK USERS_META DATA
-- ==============================================

SELECT 
    u.email,
    um.key,
    um.value,
    CASE 
        WHEN um.key = 'LANGUAGE' THEN 'REQUIRED METADATA'
        WHEN um.key = 'license_number' THEN 'DRIVER METADATA'
        ELSE 'OPTIONAL METADATA'
    END as metadata_type
FROM users u
LEFT JOIN users_meta um ON u.id = um.user_id
WHERE um.key IN ('LANGUAGE', 'license_number', 'fcm_id', 'device_token')
ORDER BY u.email, um.key;

-- ==============================================
-- 9. SUMMARY REPORT
-- ==============================================

SELECT 
    'DATABASE DIAGNOSTIC SUMMARY' as report_type,
    CASE 
        WHEN (SELECT COUNT(*) FROM users) = 0 THEN 'CRITICAL: No users found'
        WHEN EXISTS (SELECT 1 FROM users WHERE id::text ~ '^[0-9]+$') THEN 'CRITICAL: Integer IDs found - wrong database'
        WHEN (SELECT COUNT(*) FROM settings WHERE name = 'language') = 0 THEN 'WARNING: Missing language setting'
        WHEN (SELECT COUNT(*) FROM frontend) = 0 THEN 'WARNING: No frontend settings'
        WHEN (SELECT COUNT(*) FROM users_meta WHERE key = 'LANGUAGE') = 0 THEN 'WARNING: No LANGUAGE metadata'
        ELSE 'GOOD: Database appears properly configured'
    END as status,
    (SELECT COUNT(*) FROM users) as user_count,
    (SELECT COUNT(*) FROM settings) as settings_count,
    (SELECT COUNT(*) FROM frontend) as frontend_count,
    (SELECT COUNT(*) FROM users_meta) as metadata_count;

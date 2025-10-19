-- DIAGNOSE AND FIX LOGIN ISSUES
-- Check user data and fix type mismatches

-- ==============================================
-- 1. CHECK EXISTING USERS
-- ==============================================

-- Check what users exist in the database
SELECT 
    id,
    email,
    name,
    user_type,
    company_id,
    is_active,
    created_at
FROM public.users 
ORDER BY created_at;

-- Check if there are any users_meta records
SELECT 
    COUNT(*) as total_records,
    COUNT(DISTINCT user_id) as unique_users
FROM public.users_meta;

-- Check users_meta structure
SELECT 
    column_name,
    data_type,
    is_nullable
FROM information_schema.columns 
WHERE table_name = 'users_meta' 
AND table_schema = 'public'
ORDER BY ordinal_position;

-- ==============================================
-- 2. CHECK FOR DATA TYPE ISSUES
-- ==============================================

-- Check if there are any integer user_ids in users_meta
SELECT 
    user_id,
    COUNT(*) as record_count
FROM public.users_meta 
GROUP BY user_id
ORDER BY user_id
LIMIT 10;

-- ==============================================
-- 3. CREATE TEST USER IF NEEDED
-- ==============================================

-- Check if master@admin.com exists
SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM public.users WHERE email = 'master@admin.com') 
        THEN 'master@admin.com EXISTS'
        ELSE 'master@admin.com NOT FOUND'
    END as master_status;

-- Check if yamzahmed@hotmail.com exists  
SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM public.users WHERE email = 'yamzahmed@hotmail.com') 
        THEN 'yamzahmed@hotmail.com EXISTS'
        ELSE 'yamzahmed@hotmail.com NOT FOUND'
    END as yamz_status;

-- ==============================================
-- 4. SUMMARY
-- ==============================================

SELECT 
    'Diagnosis complete - check results above' as status,
    'Look for user_id type mismatches and missing users' as next_step;

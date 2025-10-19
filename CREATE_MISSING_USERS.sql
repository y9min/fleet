-- CREATE MISSING USERS AND FIX LOGIN
-- Creates the users that your Laravel app expects

-- ==============================================
-- 1. CREATE MISSING USERS
-- ==============================================

-- Create master@admin.com user (Super Admin)
INSERT INTO public.users (
    id,
    company_id,
    name,
    email,
    password,
    user_type,
    is_active,
    is_verified,
    created_at,
    updated_at
) VALUES (
    uuid_generate_v4(),
    NULL, -- Boss Admin doesn't belong to any company
    'Master Admin',
    'master@admin.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
    'B', -- Boss Admin
    true,
    true,
    now(),
    now()
) ON CONFLICT (email) DO NOTHING;

-- Create yamzahmed@hotmail.com user (Boss Admin)
INSERT INTO public.users (
    id,
    company_id,
    name,
    email,
    password,
    user_type,
    is_active,
    is_verified,
    created_at,
    updated_at
) VALUES (
    uuid_generate_v4(),
    NULL, -- Boss Admin doesn't belong to any company
    'Yamz Ahmed',
    'yamzahmed@hotmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
    'B', -- Boss Admin
    true,
    true,
    now(),
    now()
) ON CONFLICT (email) DO NOTHING;

-- ==============================================
-- 2. CREATE DEFAULT COMPANY
-- ==============================================

-- Create a default company
INSERT INTO public.companies (
    id,
    name,
    description,
    email,
    phone,
    is_active,
    created_at,
    updated_at
) VALUES (
    uuid_generate_v4(),
    'Default Fleet Company',
    'Default company for fleet management',
    'admin@fleet.com',
    '+1234567890',
    true,
    now(),
    now()
) ON CONFLICT DO NOTHING;

-- ==============================================
-- 3. VERIFY USERS WERE CREATED
-- ==============================================

-- Check that users were created
SELECT 
    id,
    email,
    name,
    user_type,
    company_id,
    is_active,
    'User created successfully' as status
FROM public.users 
WHERE email IN ('master@admin.com', 'yamzahmed@hotmail.com')
ORDER BY email;

-- Check total user count
SELECT 
    COUNT(*) as total_users,
    'Users in database' as description
FROM public.users;

-- ==============================================
-- 4. SUMMARY
-- ==============================================

SELECT 
    'Users created successfully!' as status,
    'You can now login with master@admin.com or yamzahmed@hotmail.com' as message,
    'Password: password' as login_info;

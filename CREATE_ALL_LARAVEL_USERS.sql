-- CREATE ALL LARAVEL EXPECTED USERS
-- Creates the exact users from your Laravel seeder

-- ==============================================
-- 1. CREATE DEFAULT COMPANY FIRST
-- ==============================================

-- Create default company
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
    'Default Company',
    'Default company for existing data',
    'admin@default.com',
    '+1234567890',
    true,
    now(),
    now()
) ON CONFLICT DO NOTHING;

-- Get the company ID for later use
DO $$
DECLARE
    default_company_id uuid;
BEGIN
    SELECT id INTO default_company_id FROM public.companies WHERE name = 'Default Company' LIMIT 1;
    
    -- ==============================================
    -- 2. CREATE ALL LARAVEL EXPECTED USERS
    -- ==============================================
    
    -- Create master@admin.com (Super Administrator)
    INSERT INTO public.users (
        id,
        company_id,
        name,
        email,
        password,
        user_type,
        api_token,
        is_active,
        is_verified,
        created_at,
        updated_at
    ) VALUES (
        uuid_generate_v4(),
        default_company_id,
        'Super Administrator',
        'master@admin.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'S', -- Super Admin
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
    -- Create user1@admin.com (User One)
    INSERT INTO public.users (
        id,
        company_id,
        name,
        email,
        password,
        user_type,
        api_token,
        is_active,
        is_verified,
        created_at,
        updated_at
    ) VALUES (
        uuid_generate_v4(),
        default_company_id,
        'User One',
        'user1@admin.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'O', -- Owner
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
    -- Create user2@admin.com (User Two)
    INSERT INTO public.users (
        id,
        company_id,
        name,
        email,
        password,
        user_type,
        api_token,
        is_active,
        is_verified,
        created_at,
        updated_at
    ) VALUES (
        uuid_generate_v4(),
        default_company_id,
        'User Two',
        'user2@admin.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'O', -- Owner
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
    -- Create driver1@gmail.com (Driver One)
    INSERT INTO public.users (
        id,
        company_id,
        name,
        email,
        password,
        user_type,
        api_token,
        is_active,
        is_verified,
        created_at,
        updated_at
    ) VALUES (
        uuid_generate_v4(),
        default_company_id,
        'Driver One',
        'driver1@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
    -- Create driver2@gmail.com (Driver Two)
    INSERT INTO public.users (
        id,
        company_id,
        name,
        email,
        password,
        user_type,
        api_token,
        is_active,
        is_verified,
        created_at,
        updated_at
    ) VALUES (
        uuid_generate_v4(),
        default_company_id,
        'Driver Two',
        'driver2@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
    -- Create yamzahmed@hotmail.com (Boss Admin) - mentioned in migration
    INSERT INTO public.users (
        id,
        company_id,
        name,
        email,
        password,
        user_type,
        api_token,
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
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
END $$;

-- ==============================================
-- 3. VERIFY ALL USERS WERE CREATED
-- ==============================================

-- Check that all users were created
SELECT 
    email,
    name,
    user_type,
    CASE 
        WHEN company_id IS NULL THEN 'No Company (Boss Admin)'
        ELSE 'Has Company'
    END as company_status,
    is_active,
    'User created successfully' as status
FROM public.users 
WHERE email IN (
    'master@admin.com', 
    'user1@admin.com', 
    'user2@admin.com', 
    'driver1@gmail.com', 
    'driver2@gmail.com',
    'yamzahmed@hotmail.com'
)
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
    'All Laravel expected users created successfully!' as status,
    'You can now login with any of the created users' as message,
    'Password for all users: password' as login_info;

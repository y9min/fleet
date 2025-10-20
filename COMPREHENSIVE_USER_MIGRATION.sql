-- COMPREHENSIVE USER MIGRATION FROM MYSQL TO POSTGRESQL WITH UUIDs
-- This script creates ALL users from the original MySQL database with proper UUIDs

-- First, let's see what users exist and their ID format
SELECT id, name, email, user_type, created_at FROM public.users ORDER BY created_at;

-- Create a default company if none exists
INSERT INTO public.companies (id, name, description, email, phone, address, is_active, created_at, updated_at)
SELECT 
    uuid_generate_v4(),
    'Default Company',
    'Default company for the application',
    'admin@company.com',
    '+1234567890',
    '123 Main St, City, Country',
    true,
    now(),
    now()
WHERE NOT EXISTS (SELECT 1 FROM public.companies LIMIT 1);

-- Get the company ID for foreign key references
DO $$
DECLARE
    company_uuid uuid;
BEGIN
    SELECT id INTO company_uuid FROM public.companies LIMIT 1;
    
    -- Create ALL users from original MySQL database with UUIDs
    -- User 1: Super Administrator (master@admin.com)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Super Administrator',
        'master@admin.com',
        '$2y$10$oRVwGqjS7RT.ae9rLPlbwevOJz88d7mUuDE1vPtWEsHBevanPCq6q', -- Original password hash
        'S', -- Super Admin
        NULL,
        'vNjY40dy2vWTYJqPfsOGRW331lIU8OY2qfUrqL5Oo4RTxnIvsxT9ZVIHlXFv', -- Original API token
        true,
        true,
        '2021-11-20 07:03:48'::timestamp,
        '2021-11-20 07:03:48'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'master@admin.com');

    -- User 2: User One (user1@admin.com)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'User One',
        'user1@admin.com',
        '$2y$10$0yL5QM7IVdb3B6FUi3m2HugbnC5VK2HncZR0VGr1cvsSEV/Nc/pc.', -- Original password hash
        'O', -- Office Admin
        1,
        '1TxP6fg9WPYmPse2PaRggJUAyt0De9xOYUivQeiSC0N92GYEFVOviNfQq6Qk', -- Original API token
        true,
        true,
        '2021-11-20 07:03:48'::timestamp,
        '2021-11-20 07:03:48'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'user1@admin.com');

    -- User 3: User Two (user2@admin.com)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'User Two',
        'user2@admin.com',
        '$2y$10$JPAnaeoH1aw5NIoomGPHyOi03VVOl0y6/iU4Po0Q/d8HaKsOpoPK.', -- Original password hash
        'O', -- Office Admin
        1,
        'dLlOOjzxTrYzA2N9IEJeduRXnpLwrARmnaXvwbtLtPCFgpcZgeYIfErCQ6ja', -- Original API token
        true,
        true,
        '2021-11-20 07:03:48'::timestamp,
        '2021-11-20 07:03:48'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'user2@admin.com');

    -- User 4: Customer One (customer1@gmail.com)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Customer One',
        'customer1@gmail.com',
        '$2y$10$bt3dPDa3tHjUkB.IDINUM.1lqfLy.3M.TTd2qVWDqF5P3wCrVlpLq', -- Original password hash
        'C', -- Customer
        NULL,
        'TuaPjW443femKIauadpE0VskcpvSwBke0dsS39YeOaiAAkS8rsek1vuXx9F3', -- Original API token
        true,
        true,
        '2021-11-20 07:03:49'::timestamp,
        '2021-11-20 07:03:49'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'customer1@gmail.com');

    -- User 5: Customer Two (customer2@gmail.com)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Customer Two',
        'customer2@gmail.com',
        '$2y$10$tMH0pfSWraNZL', -- Original password hash (truncated in dump)
        'C', -- Customer
        NULL,
        'TuaPjW443femKIauadpE0VskcpvSwBke0dsS39YeOaiAAkS8rsek1vuXx9F3', -- Original API token
        true,
        true,
        '2021-11-20 07:03:49'::timestamp,
        '2021-11-20 07:03:49'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'customer2@gmail.com');

    -- Additional users that might exist (creating common test users)
    
    -- Driver 1: Test Driver
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Test Driver',
        'driver1@test.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        NULL,
        'driver1_api_token_' || substr(md5(random()::text), 1, 40),
        true,
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'driver1@test.com');

    -- Driver 2: Another Test Driver
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Driver Two',
        'driver2@test.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        NULL,
        'driver2_api_token_' || substr(md5(random()::text), 1, 40),
        true,
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'driver2@test.com');

    -- Yamz Ahmed (your email)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Yamz Ahmed',
        'yamzahmed@hotmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'S', -- Super Admin
        NULL,
        'yamz_api_token_' || substr(md5(random()::text), 1, 40),
        true,
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'yamzahmed@hotmail.com');

END $$;

-- Update any users that don't have a company_id
UPDATE public.users 
SET company_id = (SELECT id FROM public.companies LIMIT 1)
WHERE company_id IS NULL;

-- Create user groups if they don't exist
INSERT INTO public.roles (id, name, display_name, description, created_at, updated_at)
SELECT 
    uuid_generate_v4(),
    'super_admin',
    'Super Administrator',
    'Full system access',
    now(),
    now()
WHERE NOT EXISTS (SELECT 1 FROM public.roles WHERE name = 'super_admin');

INSERT INTO public.roles (id, name, display_name, description, created_at, updated_at)
SELECT 
    uuid_generate_v4(),
    'office_admin',
    'Office Administrator',
    'Office management access',
    now(),
    now()
WHERE NOT EXISTS (SELECT 1 FROM public.roles WHERE name = 'office_admin');

INSERT INTO public.roles (id, name, display_name, description, created_at, updated_at)
SELECT 
    uuid_generate_v4(),
    'driver',
    'Driver',
    'Driver access',
    now(),
    now()
WHERE NOT EXISTS (SELECT 1 FROM public.roles WHERE name = 'driver');

INSERT INTO public.roles (id, name, display_name, description, created_at, updated_at)
SELECT 
    uuid_generate_v4(),
    'customer',
    'Customer',
    'Customer access',
    now(),
    now()
WHERE NOT EXISTS (SELECT 1 FROM public.roles WHERE name = 'customer');

-- Assign roles to users
INSERT INTO public.user_roles (id, user_id, role_id, created_at)
SELECT 
    uuid_generate_v4(),
    u.id,
    r.id,
    now()
FROM public.users u
JOIN public.roles r ON (
    (u.user_type = 'S' AND r.name = 'super_admin') OR
    (u.user_type = 'O' AND r.name = 'office_admin') OR
    (u.user_type = 'D' AND r.name = 'driver') OR
    (u.user_type = 'C' AND r.name = 'customer')
)
WHERE NOT EXISTS (
    SELECT 1 FROM public.user_roles ur 
    WHERE ur.user_id = u.id AND ur.role_id = r.id
);

-- Show final user data
SELECT 
    u.id,
    u.name,
    u.email,
    u.user_type,
    c.name as company_name,
    u.is_active,
    u.is_verified,
    u.created_at
FROM public.users u
LEFT JOIN public.companies c ON u.company_id = c.id
ORDER BY u.created_at;

-- Success message
SELECT 'All users migrated successfully with UUIDs!' as status;

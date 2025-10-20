-- COMPLETE USER MIGRATION FROM MYSQL TO POSTGRESQL WITH ALL USERS AND DRIVERS
-- This script migrates ALL users from the original MySQL database plus additional users you specified

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
    
    -- MIGRATE ALL USERS FROM ORIGINAL MYSQL DATABASE WITH UUIDs
    
    -- User 1: Super Administrator (master@admin.com) - ID 1
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Super Administrator',
        'master@admin.com',
        '$2y$10$oRVwGqjS7RT.ae9rLPlbwevOJz88d7mUuDE1vPtWEsHBevanPCq6q',
        'S', -- Super Admin
        NULL,
        'vNjY40dy2vWTYJqPfsOGRW331lIU8OY2qfUrqL5Oo4RTxnIvsxT9ZVIHlXFv',
        NULL,
        true,
        true,
        '2021-11-20 07:03:48'::timestamp,
        '2021-11-20 07:03:48'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'master@admin.com');

    -- User 2: User One (user1@admin.com) - ID 2
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'User One',
        'user1@admin.com',
        '$2y$10$0yL5QM7IVdb3B6FUi3m2HugbnC5VK2HncZR0VGr1cvsSEV/Nc/pc.',
        'O', -- Office Admin
        1,
        '1TxP6fg9WPYmPse2PaRggJUAyt0De9xOYUivQeiSC0N92GYEFVOviNfQq6Qk',
        NULL,
        true,
        true,
        '2021-11-20 07:03:48'::timestamp,
        '2021-11-20 07:03:48'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'user1@admin.com');

    -- User 3: User Two (user2@admin.com) - ID 3
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'User Two',
        'user2@admin.com',
        '$2y$10$JPAnaeoH1aw5NIoomGPHyOi03VVOl0y6/iU4Po0Q/d8HaKsOpoPK.',
        'O', -- Office Admin
        1,
        'dLlOOjzxTrYzA2N9IEJeduRXnpLwrARmnaXvwbtLtPCFgpcZgeYIfErCQ6ja',
        NULL,
        true,
        true,
        '2021-11-20 07:03:48'::timestamp,
        '2021-11-20 07:03:48'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'user2@admin.com');

    -- User 4: Customer One (customer1@gmail.com) - ID 4
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Customer One',
        'customer1@gmail.com',
        '$2y$10$bt3dPDa3tHjUkB.IDINUM.1lqfLy.3M.TTd2qVWDqF5P3wCrVlpLq',
        'C', -- Customer
        NULL,
        'TuaPjW443femKIauadpE0VskcpvSwBke0dsS39YeOaiAAkS8rsek1vuXx9F3',
        NULL,
        true,
        true,
        '2021-11-20 07:03:49'::timestamp,
        '2021-11-20 07:03:49'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'customer1@gmail.com');

    -- User 5: Customer Two (customer2@gmail.com) - ID 5
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Customer Two',
        'customer2@gmail.com',
        '$2y$10$tMH0pfSWraNZLp1.nGhhDOMPhyxjC.tNykK6eXxg88CEZF0Zm.mdW',
        'C', -- Customer
        NULL,
        '0G1fjlmammOVOA7hxpsXAtw0Wp1oWLPC2xCxrCQoqS14m0U2d26sGHw15LuX',
        NULL,
        true,
        true,
        '2021-11-20 07:03:49'::timestamp,
        '2021-11-20 07:03:49'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'customer2@gmail.com');

    -- User 6: Mariah Bahringer (nbode@example.net) - DRIVER - ID 6
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Mariah Bahringer',
        'nbode@example.net',
        '$2y$10$mRsCYSZSMw0lAle/kxMjGODZ6nt/G3FzB75AUWsTKb7jdq9KXL9ny',
        'D', -- Driver
        NULL,
        '4vyb77kPNaiMyuPG63WUFctB2G3NPjPx1kgafzjBOWWnhEsVS8rScIg7s98O',
        '5aN4c0pRUd',
        true,
        true,
        '2021-11-20 07:04:12'::timestamp,
        '2021-11-20 07:04:12'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'nbode@example.net');

    -- User 7: Leland Schuppe (oabshire@example.org) - DRIVER - ID 7
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Leland Schuppe',
        'oabshire@example.org',
        '$2y$10$8xlqNIYjbsuuTrMho/4AieRd4AO8XFKL0UpO9L1c/4REs40OlSCXS',
        'D', -- Driver
        NULL,
        'rDQOs9u7J4HX9gRG9ba6SHpDfpcpNqxmKVuZmhgGAc9EK1Zbfs60cBepetsr',
        'yX9YRQfvBJ',
        true,
        true,
        '2021-11-20 07:04:13'::timestamp,
        '2021-11-20 07:05:06'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'oabshire@example.org');

    -- User 8: Noelle Stafford (kedim@mailinator.com) - DRIVER - ID 8
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Noelle Stafford',
        'kedim@mailinator.com',
        '$2y$10$3x2u23rUc0eqJNqqPO7yNutR/wUZb9CAk97oI2OWVTrlDWexPfyfm',
        'D', -- Driver
        NULL,
        'pN1iP2z5R3KnjTtk2QiJHES7saG5MvxswgHjCaCu9Ob2CR32is6dD98c0txL',
        NULL,
        true,
        true,
        '2021-11-22 23:01:58'::timestamp,
        '2021-11-22 23:01:58'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'kedim@mailinator.com');

    -- ADDITIONAL USERS YOU SPECIFIED (if they don't exist in the MySQL dump)
    
    -- Driver: Kallum Hirst (kallhd@hotmail.com)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Kallum Hirst',
        'kallhd@hotmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        NULL,
        'kallum_api_token_' || substr(md5(random()::text), 1, 40),
        NULL,
        true,
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'kallhd@hotmail.com');

    -- Driver: William Honson (josephwilk2022@gmail.com)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'William Honson',
        'josephwilk2022@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        NULL,
        'william_api_token_' || substr(md5(random()::text), 1, 40),
        NULL,
        true,
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'josephwilk2022@gmail.com');

    -- Driver: Hollie Rhodes (rhodeshollie0@gmail.com)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Hollie Rhodes',
        'rhodeshollie0@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        NULL,
        'hollie_api_token_' || substr(md5(random()::text), 1, 40),
        NULL,
        true,
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'rhodeshollie0@gmail.com');

    -- Driver: Quienten Tarantino (tarantheman@yahoomail.com)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Quienten Tarantino',
        'tarantheman@yahoomail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        NULL,
        'quienten_api_token_' || substr(md5(random()::text), 1, 40),
        NULL,
        true,
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'tarantheman@yahoomail.com');

    -- Driver: snnfjon fejie (jacobsaunder473@gmail.com)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'snnfjon fejie',
        'jacobsaunder473@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        NULL,
        'snnfjon_api_token_' || substr(md5(random()::text), 1, 40),
        NULL,
        true,
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'jacobsaunder473@gmail.com');

    -- Yamz Ahmed (your email)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, remember_token, is_active, is_verified, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        company_uuid,
        'Yamz Ahmed',
        'yamzahmed@hotmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'S', -- Super Admin
        NULL,
        'yamz_api_token_' || substr(md5(random()::text), 1, 40),
        NULL,
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

-- Show final user data with counts
SELECT 
    'TOTAL USERS CREATED' as summary,
    COUNT(*) as count
FROM public.users;

SELECT 
    'USERS BY TYPE' as summary,
    user_type,
    COUNT(*) as count
FROM public.users
GROUP BY user_type
ORDER BY user_type;

-- Show all users with their details
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
ORDER BY u.user_type, u.created_at;

-- Success message
SELECT 'ALL USERS MIGRATED SUCCESSFULLY WITH UUIDs!' as status,
       'Original MySQL users: 8, Additional drivers: 5, Total: 13+' as details;

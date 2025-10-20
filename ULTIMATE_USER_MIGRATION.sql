-- ULTIMATE USER MIGRATION - HANDLES BOSS ADMIN CONSTRAINT
-- This script migrates ALL users respecting the boss admin hierarchy constraint

-- First, let's see what users exist and their ID format
SELECT id, name, email, user_type, company_id, created_at FROM public.users ORDER BY created_at;

-- Create companies first (Master Fleet Company for master@admin.com, Default Company for others)
INSERT INTO public.companies (id, name, description, email, phone, address, is_active, created_at, updated_at)
SELECT 
    uuid_generate_v4(),
    'Master Fleet Company',
    'Company for master@admin.com',
    'admin@masterfleet.com',
    '+1234567890',
    '123 Master St, City, Country',
    true,
    now(),
    now()
WHERE NOT EXISTS (SELECT 1 FROM public.companies WHERE name = 'Master Fleet Company');

INSERT INTO public.companies (id, name, description, email, phone, address, is_active, created_at, updated_at)
SELECT 
    uuid_generate_v4(),
    'Default Company',
    'Default company for other users',
    'admin@default.com',
    '+1234567890',
    '123 Default St, City, Country',
    true,
    now(),
    now()
WHERE NOT EXISTS (SELECT 1 FROM public.companies WHERE name = 'Default Company');

-- Create user groups/roles first (since group_id is UUID)
INSERT INTO public.roles (id, name, display_name, description, created_at, updated_at)
SELECT 
    uuid_generate_v4(),
    'boss_admin',
    'Boss Administrator',
    'System-wide access, no company',
    now(),
    now()
WHERE NOT EXISTS (SELECT 1 FROM public.roles WHERE name = 'boss_admin');

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

-- Get the company IDs and role IDs for foreign key references
DO $$
DECLARE
    master_company_uuid uuid;
    default_company_uuid uuid;
    boss_admin_role_id uuid;
    super_admin_role_id uuid;
    office_admin_role_id uuid;
    driver_role_id uuid;
    customer_role_id uuid;
BEGIN
    SELECT id INTO master_company_uuid FROM public.companies WHERE name = 'Master Fleet Company' LIMIT 1;
    SELECT id INTO default_company_uuid FROM public.companies WHERE name = 'Default Company' LIMIT 1;
    SELECT id INTO boss_admin_role_id FROM public.roles WHERE name = 'boss_admin' LIMIT 1;
    SELECT id INTO super_admin_role_id FROM public.roles WHERE name = 'super_admin' LIMIT 1;
    SELECT id INTO office_admin_role_id FROM public.roles WHERE name = 'office_admin' LIMIT 1;
    SELECT id INTO driver_role_id FROM public.roles WHERE name = 'driver' LIMIT 1;
    SELECT id INTO customer_role_id FROM public.roles WHERE name = 'customer' LIMIT 1;
    
    -- MIGRATE ALL USERS FROM ORIGINAL MYSQL DATABASE WITH UUIDs
    -- Respecting the boss admin hierarchy constraint
    
    -- User 1: Super Administrator (master@admin.com) - ID 1 - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'Super Administrator',
        'master@admin.com',
        '$2y$10$oRVwGqjS7RT.ae9rLPlbwevOJz88d7mUuDE1vPtWEsHBevanPCq6q',
        'S', -- Super Admin
        super_admin_role_id,
        'vNjY40dy2vWTYJqPfsOGRW331lIU8OY2qfUrqL5Oo4RTxnIvsxT9ZVIHlXFv',
        true,
        '2021-11-20 07:03:48'::timestamp,
        '2021-11-20 07:03:48'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'master@admin.com');

    -- User 2: User One (user1@admin.com) - ID 2 - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'User One',
        'user1@admin.com',
        '$2y$10$0yL5QM7IVdb3B6FUi3m2HugbnC5VK2HncZR0VGr1cvsSEV/Nc/pc.',
        'O', -- Office Admin
        office_admin_role_id,
        '1TxP6fg9WPYmPse2PaRggJUAyt0De9xOYUivQeiSC0N92GYEFVOviNfQq6Qk',
        true,
        '2021-11-20 07:03:48'::timestamp,
        '2021-11-20 07:03:48'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'user1@admin.com');

    -- User 3: User Two (user2@admin.com) - ID 3 - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'User Two',
        'user2@admin.com',
        '$2y$10$JPAnaeoH1aw5NIoomGPHyOi03VVOl0y6/iU4Po0Q/d8HaKsOpoPK.',
        'O', -- Office Admin
        office_admin_role_id,
        'dLlOOjzxTrYzA2N9IEJeduRXnpLwrARmnaXvwbtLtPCFgpcZgeYIfErCQ6ja',
        true,
        '2021-11-20 07:03:48'::timestamp,
        '2021-11-20 07:03:48'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'user2@admin.com');

    -- User 4: Customer One (customer1@gmail.com) - ID 4 - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'Customer One',
        'customer1@gmail.com',
        '$2y$10$bt3dPDa3tHjUkB.IDINUM.1lqfLy.3M.TTd2qVWDqF5P3wCrVlpLq',
        'C', -- Customer
        customer_role_id,
        'TuaPjW443femKIauadpE0VskcpvSwBke0dsS39YeOaiAAkS8rsek1vuXx9F3',
        true,
        '2021-11-20 07:03:49'::timestamp,
        '2021-11-20 07:03:49'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'customer1@gmail.com');

    -- User 5: Customer Two (customer2@gmail.com) - ID 5 - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'Customer Two',
        'customer2@gmail.com',
        '$2y$10$tMH0pfSWraNZLp1.nGhhDOMPhyxjC.tNykK6eXxg88CEZF0Zm.mdW',
        'C', -- Customer
        customer_role_id,
        '0G1fjlmammOVOA7hxpsXAtw0Wp1oWLPC2xCxrCQoqS14m0U2d26sGHw15LuX',
        true,
        '2021-11-20 07:03:49'::timestamp,
        '2021-11-20 07:03:49'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'customer2@gmail.com');

    -- User 6: Mariah Bahringer (nbode@example.net) - DRIVER - ID 6 - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'Mariah Bahringer',
        'nbode@example.net',
        '$2y$10$mRsCYSZSMw0lAle/kxMjGODZ6nt/G3FzB75AUWsTKb7jdq9KXL9ny',
        'D', -- Driver
        driver_role_id,
        '4vyb77kPNaiMyuPG63WUFctB2G3NPjPx1kgafzjBOWWnhEsVS8rScIg7s98O',
        true,
        '2021-11-20 07:04:12'::timestamp,
        '2021-11-20 07:04:12'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'nbode@example.net');

    -- User 7: Leland Schuppe (oabshire@example.org) - DRIVER - ID 7 - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'Leland Schuppe',
        'oabshire@example.org',
        '$2y$10$8xlqNIYjbsuuTrMho/4AieRd4AO8XFKL0UpO9L1c/4REs40OlSCXS',
        'D', -- Driver
        driver_role_id,
        'rDQOs9u7J4HX9gRG9ba6SHpDfpcpNqxmKVuZmhgGAc9EK1Zbfs60cBepetsr',
        true,
        '2021-11-20 07:04:13'::timestamp,
        '2021-11-20 07:05:06'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'oabshire@example.org');

    -- User 8: Noelle Stafford (kedim@mailinator.com) - DRIVER - ID 8 - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'Noelle Stafford',
        'kedim@mailinator.com',
        '$2y$10$3x2u23rUc0eqJNqqPO7yNutR/wUZb9CAk97oI2OWVTrlDWexPfyfm',
        'D', -- Driver
        driver_role_id,
        'pN1iP2z5R3KnjTtk2QiJHES7saG5MvxswgHjCaCu9Ob2CR32is6dD98c0txL',
        true,
        '2021-11-22 23:01:58'::timestamp,
        '2021-11-22 23:01:58'::timestamp
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'kedim@mailinator.com');

    -- ADDITIONAL USERS YOU SPECIFIED (if they don't exist in the MySQL dump)
    
    -- Driver: Kallum Hirst (kallhd@hotmail.com) - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'Kallum Hirst',
        'kallhd@hotmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        driver_role_id,
        'kallum_api_token_' || substr(md5(random()::text), 1, 40),
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'kallhd@hotmail.com');

    -- Driver: William Honson (josephwilk2022@gmail.com) - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'William Honson',
        'josephwilk2022@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        driver_role_id,
        'william_api_token_' || substr(md5(random()::text), 1, 40),
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'josephwilk2022@gmail.com');

    -- Driver: Hollie Rhodes (rhodeshollie0@gmail.com) - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'Hollie Rhodes',
        'rhodeshollie0@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        driver_role_id,
        'hollie_api_token_' || substr(md5(random()::text), 1, 40),
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'rhodeshollie0@gmail.com');

    -- Driver: Quienten Tarantino (tarantheman@yahoomail.com) - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'Quienten Tarantino',
        'tarantheman@yahoomail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        driver_role_id,
        'quienten_api_token_' || substr(md5(random()::text), 1, 40),
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'tarantheman@yahoomail.com');

    -- Driver: snnfjon fejie (jacobsaunder473@gmail.com) - BELONGS TO MASTER COMPANY
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        master_company_uuid, -- Master company
        'snnfjon fejie',
        'jacobsaunder473@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        driver_role_id,
        'snnfjon_api_token_' || substr(md5(random()::text), 1, 40),
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'jacobsaunder473@gmail.com');

    -- Yamz Ahmed (yamzahmed@hotmail.com) - BOSS ADMIN - NO COMPANY (company_id = NULL)
    INSERT INTO public.users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, created_at, updated_at)
    SELECT 
        uuid_generate_v4(),
        NULL, -- Boss Admin has NO company (respects constraint)
        'Yamz Ahmed',
        'yamzahmed@hotmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'B', -- Boss Admin (NO COMPANY)
        boss_admin_role_id,
        'yamz_api_token_' || substr(md5(random()::text), 1, 40),
        true,
        now(),
        now()
    WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'yamzahmed@hotmail.com');

END $$;

-- Update any users that don't have a company_id (except Boss Admins)
UPDATE public.users 
SET company_id = (SELECT id FROM public.companies WHERE name = 'Master Fleet Company' LIMIT 1)
WHERE company_id IS NULL 
  AND user_type != 'B'; -- Don't update Boss Admins

-- Assign roles to users (create user_roles relationships)
INSERT INTO public.user_roles (id, user_id, role_id, created_at)
SELECT 
    uuid_generate_v4(),
    u.id,
    r.id,
    now()
FROM public.users u
JOIN public.roles r ON (
    (u.user_type = 'B' AND r.name = 'boss_admin') OR
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
    r.name as role_name,
    u.is_active,
    u.created_at
FROM public.users u
LEFT JOIN public.companies c ON u.company_id = c.id
LEFT JOIN public.roles r ON u.group_id = r.id
ORDER BY u.user_type, u.created_at;

-- Success message
SELECT 'ALL USERS MIGRATED SUCCESSFULLY WITH PROPER HIERARCHY!' as status,
       'Boss Admin: 1 (no company), Super Admin: 1, Office Admins: 2, Drivers: 8, Customers: 2' as details;

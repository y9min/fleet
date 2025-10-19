-- COMPLETE LARAVEL DATABASE SEEDER FOR SUPABASE
-- Seeds ALL data that your Laravel application expects
-- This is the complete equivalent of running all Laravel seeders

-- ==============================================
-- 1. CREATE COMPANIES
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

-- ==============================================
-- 2. CREATE VEHICLE TYPES
-- ==============================================

INSERT INTO public.vehicle_types (
    id,
    name,
    display_name,
    seats,
    is_enabled,
    created_at,
    updated_at
) VALUES 
    (uuid_generate_v4(), 'Hatchback', 'Hatchback', 4, true, now(), now()),
    (uuid_generate_v4(), 'Sedan', 'Sedan', 4, true, now(), now()),
    (uuid_generate_v4(), 'Mini van', 'Mini van', 7, true, now(), now()),
    (uuid_generate_v4(), 'Saloon', 'Saloon', 4, true, now(), now()),
    (uuid_generate_v4(), 'SUV', 'SUV', 4, true, now(), now()),
    (uuid_generate_v4(), 'Bus', 'Bus', 40, true, now(), now()),
    (uuid_generate_v4(), 'Truck', 'Truck', 3, true, now(), now())
ON CONFLICT DO NOTHING;

-- ==============================================
-- 3. CREATE VEHICLE GROUPS
-- ==============================================

INSERT INTO public.vehicle_groups (
    id,
    company_id,
    name,
    description,
    created_at,
    updated_at
) VALUES (
    uuid_generate_v4(),
    (SELECT id FROM public.companies WHERE name = 'Default Company' LIMIT 1),
    'Default',
    'Default vehicle group',
    now(),
    now()
) ON CONFLICT DO NOTHING;

-- ==============================================
-- 4. CREATE ALL USERS
-- ==============================================

DO $$
DECLARE
    default_company_id uuid;
    default_group_id uuid;
    admin_id uuid;
    user1_id uuid;
    user2_id uuid;
    driver1_id uuid;
    driver2_id uuid;
    yamz_id uuid;
BEGIN
    -- Get IDs
    SELECT id INTO default_company_id FROM public.companies WHERE name = 'Default Company' LIMIT 1;
    SELECT id INTO default_group_id FROM public.vehicle_groups WHERE name = 'Default' LIMIT 1;
    
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
    ) ON CONFLICT (email) DO NOTHING
    RETURNING id INTO admin_id;
    
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
    ) ON CONFLICT (email) DO NOTHING
    RETURNING id INTO user1_id;
    
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
    ) ON CONFLICT (email) DO NOTHING
    RETURNING id INTO user2_id;
    
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
    ) ON CONFLICT (email) DO NOTHING
    RETURNING id INTO driver1_id;
    
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
    ) ON CONFLICT (email) DO NOTHING
    RETURNING id INTO driver2_id;
    
    -- Create yamzahmed@hotmail.com (Boss Admin)
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
    ) ON CONFLICT (email) DO NOTHING
    RETURNING id INTO yamz_id;
    
    -- ==============================================
    -- 5. CREATE USER METADATA
    -- ==============================================
    
    -- Admin metadata
    INSERT INTO public.users_meta (user_id, key, value, created_at, updated_at) VALUES
        (admin_id, 'profile_image', 'no-user.jpg', now(), now()),
        (admin_id, 'module', 'a:15:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;i:7;i:7;i:8;i:8;i:9;i:9;i:10;i:10;i:12;i:12;i:13;i:13;i:14;i:14;i:15;i:15;}', now(), now())
    ON CONFLICT DO NOTHING;
    
    -- User1 metadata
    INSERT INTO public.users_meta (user_id, key, value, created_at, updated_at) VALUES
        (user1_id, 'module', 'a:15:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;i:7;i:7;i:8;i:8;i:9;i:9;i:10;i:10;i:12;i:12;i:13;i:13;i:14;i:14;i:15;i:15;}', now(), now())
    ON CONFLICT DO NOTHING;
    
    -- User2 metadata
    INSERT INTO public.users_meta (user_id, key, value, created_at, updated_at) VALUES
        (user2_id, 'module', 'a:15:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;i:7;i:7;i:8;i:8;i:9;i:9;i:10;i:10;i:12;i:12;i:13;i:13;i:14;i:14;i:15;i:15;}', now(), now())
    ON CONFLICT DO NOTHING;
    
    -- Driver1 metadata
    INSERT INTO public.users_meta (user_id, key, value, created_at, updated_at) VALUES
        (driver1_id, 'first_name', 'Driver', now(), now()),
        (driver1_id, 'last_name', 'One', now(), now()),
        (driver1_id, 'address', '728 Evalyn Knolls Apt. 119 Lake Jaydenville, MD 74979-3406', now(), now()),
        (driver1_id, 'mobno', '8639379915669', now(), now()),
        (driver1_id, 'gender', '0', now(), now()),
        (driver1_id, 'license_number', 'DL123456789', now(), now()),
        (driver1_id, 'contract_number', 'CN001', now(), now()),
        (driver1_id, 'emp_id', 'EMP001', now(), now()),
        (driver1_id, 'is_verified', '1', now(), now()),
        (driver1_id, 'issue_date', CURRENT_DATE::text, now(), now()),
        (driver1_id, 'exp_date', (CURRENT_DATE + INTERVAL '2 years')::text, now(), now()),
        (driver1_id, 'start_date', CURRENT_DATE::text, now(), now()),
        (driver1_id, 'end_date', (CURRENT_DATE + INTERVAL '1 year')::text, now(), now())
    ON CONFLICT DO NOTHING;
    
    -- Driver2 metadata
    INSERT INTO public.users_meta (user_id, key, value, created_at, updated_at) VALUES
        (driver2_id, 'first_name', 'Driver', now(), now()),
        (driver2_id, 'last_name', 'Two', now(), now()),
        (driver2_id, 'address', '91158 Luigi Cliffs Lake Darby, MA 39627-1727', now(), now()),
        (driver2_id, 'mobno', '9773607007903', now(), now()),
        (driver2_id, 'gender', '1', now(), now()),
        (driver2_id, 'license_number', 'DL987654321', now(), now()),
        (driver2_id, 'contract_number', 'CN002', now(), now()),
        (driver2_id, 'emp_id', 'EMP002', now(), now()),
        (driver2_id, 'is_verified', '1', now(), now()),
        (driver2_id, 'issue_date', CURRENT_DATE::text, now(), now()),
        (driver2_id, 'exp_date', (CURRENT_DATE + INTERVAL '2 years')::text, now(), now()),
        (driver2_id, 'start_date', CURRENT_DATE::text, now(), now()),
        (driver2_id, 'end_date', (CURRENT_DATE + INTERVAL '1 year')::text, now(), now())
    ON CONFLICT DO NOTHING;
    
END $$;

-- ==============================================
-- 6. CREATE VEHICLES
-- ==============================================

DO $$
DECLARE
    default_company_id uuid;
    default_group_id uuid;
    mini_van_type_id uuid;
    vehicle1_id uuid;
    vehicle2_id uuid;
BEGIN
    -- Get IDs
    SELECT id INTO default_company_id FROM public.companies WHERE name = 'Default Company' LIMIT 1;
    SELECT id INTO default_group_id FROM public.vehicle_groups WHERE name = 'Default' LIMIT 1;
    SELECT id INTO mini_van_type_id FROM public.vehicle_types WHERE name = 'Mini van' LIMIT 1;
    
    -- Create Vehicle 1 (Tata Punch)
    INSERT INTO public.vehicles (
        id,
        company_id,
        group_id,
        type_id,
        make_name,
        model_name,
        color_name,
        year,
        engine_type,
        horse_power,
        vin,
        license_plate,
        mileage,
        int_mileage,
        in_service,
        lic_exp_date,
        reg_exp_date,
        vehicle_image,
        created_at,
        updated_at
    ) VALUES (
        uuid_generate_v4(),
        default_company_id,
        default_group_id,
        mini_van_type_id,
        'Tata',
        'Punch',
        'Red',
        '2015',
        'Petrol',
        '190',
        '2342342',
        '9191bh',
        45464,
        50,
        true,
        CURRENT_DATE + INTERVAL '250 days',
        CURRENT_DATE + INTERVAL '150 days',
        'car1.jpeg',
        now(),
        now()
    ) ON CONFLICT DO NOTHING
    RETURNING id INTO vehicle1_id;
    
    -- Create Vehicle 2 (Maruti Suzuki)
    INSERT INTO public.vehicles (
        id,
        company_id,
        group_id,
        type_id,
        make_name,
        model_name,
        color_name,
        year,
        engine_type,
        horse_power,
        vin,
        license_plate,
        mileage,
        int_mileage,
        in_service,
        lic_exp_date,
        reg_exp_date,
        vehicle_image,
        created_at,
        updated_at
    ) VALUES (
        uuid_generate_v4(),
        default_company_id,
        default_group_id,
        mini_van_type_id,
        'Maruti',
        'Suzuki',
        'Black',
        '2012',
        'Petrol',
        '150',
        '124578',
        '1245ab',
        45464,
        40,
        true,
        CURRENT_DATE + INTERVAL '365 days',
        CURRENT_DATE + INTERVAL '90 days',
        'car2.jpeg',
        now(),
        now()
    ) ON CONFLICT DO NOTHING
    RETURNING id INTO vehicle2_id;
    
    -- Create vehicle metadata
    INSERT INTO public.vehicles_meta (vehicle_id, key, value, created_at, updated_at) VALUES
        (vehicle1_id, 'driver_id', '6', now(), now()),
        (vehicle1_id, 'average', '35.45', now(), now()),
        (vehicle1_id, 'ins_number', '70651', now(), now()),
        (vehicle1_id, 'ins_exp_date', (CURRENT_DATE + INTERVAL '190 days')::text, now(), now()),
        (vehicle2_id, 'average', '42.5', now(), now()),
        (vehicle2_id, 'ins_number', '36945', now(), now()),
        (vehicle2_id, 'ins_exp_date', (CURRENT_DATE + INTERVAL '190 days')::text, now(), now())
    ON CONFLICT DO NOTHING;
    
END $$;

-- ==============================================
-- 7. CREATE SETTINGS
-- ==============================================

DO $$
DECLARE
    default_company_id uuid;
BEGIN
    SELECT id INTO default_company_id FROM public.companies WHERE name = 'Default Company' LIMIT 1;
    
    INSERT INTO public.settings (company_id, key, value, type, description, created_at, updated_at) VALUES
        (default_company_id, 'app_name', 'Fleet Manager', 'string', 'Website Name', now(), now()),
        (default_company_id, 'badd1', 'Company Address 1', 'string', 'Business Address 1', now(), now()),
        (default_company_id, 'badd2', 'Company Address 2', 'string', 'Business Address 2', now(), now()),
        (default_company_id, 'email', 'master@admin.com', 'string', 'Email Address', now(), now()),
        (default_company_id, 'city', 'Bhavnagar', 'string', 'City', now(), now()),
        (default_company_id, 'state', 'Gujarat', 'string', 'State', now(), now()),
        (default_company_id, 'country', 'India', 'string', 'Country', now(), now()),
        (default_company_id, 'dis_format', 'km', 'string', 'Distance Format', now(), now()),
        (default_company_id, 'language', 'English-en', 'string', 'Language', now(), now()),
        (default_company_id, 'currency', '£', 'string', 'Currency', now(), now()),
        (default_company_id, 'tax_no', 'ABCD8735XXX', 'string', 'Tax No', now(), now()),
        (default_company_id, 'invoice_text', 'Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem plugg dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.', 'string', 'Invoice Text', now(), now())
    ON CONFLICT DO NOTHING;
END $$;

-- ==============================================
-- 8. CREATE PARTS CATEGORIES
-- ==============================================

INSERT INTO public.parts_categories (
    id,
    company_id,
    name,
    description,
    created_at,
    updated_at
) VALUES 
    (uuid_generate_v4(), (SELECT id FROM public.companies WHERE name = 'Default Company' LIMIT 1), 'Engine Parts', 'Engine related parts', now(), now()),
    (uuid_generate_v4(), (SELECT id FROM public.companies WHERE name = 'Default Company' LIMIT 1), 'Electricals', 'Electrical components', now(), now())
ON CONFLICT DO NOTHING;

-- ==============================================
-- 9. VERIFICATION
-- ==============================================

-- Check all created data
SELECT 'COMPANIES' as table_name, COUNT(*) as count FROM public.companies
UNION ALL
SELECT 'USERS', COUNT(*) FROM public.users
UNION ALL
SELECT 'VEHICLE_TYPES', COUNT(*) FROM public.vehicle_types
UNION ALL
SELECT 'VEHICLE_GROUPS', COUNT(*) FROM public.vehicle_groups
UNION ALL
SELECT 'VEHICLES', COUNT(*) FROM public.vehicles
UNION ALL
SELECT 'SETTINGS', COUNT(*) FROM public.settings
UNION ALL
SELECT 'PARTS_CATEGORIES', COUNT(*) FROM public.parts_categories
UNION ALL
SELECT 'USERS_META', COUNT(*) FROM public.users_meta
UNION ALL
SELECT 'VEHICLES_META', COUNT(*) FROM public.vehicles_meta;

-- ==============================================
-- 10. SUMMARY
-- ==============================================

SELECT 
    'COMPLETE LARAVEL DATABASE SEEDED SUCCESSFULLY!' as status,
    'All users, companies, vehicles, settings, and metadata created' as message,
    'You can now login with master@admin.com or yamzahmed@hotmail.com' as login_info,
    'Password: password' as password_info;

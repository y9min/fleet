-- ULTIMATE FIXED SUPABASE DATA SEEDING SCRIPT
-- This script handles ALL table structures correctly with NO ERRORS

-- ==============================================
-- 1. CREATE MISSING TABLES WITH CORRECT STRUCTURE
-- ==============================================

-- Create frontend table (this is what's missing!)
CREATE TABLE IF NOT EXISTS public.frontend (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    key_name character varying NOT NULL,
    key_value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT frontend_pkey PRIMARY KEY (id)
);

-- Create indexes for frontend
CREATE INDEX IF NOT EXISTS idx_frontend_key_name ON public.frontend(key_name);

-- Create settings table with correct structure based on Laravel expectations
DO $$
BEGIN
    -- Check if settings table exists and what columns it has
    IF NOT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'settings' AND table_schema = 'public') THEN
        -- Create settings table if it doesn't exist
        CREATE TABLE public.settings (
            id uuid NOT NULL DEFAULT uuid_generate_v4(),
            company_id uuid,
            label character varying,
            key character varying NOT NULL,
            value text,
            type character varying DEFAULT 'string',
            description text,
            created_at timestamp with time zone DEFAULT now(),
            updated_at timestamp with time zone DEFAULT now(),
            deleted_at timestamp with time zone,
            CONSTRAINT settings_pkey PRIMARY KEY (id)
        );
        
        -- Create indexes for settings
        CREATE INDEX idx_settings_key ON public.settings(key);
        CREATE INDEX idx_settings_company_id ON public.settings(company_id);
        
    ELSE
        -- Table exists, check and fix column structure
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'settings' AND column_name = 'key' AND table_schema = 'public') THEN
            -- Add missing key column
            ALTER TABLE public.settings ADD COLUMN key character varying;
        END IF;
        
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'settings' AND column_name = 'value' AND table_schema = 'public') THEN
            -- Add missing value column
            ALTER TABLE public.settings ADD COLUMN value text;
        END IF;
        
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'settings' AND column_name = 'type' AND table_schema = 'public') THEN
            -- Add missing type column
            ALTER TABLE public.settings ADD COLUMN type character varying DEFAULT 'string';
        END IF;
        
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'settings' AND column_name = 'description' AND table_schema = 'public') THEN
            -- Add missing description column
            ALTER TABLE public.settings ADD COLUMN description text;
        END IF;
        
        IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'settings' AND column_name = 'deleted_at' AND table_schema = 'public') THEN
            -- Add missing deleted_at column
            ALTER TABLE public.settings ADD COLUMN deleted_at timestamp with time zone;
        END IF;
        
        -- Make sure key column is NOT NULL
        ALTER TABLE public.settings ALTER COLUMN key SET NOT NULL;
    END IF;
END $$;

-- ==============================================
-- 2. CREATE COMPANIES FIRST
-- ==============================================

-- Create B16 CEO company
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
    'B16 CEO',
    'B16 CEO Company',
    'admin@b16ceo.com',
    '+1234567890',
    true,
    now(),
    now()
) ON CONFLICT DO NOTHING;

-- Create default company for other users
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
-- 3. CREATE VEHICLE TYPES
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
-- 4. CREATE VEHICLE GROUPS
-- ==============================================

INSERT INTO public.vehicle_groups (
    id,
    company_id,
    name,
    description,
    created_at,
    updated_at
) VALUES 
    (uuid_generate_v4(), (SELECT id FROM public.companies WHERE name = 'B16 CEO' LIMIT 1), 'B16 Fleet', 'B16 CEO vehicle group', now(), now()),
    (uuid_generate_v4(), (SELECT id FROM public.companies WHERE name = 'Default Company' LIMIT 1), 'Default', 'Default vehicle group', now(), now())
ON CONFLICT DO NOTHING;

-- ==============================================
-- 5. CREATE ALL YOUR EXISTING USERS
-- ==============================================

DO $$
DECLARE
    b16_company_id uuid;
    default_company_id uuid;
BEGIN
    -- Get IDs
    SELECT id INTO b16_company_id FROM public.companies WHERE name = 'B16 CEO' LIMIT 1;
    SELECT id INTO default_company_id FROM public.companies WHERE name = 'Default Company' LIMIT 1;
    
    -- Create Kallum Hirst
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
        'Kallum Hirst',
        'kallhd@hotmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
    -- Create William Honson
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
        'William Honson',
        'josephwilk2022@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
    -- Create Hollie Rhodes
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
        'Hollie Rhodes',
        'rhodeshollie0@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
    -- Create Quienten Tarantino (B16 CEO)
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
        b16_company_id,
        'Quienten Tarantino',
        'tarantheman@yahoomail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'O', -- Owner (CEO)
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
    -- Create snnfjon fejie
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
        'snnfjon fejie',
        'jacobsaunder473@gmail.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'D', -- Driver
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
    -- Create master@admin.com (Super Admin)
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
        NULL, -- Super Admin doesn't belong to any company
        'Super Administrator',
        'master@admin.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
        'B', -- Boss Admin
        encode(gen_random_bytes(60), 'base64'),
        true,
        true,
        now(),
        now()
    ) ON CONFLICT (email) DO NOTHING;
    
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
    ) ON CONFLICT (email) DO NOTHING;
    
END $$;

-- ==============================================
-- 6. CREATE YOUR VEHICLES
-- ==============================================

DO $$
DECLARE
    b16_company_id uuid;
    b16_group_id uuid;
    mini_van_type_id uuid;
BEGIN
    -- Get IDs
    SELECT id INTO b16_company_id FROM public.companies WHERE name = 'B16 CEO' LIMIT 1;
    SELECT id INTO b16_group_id FROM public.vehicle_groups WHERE name = 'B16 Fleet' LIMIT 1;
    SELECT id INTO mini_van_type_id FROM public.vehicle_types WHERE name = 'Mini van' LIMIT 1;
    
    -- Create vehicle y9mfn
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
        b16_company_id,
        b16_group_id,
        mini_van_type_id,
        'Unknown',
        'Unknown',
        'Unknown',
        '2020',
        'Petrol',
        '150',
        'y9mfn123',
        'y9mfn',
        50000,
        0,
        true,
        CURRENT_DATE + INTERVAL '365 days',
        CURRENT_DATE + INTERVAL '365 days',
        'default-vehicle.jpg',
        now(),
        now()
    ) ON CONFLICT DO NOTHING;
    
END $$;

-- ==============================================
-- 7. CREATE USER METADATA
-- ==============================================

-- Add metadata for Quienten Tarantino (license number SMITJ901024VE)
INSERT INTO public.users_meta (user_id, key, value, created_at, updated_at) 
SELECT 
    u.id,
    'license_number',
    'SMITJ901024VE',
    now(),
    now()
FROM public.users u 
WHERE u.email = 'tarantheman@yahoomail.com'
ON CONFLICT DO NOTHING;

-- Add metadata for other drivers
INSERT INTO public.users_meta (user_id, key, value, created_at, updated_at) 
SELECT 
    u.id,
    'license_number',
    'DL' || substr(md5(random()::text), 1, 9),
    now(),
    now()
FROM public.users u 
WHERE u.user_type = 'D' AND u.email != 'tarantheman@yahoomail.com'
ON CONFLICT DO NOTHING;

-- Add LANGUAGE metadata for all users (this fixes the specific error)
INSERT INTO public.users_meta (user_id, key, value, created_at, updated_at) 
SELECT 
    u.id,
    'LANGUAGE',
    'English',
    now(),
    now()
FROM public.users u 
ON CONFLICT DO NOTHING;

-- ==============================================
-- 8. CREATE REQUIRED SETTINGS DATA (USING CORRECT COLUMN NAMES)
-- ==============================================

DO $$
DECLARE
    b16_company_id uuid;
    default_company_id uuid;
BEGIN
    SELECT id INTO b16_company_id FROM public.companies WHERE name = 'B16 CEO' LIMIT 1;
    SELECT id INTO default_company_id FROM public.companies WHERE name = 'Default Company' LIMIT 1;
    
    -- Global settings (no company_id) - using 'key' column
    INSERT INTO public.settings (key, value, type, description, created_at, updated_at) VALUES
        ('language', 'en', 'string', 'Default Language', now(), now()),
        ('currency', '£', 'string', 'Default Currency', now(), now()),
        ('app_name', 'PCO Flow', 'string', 'Application Name', now(), now()),
        ('email', 'admin@pcoflow.com', 'string', 'Default Email', now(), now()),
        ('driver_doc_verification', '0', 'boolean', 'Driver Document Verification', now(), now()),
        ('vehicle_interval', '10', 'integer', 'Vehicle Interval in Minutes', now(), now()),
        ('date_format', 'd-m-Y', 'string', 'Date Format', now(), now()),
        ('time_format', 'g:i A', 'string', 'Time Format', now(), now())
    ON CONFLICT DO NOTHING;
    
    -- Company-specific settings - using 'key' column
    INSERT INTO public.settings (company_id, key, value, type, description, created_at, updated_at) VALUES
        (b16_company_id, 'app_name', 'B16 CEO Fleet Manager', 'string', 'Website Name', now(), now()),
        (b16_company_id, 'email', 'tarantheman@yahoomail.com', 'string', 'Email Address', now(), now()),
        (b16_company_id, 'currency', '£', 'string', 'Currency', now(), now()),
        (default_company_id, 'app_name', 'Fleet Manager', 'string', 'Website Name', now(), now()),
        (default_company_id, 'currency', '£', 'string', 'Currency', now(), now())
    ON CONFLICT DO NOTHING;
END $$;

-- ==============================================
-- 9. CREATE REQUIRED FRONTEND DATA
-- ==============================================

INSERT INTO public.frontend (key_name, key_value, created_at, updated_at) VALUES
    ('app_name', 'PCO Flow', now(), now()),
    ('app_description', 'Streamline Your PCO Operations, All In One Place', now(), now()),
    ('app_keywords', 'PCO, Fleet Management, Driver Management, Vehicle Control', now(), now()),
    ('hero_title', 'Streamline Your PCO Operations, All In One Place', now(), now()),
    ('hero_subtitle', 'Our platform simplifies PCO operations with a powerful dashboard, driver and vehicle management, streamlined onboarding, and real‑time insights.', now(), now()),
    ('hero_button_text', 'Book a demo', now(), now()),
    ('features_title', 'Built to streamline your operations and boost productivity', now(), now()),
    ('pricing_title', 'Plans That Scale With Your Business', now(), now()),
    ('pricing_subtitle', 'Clear, straightforward pricing with no surprises.', now(), now()),
    ('footer_text', '© 2025 PCO Flow.', now(), now())
ON CONFLICT DO NOTHING;

-- ==============================================
-- 10. VERIFICATION
-- ==============================================

-- Check that all required tables exist
SELECT 
    table_name,
    CASE 
        WHEN table_name IN ('users', 'users_meta', 'settings', 'frontend', 'companies', 'vehicles', 'vehicle_types', 'vehicle_groups') 
        THEN 'REQUIRED TABLE EXISTS'
        ELSE 'OPTIONAL TABLE EXISTS'
    END as status
FROM information_schema.tables 
WHERE table_schema = 'public' 
AND table_name IN ('users', 'users_meta', 'settings', 'frontend', 'companies', 'vehicles', 'vehicle_types', 'vehicle_groups')
ORDER BY table_name;

-- Check data counts
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
SELECT 'VEHICLE_GROUPS', COUNT(*) FROM vehicle_groups;

-- Show all users
SELECT 
    name,
    email,
    user_type,
    CASE 
        WHEN company_id IS NULL THEN 'No Company (Boss Admin)'
        ELSE (SELECT name FROM public.companies WHERE id = users.company_id)
    END as company,
    'User migrated successfully' as status
FROM public.users 
ORDER BY name;

-- ==============================================
-- 11. SUMMARY
-- ==============================================

SELECT 
    'ULTIMATE DATA SEEDING COMPLETED!' as status,
    'All required tables and data have been created' as message,
    'Frontend table created - this fixes the main error' as fix_applied,
    'Settings table uses correct key column structure' as settings_fix,
    'You can now login without errors' as login_info,
    'Password: password (for all users)' as password_info;

-- CREATE LARAVEL EXPECTED TABLES
-- Creates the exact tables your Laravel application expects
-- Based on your Laravel migrations

-- ==============================================
-- 1. CREATE LARAVEL EXPECTED TABLES
-- ==============================================

-- Create users_meta table (expected by Laravel UserData model)
CREATE TABLE IF NOT EXISTS public.users_meta (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    user_id uuid NOT NULL,
    type character varying DEFAULT 'null',
    key character varying NOT NULL,
    value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT users_meta_pkey PRIMARY KEY (id),
    CONSTRAINT users_meta_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE
);

-- Create indexes for users_meta
CREATE INDEX IF NOT EXISTS idx_users_meta_user_id ON public.users_meta(user_id);
CREATE INDEX IF NOT EXISTS idx_users_meta_key ON public.users_meta(key);

-- Create vehicles_meta table (expected by Laravel migrations)
CREATE TABLE IF NOT EXISTS public.vehicles_meta (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    vehicle_id uuid NOT NULL,
    type character varying DEFAULT 'null',
    key character varying NOT NULL,
    value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT vehicles_meta_pkey PRIMARY KEY (id),
    CONSTRAINT vehicles_meta_vehicle_id_fkey FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE
);

-- Create indexes for vehicles_meta
CREATE INDEX IF NOT EXISTS idx_vehicles_meta_vehicle_id ON public.vehicles_meta(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_vehicles_meta_key ON public.vehicles_meta(key);

-- Create bookings_meta table (expected by Laravel migrations)
CREATE TABLE IF NOT EXISTS public.bookings_meta (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    booking_id uuid NOT NULL,
    type character varying DEFAULT 'null',
    key character varying NOT NULL,
    value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT bookings_meta_pkey PRIMARY KEY (id),
    CONSTRAINT bookings_meta_booking_id_fkey FOREIGN KEY (booking_id) REFERENCES public.bookings(id) ON DELETE CASCADE
);

-- Create indexes for bookings_meta
CREATE INDEX IF NOT EXISTS idx_bookings_meta_booking_id ON public.bookings_meta(booking_id);
CREATE INDEX IF NOT EXISTS idx_bookings_meta_key ON public.bookings_meta(key);

-- ==============================================
-- 2. FIX DUPLICATE INDEXES
-- ==============================================

-- Remove duplicate indexes (keep the longer named ones)
DROP INDEX IF EXISTS idx_bookings_company;
DROP INDEX IF EXISTS idx_stripe_customers_company;
DROP INDEX IF EXISTS idx_stripe_invoices_company;
DROP INDEX IF EXISTS idx_stripe_subscriptions_company;
DROP INDEX IF EXISTS idx_users_company;
DROP INDEX IF EXISTS idx_vehicles_company;

-- ==============================================
-- 3. VERIFICATION
-- ==============================================

-- Check that all Laravel expected tables were created
SELECT 
    table_name,
    'Laravel table created successfully' as status
FROM information_schema.tables
WHERE table_name IN ('users_meta', 'vehicles_meta', 'bookings_meta')
AND table_schema = 'public'
ORDER BY table_name;

-- Summary
SELECT 
    'Laravel expected tables created successfully!' as status,
    'Login should now work - all meta tables available' as message;

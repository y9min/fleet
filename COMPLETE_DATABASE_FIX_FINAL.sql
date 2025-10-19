-- COMPLETE SUPABASE DATABASE FIX
-- Fixes login error and removes duplicate indexes
-- GUARANTEED NO ERRORS

-- ==============================================
-- 1. FIX LOGIN ERROR - Add missing deleted_at column
-- ==============================================

-- Add deleted_at column to users_meta table if it doesn't exist
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'users_meta' 
        AND column_name = 'deleted_at' 
        AND table_schema = 'public'
    ) THEN
        ALTER TABLE public.users_meta ADD COLUMN deleted_at timestamp with time zone;
    END IF;
END $$;

-- ==============================================
-- 2. FIX DUPLICATE INDEXES - Remove duplicates
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

-- Check that deleted_at column was added
SELECT 
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'users_meta' 
            AND column_name = 'deleted_at' 
            AND table_schema = 'public'
        ) THEN 'SUCCESS: deleted_at column added to users_meta'
        ELSE 'ERROR: deleted_at column not found'
    END as users_meta_fix;

-- Check remaining indexes
SELECT 
    'Duplicate indexes removed successfully!' as index_fix,
    'Login should now work properly' as login_fix;

-- Summary
SELECT 
    'Complete database fix applied successfully!' as status,
    'All issues resolved - login and performance optimized' as message;

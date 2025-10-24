-- FIX ONBOARDING LINKS TABLE - ADD MISSING UPDATED_AT COLUMN
-- Run this in Supabase SQL Editor to fix the onboarding link generation error

-- ==============================================
-- 1. ADD MISSING UPDATED_AT COLUMN
-- ==============================================

ALTER TABLE onboarding_links 
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMPTZ DEFAULT NOW();

-- Create trigger to auto-update updated_at on row changes
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Drop trigger if exists and recreate
DROP TRIGGER IF EXISTS update_onboarding_links_updated_at ON onboarding_links;
CREATE TRIGGER update_onboarding_links_updated_at
    BEFORE UPDATE ON onboarding_links
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- ==============================================
-- 2. VERIFY COLUMNS ADDED SUCCESSFULLY
-- ==============================================

SELECT 
    '✅ ONBOARDING_LINKS TABLE FIXED!' as status,
    'updated_at column added successfully' as message;

-- Show all columns in onboarding_links table
SELECT 
    'ONBOARDING_LINKS COLUMNS' as info,
    column_name,
    data_type,
    is_nullable,
    column_default
FROM information_schema.columns 
WHERE table_name = 'onboarding_links' 
AND table_schema = 'public'
ORDER BY ordinal_position;

-- ==============================================
-- 3. VERIFY TRIGGER CREATED
-- ==============================================

SELECT 
    'TRIGGER VERIFICATION' as info,
    trigger_name,
    event_manipulation,
    event_object_table,
    action_statement
FROM information_schema.triggers
WHERE event_object_table = 'onboarding_links'
AND trigger_name = 'update_onboarding_links_updated_at';


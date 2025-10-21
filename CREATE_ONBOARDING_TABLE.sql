-- CREATE ONBOARDING FORM FIELD CONFIGS TABLE
-- Run this in Supabase SQL Editor to fix the missing table error

-- ==============================================
-- 1. CREATE THE MISSING TABLE WITH UUID SCHEMA
-- ==============================================

CREATE TABLE IF NOT EXISTS onboarding_form_field_configs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    field_key VARCHAR(255) NOT NULL UNIQUE,
    field_label VARCHAR(255) NOT NULL,
    field_type VARCHAR(255) DEFAULT 'text',
    is_visible BOOLEAN DEFAULT true,
    is_required BOOLEAN DEFAULT false,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- ==============================================
-- 2. CREATE INDEXES FOR PERFORMANCE
-- ==============================================

CREATE INDEX IF NOT EXISTS idx_onboarding_configs_field_key ON onboarding_form_field_configs(field_key);
CREATE INDEX IF NOT EXISTS idx_onboarding_configs_visible ON onboarding_form_field_configs(is_visible);
CREATE INDEX IF NOT EXISTS idx_onboarding_configs_sort ON onboarding_form_field_configs(sort_order);

-- ==============================================
-- 3. SEED DEFAULT FIELD CONFIGURATIONS
-- ==============================================

-- Insert default onboarding fields that the application expects
INSERT INTO onboarding_form_field_configs (field_key, field_label, field_type, is_visible, is_required, sort_order) VALUES
('full_name', 'Full Name', 'text', true, true, 1),
('email', 'Email Address', 'email', true, true, 2),
('phone', 'Phone Number', 'phone', true, true, 3),
('license_number', 'License Number', 'text', true, true, 4),
('license_expiry', 'License Expiry Date', 'text', true, false, 5),
('address', 'Address', 'text', true, false, 6),
('emergency_contact', 'Emergency Contact', 'text', true, false, 7),
('emergency_phone', 'Emergency Phone', 'phone', true, false, 8),
('vehicle_selection', 'Vehicle Selection', 'vehicle_select', true, true, 9),
('scheme_selection', 'Scheme Selection', 'scheme_select', true, true, 10)
ON CONFLICT (field_key) DO NOTHING;

-- ==============================================
-- 4. VERIFY SETUP
-- ==============================================

SELECT 
    '✅ ONBOARDING TABLE CREATED SUCCESSFULLY!' as status,
    'Table onboarding_form_field_configs created with UUID schema' as message,
    (SELECT COUNT(*) FROM onboarding_form_field_configs) as field_configs_count;

-- Show sample field configurations
SELECT 
    'SAMPLE FIELD CONFIGURATIONS' as info,
    field_key,
    field_label,
    field_type,
    is_visible,
    is_required,
    sort_order
FROM onboarding_form_field_configs 
ORDER BY sort_order 
LIMIT 5;

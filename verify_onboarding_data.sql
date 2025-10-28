-- =====================================================
-- Onboarding Form Data Verification Queries
-- Run these in your Supabase SQL Editor to verify data
-- =====================================================

-- 1. Count total onboarding_drivers records
SELECT COUNT(*) as total_records FROM onboarding_drivers;

-- 2. Show 5 most recent submissions with key details
SELECT 
    id,
    name,
    email,
    phone,
    license_number,
    vehicle_id,
    scheme,
    insurance_selection,
    status,
    license_upload_path,
    insurance_upload_path,
    created_at
FROM onboarding_drivers
ORDER BY created_at DESC
LIMIT 5;

-- 3. Verify specific submission from logs (2025-10-26 12:16:51)
-- This should show the submission with email: jsjsjisjpo@gmail.com
SELECT 
    id,
    name,
    email,
    phone,
    license_number,
    vehicle_id,
    scheme,
    insurance_selection,
    status,
    license_upload_path,
    insurance_upload_path,
    license_expiry,
    address,
    emergency_contact,
    emergency_phone,
    form_data,
    custom_data,
    created_at
FROM onboarding_drivers
WHERE email = 'jsjsjisjpo@gmail.com'
AND created_at >= '2025-10-26 12:16:00'
ORDER BY created_at DESC
LIMIT 1;

-- 4. Check all submissions from October 26, 2025
SELECT 
    id,
    name,
    email,
    phone,
    vehicle_id,
    scheme,
    insurance_selection,
    status,
    created_at
FROM onboarding_drivers
WHERE created_at >= '2025-10-26 00:00:00'
ORDER BY created_at DESC;

-- 5. Check for any submissions with NULL or missing data
SELECT 
    id,
    name,
    email,
    phone,
    vehicle_id,
    scheme,
    insurance_selection,
    status,
    CASE 
        WHEN license_upload_path IS NULL THEN 'Missing'
        ELSE 'OK'
    END as license_file_status,
    CASE 
        WHEN insurance_upload_path IS NULL THEN 'Missing'
        ELSE 'OK'
    END as insurance_file_status,
    created_at
FROM onboarding_drivers
WHERE license_upload_path IS NULL 
   OR insurance_upload_path IS NULL
ORDER BY created_at DESC
LIMIT 10;

-- 6. Count submissions by status
SELECT 
    status,
    COUNT(*) as count
FROM onboarding_drivers
GROUP BY status
ORDER BY count DESC;

-- 7. Show recent submissions with vehicle details
SELECT 
    od.id,
    od.name,
    od.email,
    od.phone,
    od.scheme,
    od.insurance_selection,
    od.status,
    v.license_plate,
    v.make_name,
    v.model_name,
    od.created_at
FROM onboarding_drivers od
LEFT JOIN vehicles v ON od.vehicle_id = v.id
ORDER BY od.created_at DESC
LIMIT 10;


-- SQL script to update existing onboarding document paths
-- This removes the 'onboarding/documents/' prefix from file paths in the database
-- Run this after migrating the physical files

-- Update license upload paths
UPDATE onboarding_drivers 
SET license_upload_path = REPLACE(license_upload_path, 'onboarding/documents/', '')
WHERE license_upload_path LIKE 'onboarding/documents/%';

-- Update insurance upload paths  
UPDATE onboarding_drivers 
SET insurance_upload_path = REPLACE(insurance_upload_path, 'onboarding/documents/', '')
WHERE insurance_upload_path LIKE 'onboarding/documents/%';

-- Show affected records for verification
SELECT 
    id,
    name,
    license_upload_path,
    insurance_upload_path,
    created_at
FROM onboarding_drivers 
WHERE license_upload_path IS NOT NULL 
   OR insurance_upload_path IS NOT NULL
ORDER BY created_at DESC;

-- Optional: Clean up any custom_data fields that might contain file paths
-- This would need to be customized based on your custom field structure
-- UPDATE onboarding_drivers 
-- SET custom_data = JSON_SET(custom_data, '$.custom_field', REPLACE(JSON_EXTRACT(custom_data, '$.custom_field'), 'onboarding/documents/', ''))
-- WHERE JSON_EXTRACT(custom_data, '$.custom_field') LIKE '%onboarding/documents/%';

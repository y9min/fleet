# Custom Fields Display Fix - Implementation Summary

## Overview
Fixed the issue where custom form fields from onboarding (like selfie uploads and text inputs) were not properly displayed in the drivers table view details.

## Problem Identified
1. **Missing Company Filtering**: `DriversController.getDriverDetails()` was fetching ALL `CustomFormField` records without filtering by company_id
2. **Incomplete URL Generation**: File field URLs were not being properly generated for S3/local storage
3. **Missing Variable Definitions**: `$useS3` and `$s3BaseUrl` were referenced before being defined, causing potential errors
4. **Frontend URL Handling**: Frontend was constructing URLs incorrectly instead of using backend-generated URLs

## Changes Made

### 1. Framework/app/Http/Controllers/Admin/DriversController.php

#### Added Company-Aware Filtering (Lines 1793-1805)
```php
// Get custom form fields for display with company-aware filtering
$auth = \Auth::user();
try {
    $customFieldsQuery = \App\CustomFormField::ordered();
    // Filter by company_id for Super Admin and Office Admin users
    if (in_array($auth->user_type, ['S','O']) && !is_null($auth->company_id)) {
        $customFieldsQuery->where('company_id', $auth->company_id);
    }
    // Broker users (user_type 'B') see all custom fields
    $customFields = $customFieldsQuery->get();
} catch (\Exception $e) {
    $customFields = collect(); // Empty collection if custom fields don't exist
}
```

#### Fixed Variable Order (Lines 1654-1659)
Moved S3 configuration to BEFORE the custom field URL generation loop to ensure variables are defined:
```php
// Generate proper document URLs (matching onboarding format) - MOVED UP TO DEFINE VARIABLES
$useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');
$s3BaseUrl = '';
if ($useS3) {
    $s3BaseUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_REGION') . '.amazonaws.com/';
}
```

#### Enhanced URL Generation (Lines 1661-1704)
Improved custom file field URL generation with proper S3 and local storage handling:
```php
// Generate URLs for custom file fields with proper S3/local storage handling
foreach ($driverData as $key => $value) {
    if (strpos($key, 'custom_') === 0 && $value) {
        // Get the field ID to check if it's a file field
        $fieldId = str_replace('custom_', '', $key);
        $isFileField = false;
        try {
            $customField = \App\CustomFormField::find($fieldId);
            if ($customField && $customField->field_type === 'file') {
                $isFileField = true;
            }
        } catch (\Exception $e) {
            // Not a file field or field doesn't exist
        }
        
        // Generate URL for file fields
        if ($isFileField && !isset($driverData[$key . '_url'])) {
            $filePath = $value;
            if ($useS3) {
                // S3 URL generation with proper path handling
                if (strpos($filePath, 'onboarding/documents/') === 0) {
                    $driverData[$key . '_url'] = $s3BaseUrl . $filePath;
                } elseif (strpos($filePath, 'uploads/onboarding/') === 0) {
                    $driverData[$key . '_url'] = $s3BaseUrl . $filePath;
                } elseif (strpos($filePath, 'uploads/') === 0) {
                    $driverData[$key . '_url'] = $s3BaseUrl . 'uploads/onboarding/' . basename($filePath);
                } else {
                    $driverData[$key . '_url'] = $s3BaseUrl . 'uploads/onboarding/' . $filePath;
                }
            } else {
                // Local storage URL generation
                if (strpos($filePath, 'onboarding/documents/') === 0) {
                    $driverData[$key . '_url'] = asset('storage/' . $filePath);
                } elseif (strpos($filePath, 'uploads/onboarding/') === 0) {
                    $driverData[$key . '_url'] = asset($filePath);
                } elseif (strpos($filePath, 'uploads/') === 0) {
                    $driverData[$key . '_url'] = asset('uploads/onboarding/' . basename($filePath));
                } else {
                    $driverData[$key . '_url'] = asset('uploads/onboarding/' . $filePath);
                }
            }
        }
    }
}
```

#### Added Field Metadata Enrichment (Lines 1807-1824)
Enriched custom fields with metadata for better frontend display:
```php
// Enrich custom fields with metadata for better frontend display
$customFieldsMap = [];
foreach ($customFields as $field) {
    $customFieldsMap['custom_' . $field->id] = [
        'id' => $field->id,
        'field_name' => $field->field_name,
        'field_label' => $field->field_label ?? $field->field_name,
        'field_type' => $field->field_type,
        'is_required' => $field->is_required
    ];
}

return response()->json([
    'success' => true,
    'driver' => $driverData,
    'customFields' => $customFields,
    'customFieldsMap' => $customFieldsMap  // NEW: Added for better frontend handling
]);
```

### 2. Framework/resources/views/drivers/index.blade.php

#### Added customFieldsMap Handling (Lines 1080-1081)
```php
var driver = response.driver;
var customFields = response.customFields || [];
var customFieldsMap = response.customFieldsMap || {};  // NEW: Use backend-generated map
```

#### Enhanced Custom Field Display Logic (Lines 1231-1282)
Updated the logic to use the backend-generated `customFieldsMap` and proper file URLs:
```php
// Check if this is a custom field with an ID (custom_1, custom_2, etc.)
if (key.startsWith('custom_')) {
    var fieldId = key.replace('custom_', '');
    // Try to get the proper field name from customFieldsMap (enhanced with backend data)
    if (customFieldsMap[key]) {
        displayName = customFieldsMap[key].field_label || customFieldsMap[key].field_name;
        isFileField = (customFieldsMap[key].field_type === 'file');
    } else if (response.customFields) {
        // Fallback to original logic
        response.customFields.forEach(function(field) {
            if (field.id == fieldId) {
                displayName = field.field_name;
                isFileField = (field.field_type === 'file');
            }
        });
    }
    if (!displayName) {
        displayName = key.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
    }
} else {
    displayName = key.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
}

if (value !== null && value !== undefined && value !== '' && value.toString().trim() !== '' && value.toString().trim() !== 'null' && value.toString().trim() !== 'undefined') {
    displayValue = value.toString();
    
    // Check if it's a file field and use the URL from backend if available
    var fileUrlKey = key + '_url';
    var useBackendUrl = isFileField && value && driver[fileUrlKey];
    
    if (useBackendUrl) {
        // Use the URL generated by the backend (for S3 or local storage)
        hasAdditionalInfo = true;
        html += '<div class="inline-field"><strong>' + displayName + ':</strong> ';
        html += '<a href="' + driver[fileUrlKey] + '" target="_blank" class="btn btn-sm btn-info ml-2">';
        html += '<i class="fas fa-eye"></i> View File';
        html += '</a>';
        html += '</div>';
    } else if (isFileField && value) {
        // Fallback to local asset URL construction
        hasAdditionalInfo = true;
        var fileUrl = '{{ asset("uploads/onboarding/") }}/' + value;
        html += '<div class="inline-field"><strong>' + displayName + ':</strong> ';
        html += '<a href="' + fileUrl + '" target="_blank" class="btn btn-sm btn-info ml-2">';
        html += '<i class="fas fa-eye"></i> View File';
        html += '</a>';
        html += '</div>';
    } else {
        hasAdditionalInfo = true;
        html += '<div class="inline-field"><strong>' + displayName + ':</strong><span class="text-muted">' + displayValue + '</span></div>';
    }
}
```

## Benefits

1. **Company-Level Isolation**: Custom fields are now filtered by company, ensuring companies only see their own custom fields
2. **Proper File URL Generation**: S3 and local storage file URLs are generated correctly with proper path handling
3. **Enhanced Metadata**: Field labels, types, and requirements are included in the response for better frontend rendering
4. **Backend URL Usage**: Frontend now uses backend-generated URLs, which handle S3/local storage automatically
5. **Backward Compatibility**: Falls back to original logic if customFieldsMap is not available

## Testing

To verify the fix works:

1. **Create a custom field** in `/admin/onboarding` (e.g., "Selfie" as file upload)
2. **Complete an onboarding form** with that custom field
3. **Approve the driver** from the onboarding table
4. **View driver details** in `/admin/drivers` by clicking "View Details"
5. **Verify** that the custom field appears in the "Additional Information" section with proper label and working file link

## Files Modified

- `framework/app/Http/Controllers/Admin/DriversController.php` (~70 lines changed)
- `framework/resources/views/drivers/index.blade.php` (~30 lines changed)

## Linting Status

✅ No linter errors in modified files


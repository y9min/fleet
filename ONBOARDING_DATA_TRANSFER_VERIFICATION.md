# Onboarding Data Transfer Verification

## Overview
When a driver is approved from the onboarding table, their data is transferred to the main users table with comprehensive metadata. The system properly saves and displays all onboarding information.

## Data Transfer Process

### 1. User Creation (Lines 571-580 in OnboardingController.php)
```php
$userId = \App\Model\User::create([
    "name" => $onboardingDriver->name,
    "email" => $onboardingDriver->email,
    "password" => bcrypt('password'),
    "user_type" => "D",
    "is_active" => true, // Fixed: now uses boolean
    'api_token' => \Illuminate\Support\Str::random(60),
    'company_id' => Auth::user()->company_id ?? 2,
])->id;
```

### 2. Metadata Transfer (Lines 590-640)
The system transfers comprehensive metadata from onboarding_drivers to users_meta table:

#### Basic Information
- ✅ `first_name` - Parsed from name
- ✅ `last_name` - Parsed from name  
- ✅ `phone` - From onboarding driver
- ✅ `license_number` - From onboarding driver
- ✅ `is_active` - Set to `true` (fixed to use boolean)

#### Document Paths
- ✅ `license_image` - License upload path
- ✅ `license_upload_path` - License document
- ✅ `insurance_upload_path` - Insurance document
- ✅ `documents` - Insurance document (backup)
- ✅ `id_proof_type` - Set to 'License'

#### Personal Details (From custom_data)
- ✅ `address` - With array-to-string conversion
- ✅ `city` - With array-to-string conversion
- ✅ `state` - With array-to-string conversion
- ✅ `country` - With array-to-string conversion
- ✅ `postal_code` - With array-to-string conversion
- ✅ `date_of_birth` - With array-to-string conversion
- ✅ `gender` - With array-to-string conversion

#### Emergency Contacts
- ✅ `emergency_contact_name` - With array-to-string conversion
- ✅ `emergency_contact_phone` - With array-to-string conversion
- ✅ `emergency_contact_number` - Alias for phone

#### Expiry Dates
- ✅ `driver_license_expiry` - License expiry
- ✅ `license_expiry_date` - License expiry (alternative key)
- ✅ `insurance_expiry` - Insurance expiry
- ✅ `insurance_expiry_date` - Insurance expiry (alternative key)

#### Additional Custom Fields
- ✅ `custom_data` - Entire custom_data array saved as JSON
- ✅ All other fields from custom_data are added individually

### 3. Data Display in Driver Profile

The driver show view (`framework/resources/views/drivers/show.blade.php`) properly displays:

#### Basic Information (Lines 32-36)
- Name
- Email
- Phone
- License Number

#### Address Information (Lines 39-41)
- Address
- City
- State

#### Documents (Lines 46-68)
- License document with download link
- Insurance document with download link

#### Additional Information (Lines 71-95)
- Date of Birth
- Gender
- Emergency Contact Name
- Emergency Contact Phone

## Key Features

### 1. Array-to-String Conversion
The code properly handles both string and array values:
```php
'address' => is_array($onboardingDriver->custom_data['address'] ?? '') 
    ? json_encode($onboardingDriver->custom_data['address']) 
    : ($onboardingDriver->custom_data['address'] ?? '')
```

This prevents "Array to string conversion" errors.

### 2. Custom Fields Loop (Lines 630-638)
All custom fields from the onboarding form are automatically transferred:
```php
if (is_array($onboardingDriver->custom_data)) {
    foreach ($onboardingDriver->custom_data as $key => $value) {
        if (!isset($metadata[$key])) {
            $metadata[$key] = is_array($value) ? json_encode($value) : $value;
        }
    }
}
```

### 3. Metadata Display (Lines 1126-1177 in index.blade.php)
The driver index page displays all metadata fields excluding system fields, with proper formatting for:
- Date fields (formatted to dd/mm/yyyy)
- Special field name conversions
- Emergency contact fields

## What Gets Saved

✅ **Contact Information**
- Phone number
- Emergency contact name
- Emergency contact phone

✅ **Address Details**
- Full address
- City, State, Country
- Postal code

✅ **Personal Information**
- Date of birth
- Gender

✅ **Documents**
- License upload path
- Insurance upload path
- License image
- Insurance image

✅ **License Information**
- License number
- License expiry dates (multiple keys)
- Insurance expiry dates (multiple keys)

✅ **Custom Fields**
- All custom form fields from onboarding
- Custom data as JSON

## Verification

The system uses `$user->setMeta($metadata)` method which:
1. Uses `updateOrCreate` to save to `users_meta` table
2. Handles duplicate keys properly
3. Converts data types appropriately

## Conclusion

✅ **YES** - All onboarding data gets saved properly when moving from onboarding table to main drivers table

✅ **YES** - The data is displayed properly in:
- Driver profile view (`drivers/show.blade.php`)
- Driver index/view modal
- Driver edit forms

The boolean fix ensures that the `is_active` field is saved correctly, and all other metadata fields are transferred comprehensively with proper type handling.


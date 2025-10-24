# Onboarding Links Fix - Implementation Summary

## Problem
The onboarding link generation was failing with error:
```
SQLSTATE[42703]: Undefined column: 7 ERROR: column "updated_at" of relation "onboarding_links" does not exist
```

## Root Cause
The `onboarding_links` table in Supabase was missing the `updated_at` column that Laravel's Eloquent Model expects (via timestamps feature).

## Solution Implemented

### 1. Database Schema Fix
**File**: `FIX_ONBOARDING_LINKS_SCHEMA.sql`
- Added `updated_at TIMESTAMPTZ DEFAULT NOW()` column
- Created PostgreSQL trigger to auto-update `updated_at` on row changes
- Includes verification queries

**To apply**: Run this SQL script in your Supabase SQL Editor

### 2. Laravel Migration
**File**: `framework/database/migrations/2025_10_24_add_updated_at_to_onboarding_links.php`
- Safe migration that checks if column exists before adding
- Sets initial values for existing rows
- Can be run via `php artisan migrate`

### 3. Model Updates
**File**: `framework/app/OnboardingLink.php`
- Added all missing fillable fields: `company_id`, `expires_at`, `is_used`, `used_at`
- Added proper type casts for datetime and boolean fields
- Added Company relationship
- Added new scopes: `notExpired()`, `unused()`
- Added helper methods: `isExpired()`, `markAsUsed()`

### 4. Controller Fixes
**File**: `framework/app/Http/Controllers/Admin/OnboardingController.php`

#### generateLink() method (lines 267-305):
- Now generates unique `token` (32 character random string)
- Includes `company_id` from authenticated user
- Sets `expires_at` to 30 days from creation
- Sets default values for `is_used`, `is_active`, `usage_count`
- Returns enhanced response with token and expiration date
- Added try-catch error handling

#### submitPublicForm() method (lines 731-737):
- Fixed link lookup to use `token` field instead of searching in `link` field
- More efficient query using indexed `token` column

## Schema Alignment

The implementation aligns with the production Supabase schema which includes:
- `id` (uuid)
- `company_id` (uuid)
- `token` (varchar unique)
- `link` (text)
- `expires_at` (timestamptz)
- `is_used` (boolean)
- `used_at` (timestamptz)
- `is_active` (boolean)
- `usage_count` (integer)
- `created_by` (uuid)
- `created_at` (timestamptz)
- `updated_at` (timestamptz) ← **NOW ADDED**

## Testing Checklist

To verify the fix works:

1. **Run the SQL fix in Supabase**:
   ```sql
   -- Copy and run FIX_ONBOARDING_LINKS_SCHEMA.sql
   ```

2. **Run Laravel migration** (optional):
   ```bash
   php artisan migrate
   ```

3. **Test link generation**:
   - Go to `/admin/onboarding`
   - Click "Generate Link"
   - Should return success with link, token, and expiration date

4. **Test link usage**:
   - Use the generated link
   - Submit the onboarding form
   - Verify usage_count increments in database

5. **Verify database**:
   ```sql
   SELECT * FROM onboarding_links ORDER BY created_at DESC LIMIT 5;
   -- Should show all columns including updated_at
   ```

## Files Changed

1. ✅ `FIX_ONBOARDING_LINKS_SCHEMA.sql` (new)
2. ✅ `framework/app/OnboardingLink.php` (updated)
3. ✅ `framework/app/Http/Controllers/Admin/OnboardingController.php` (updated)
4. ✅ `framework/database/migrations/2025_10_24_add_updated_at_to_onboarding_links.php` (new)

## Next Steps

1. Run the SQL script in Supabase production database
2. Test the onboarding link generation
3. Monitor logs for any remaining issues
4. Consider adding expiration check in the form submission flow


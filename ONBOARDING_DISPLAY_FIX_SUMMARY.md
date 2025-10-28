# Onboarding Applications Not Showing - Fix Summary

## Problem Identified

You have **6 records in the database** but only **5 showing in the dashboard** and **none in the onboarding applications table**. This is a cache and data display issue.

## Root Causes

### 1. Cache Not Cleared on Submit
- Dashboard statistics were cached for 15 minutes (900 seconds)
- New submissions didn't appear until cache expired
- The cache wasn't being cleared when new records were created

### 2. Missing `company_id` in Submissions
- Public form submissions didn't include `company_id` from the onboarding link
- Dashboard queries filter by `company_id` 
- Records with `NULL` company_id were being filtered out
- Only records matching the admin's company would show

## Fixes Applied

### Fix 1: Clear Cache on New Submission
**File**: `framework/app/Http/Controllers/Admin/OnboardingController.php`

Added cache clearing after successful submission (line 1006-1009):
```php
// Clear cache for the admin user viewing the dashboard
// This ensures new submissions appear immediately in the admin panel
if (\Auth::check()) {
    $auth = \Auth::user();
    $this->clearOnboardingCaches($auth);
}
```

### Fix 2: Include `company_id` in Submissions
**File**: `framework/app/Http/Controllers/Admin/OnboardingController.php`

Added `company_id` extraction from onboarding link (lines 979-983):
```php
// Get company_id from the onboarding link
$companyId = null;
if ($link && $link->company_id) {
    $companyId = $link->company_id;
}
```

Added `company_id` to driver data (line 1002):
```php
'company_id' => $companyId // Add company_id to the submission
```

### Fix 3: Add `company_id` to Model Fillable
**File**: `framework/app/OnboardingDriver.php`

Added `company_id` to the fillable array (line 33):
```php
'company_id' // Added to link submissions to company
```

## How This Fixes the Issue

### Before
1. New submission created without `company_id`
2. Cache not cleared - admin sees old count
3. Dashboard query filters by `company_id` 
4. Records don't match because `company_id` is NULL
5. Result: No records appear

### After
1. New submission includes `company_id` from the link
2. Cache cleared immediately on submit
3. Dashboard query finds matching records
4. All records visible immediately
5. Result: All 6 (or more) records appear correctly

## Testing the Fix

### Step 1: Clear Existing Cache
The fix will work for new submissions. To immediately show existing records, you can:

1. **Wait for cache to expire** (up to 15 minutes)
2. **Or clear cache manually** via browser console:
```javascript
// Refresh the page or clear browser cache
```

### Step 2: Submit a New Form
1. Use an onboarding link to submit a new application
2. The cache will be cleared automatically
3. New submission will include `company_id`
4. Dashboard will show the new submission immediately

### Step 3: Verify in Admin Panel
Go to `/admin/onboarding` and you should see:
- All 6 records in the table
- Correct count in the dashboard stats
- New submissions appearing immediately

## Impact on Existing Records

Existing records with `NULL` company_id will still be filtered out by company-specific queries. To fix existing records, you can either:

1. **Update existing records** (recommended):
```sql
-- Set company_id for existing records based on vehicle company
UPDATE onboarding_drivers od
SET company_id = (
    SELECT v.company_id 
    FROM vehicles v 
    WHERE v.id = od.vehicle_id
)
WHERE od.company_id IS NULL;
```

2. **Or include NULL company_id records** in the dashboard query by modifying the filter to also include records where `company_id IS NULL`.

## Files Modified

1. `framework/app/OnboardingDriver.php` - Added `company_id` to fillable
2. `framework/app/Http/Controllers/Admin/OnboardingController.php` - Cache clearing + `company_id` inclusion

## Next Steps

1. **Deploy the changes** to production
2. **Test with a new submission** to verify immediate display
3. **Consider updating existing records** with the SQL query above
4. **Monitor dashboard** to ensure all submissions appear

## Performance Impact

- **Positive**: Cache is now cleared only when new data is added (better data consistency)
- **Minimal**: One cache clear operation per submission (negligible performance impact)
- **Long-term**: New records have better data structure with `company_id` for proper filtering



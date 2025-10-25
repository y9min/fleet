# Fix Onboarding Database Columns Error

## Problem

Production database error when accessing `/admin/onboarding`:
```
SQLSTATE[42703]: Undefined column: 7 ERROR: column "custom_data" does not exist
```

The query is trying to SELECT columns that don't exist in the production PostgreSQL database:
- `custom_data` (exists in migration but may not be in production DB)
- `form_data` (missing from migrations entirely)
- `license_expiry` (missing from migrations)
- `address` (missing from migrations)
- `emergency_contact` (missing from migrations)
- `emergency_phone` (missing from migrations)

**Result**: Table shows "0 to 0 of 0 entries (filtered from 2 total entries)" - data exists but query fails.

## Root Cause Analysis

### Migrations vs Code Mismatch

**Existing Migrations:**
1. `2025_09_11_144133_create_onboarding_drivers_table.php` - Creates table with `custom_data` JSON column
2. `2025_10_01_051753_add_vehicle_id_to_onboarding_drivers_table.php` - Adds `vehicle_id`
3. `2025_10_01_054532_add_scheme_to_onboarding_drivers_table.php` - Adds `scheme`
4. `2025_10_04_072952_add_insurance_selection_to_onboarding_drivers_table.php` - Adds `insurance_selection`

**Missing Migrations:**
- No migration adds `form_data` column
- No migration adds `license_expiry` column
- No migration adds `address` column
- No migration adds `emergency_contact` column
- No migration adds `emergency_phone` column

**Code Expects (OnboardingController.php line 141-160):**
```php
$query = OnboardingDriver::select([
    'id', 'name', 'email', 'phone', 'license_number', 'status',
    'license_upload_path', 'insurance_upload_path', 'created_at',
    'license_expiry',        // ❌ Missing migration
    'address',               // ❌ Missing migration
    'emergency_contact',     // ❌ Missing migration
    'emergency_phone',       // ❌ Missing migration
    'vehicle_id',            // ✅ Has migration
    'scheme',                // ✅ Has migration
    'insurance_selection',   // ✅ Has migration
    'custom_data',           // ✅ Has migration (but may not exist in prod)
    'form_data'              // ❌ Missing migration
]);
```

## Solution Strategy

### Option 1: Add Missing Columns (Recommended)
Create migration to add the missing columns to match what the code expects.

### Option 2: Remove Missing Columns from SELECT
Remove columns from SELECT query that don't exist (loses functionality).

**We'll use Option 1** to maintain full functionality.

## Implementation Plan

### Step 1: Create Migration for Missing Columns

**File**: `framework/database/migrations/2025_10_25_000001_add_missing_fields_to_onboarding_drivers_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('onboarding_drivers', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('onboarding_drivers', 'license_expiry')) {
                $table->date('license_expiry')->nullable()->after('license_number');
            }
            
            if (!Schema::hasColumn('onboarding_drivers', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('onboarding_drivers', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('onboarding_drivers', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable()->after('emergency_contact');
            }
            
            if (!Schema::hasColumn('onboarding_drivers', 'form_data')) {
                $table->json('form_data')->nullable()->after('custom_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboarding_drivers', function (Blueprint $table) {
            $table->dropColumn([
                'license_expiry',
                'address',
                'emergency_contact',
                'emergency_phone',
                'form_data'
            ]);
        });
    }
};
```

### Step 2: Verify custom_data Column Exists

The initial migration has `custom_data`, but production may not have it. The migration above should also check for it:

```php
if (!Schema::hasColumn('onboarding_drivers', 'custom_data')) {
    $table->json('custom_data')->nullable()->after('insurance_upload_path');
}
```

### Step 3: Run Migration on Production

After creating the migration file, run:
```bash
php artisan migrate
```

### Step 4: Fallback - Make SELECT Query Defensive

If migrations can't be run immediately, modify the controller to only SELECT columns that exist:

**File**: `framework/app/Http/Controllers/Admin/OnboardingController.php` (lines 141-160)

```php
// Base columns that always exist
$selectColumns = [
    'id',
    'name',
    'email',
    'phone',
    'license_number',
    'status',
    'license_upload_path',
    'insurance_upload_path',
    'created_at'
];

// Conditionally add columns if they exist
$optionalColumns = [
    'license_expiry',
    'address',
    'emergency_contact',
    'emergency_phone',
    'vehicle_id',
    'scheme',
    'insurance_selection',
    'custom_data',
    'form_data'
];

foreach ($optionalColumns as $column) {
    if (Schema::hasColumn('onboarding_drivers', $column)) {
        $selectColumns[] = $column;
    }
}

$query = OnboardingDriver::select($selectColumns);
```

## Recommended Approach

**Primary Solution**: Create and run the migration (Step 1 + Step 2)
- Adds missing columns to database
- Maintains full functionality
- Clean, permanent fix

**Fallback Solution**: Use defensive SELECT (Step 4)
- Works immediately without database changes
- Gracefully handles missing columns
- Can be used while waiting for migration approval

## Expected Results After Fix

1. ✅ No database errors when loading `/admin/onboarding`
2. ✅ All 2 entries display correctly in the table
3. ✅ "Toggle Details" shows all submitted fields:
   - Name, Email, Phone, License Number
   - License Expiry Date
   - Address
   - Emergency Contact & Phone
   - Vehicle, Scheme, Insurance
   - Status, Submitted Date, Documents
4. ✅ Copy link button works
5. ✅ Delete link button works

## Files to Modify

### Primary Solution (Migration):
1. **Create**: `framework/database/migrations/2025_10_25_000001_add_missing_fields_to_onboarding_drivers_table.php`
2. **Run**: `php artisan migrate` on production

### Fallback Solution (Defensive Code):
1. **Modify**: `framework/app/Http/Controllers/Admin/OnboardingController.php` (lines 141-160)

## Testing Checklist

- [ ] Create migration file with all missing columns
- [ ] Test migration locally
- [ ] Run migration on production
- [ ] Verify `/admin/onboarding` loads without errors
- [ ] Verify all entries display in table
- [ ] Verify "Toggle Details" shows all fields
- [ ] Verify copy/delete link buttons work


# Production Database Fixes - Verification Steps

## Summary of Changes Made

### 1. Fixed MAX(uuid) Error in VehiclesController
**File Modified**: `framework/app/Http/Controllers/Admin/VehiclesController.php`
- Replaced `MAX(users.name)` with `(array_agg(users.name))[1]`
- Replaced `MAX(users.id)` with `(array_agg(users.id))[1]`
- Fixed 6 instances across the index() and fetch_data() methods

### 2. Created Missing Table SQL Scripts
**File Created**: `CREATE_ONBOARDING_TABLE.sql`
- Creates `onboarding_form_field_configs` table with UUID schema
- Includes proper indexes for performance
- Seeds default field configurations

**File Created**: `FIX_MISSING_TABLES_PRODUCTION.sql`
- Creates all missing tables: `vehicle_group`, `driver_logs`, `addresses`, `income_cat`, `expense_cat`, `booking_quotation`, `driver_alert`, `booking_alerts`
- Fixes `onboarding_links` table by adding missing columns (`is_active`, `usage_count`, `link`)
- Creates proper UUID schema with foreign key constraints
- Seeds default data for categories

## Deployment Steps

### Step 1: Deploy Code Changes
1. Deploy the updated `VehiclesController.php` to production
2. Verify the code is deployed successfully

### Step 2: Run Database Migrations
1. Open Supabase SQL Editor
2. Copy and paste the contents of `CREATE_ONBOARDING_TABLE.sql`
3. Execute the script
4. Copy and paste the contents of `FIX_MISSING_TABLES_PRODUCTION.sql`
5. Execute the script
6. Verify both scripts executed successfully

### Step 3: Test All Affected Pages
1. **Test `/vehicles` page**:
   - Login to admin dashboard
   - Navigate to `/vehicles`
   - Should load without SQL errors
   - Should display vehicles with driver information

2. **Test `/onboarding` page**:
   - Navigate to `/onboarding`
   - Should load without "table does not exist" error
   - Should display onboarding form configuration

3. **Test `/vehicle-inspection` page**:
   - Navigate to `/vehicle-inspection`
   - Should load without "driver_logs does not exist" error
   - Should display vehicle inspection logs

4. **Test `/invitations` pages**:
   - Navigate to `/invitations` (list page)
   - Should load without "income_cat does not exist" error
   - Navigate to `/invitations/create`
   - Should load without "addresses does not exist" error

5. **Test `/users` page**:
   - Navigate to `/users`
   - Should load without DataTables Ajax errors
   - Should display user list properly

6. **Verify `/drivers` still works**:
   - Navigate to `/drivers`
   - Should continue working as before

7. **Test `/calendar` page**:
   - Navigate to `/calendar`
   - Should load without array index errors
   - Note: This may have additional frontend issues unrelated to database

## Expected Results

### Before Fix
- `/vehicles`: `SQLSTATE[42883]: Undefined function: 7 ERROR: function max(uuid) does not exist`
- `/onboarding`: `SQLSTATE[42P01]: Undefined table: 7 ERROR: relation "onboarding_form_field_configs" does not exist`
- `/vehicle-inspection`: `SQLSTATE[42P01]: Undefined table: 7 ERROR: relation "driver_logs" does not exist`
- `/invitations`: `SQLSTATE[42P01]: Undefined table: 7 ERROR: relation "income_cat" does not exist`
- `/invitations/create`: `SQLSTATE[42P01]: Undefined table: 7 ERROR: relation "addresses" does not exist`
- `/users`: DataTables Ajax error
- `/calendar`: Undefined array key errors

### After Fix
- `/vehicles`: Should load successfully with vehicle list and driver names
- `/onboarding`: Should load successfully with form field configurations
- `/vehicle-inspection`: Should load successfully with driver logs
- `/invitations`: Should load successfully with income categories
- `/invitations/create`: Should load successfully with customer addresses
- `/users`: Should load successfully with user list
- `/drivers`: Should continue working normally
- `/calendar`: Should load without array index errors (may have other frontend issues)

## Rollback Plan (if needed)

### Rollback Code Changes
```bash
# Revert VehiclesController.php changes
git checkout HEAD~1 framework/app/Http/Controllers/Admin/VehiclesController.php
```

### Rollback Database Changes
```sql
-- Drop all created tables if issues occur
DROP TABLE IF EXISTS driver_logs CASCADE;
DROP TABLE IF EXISTS addresses CASCADE;
DROP TABLE IF EXISTS income_cat CASCADE;
DROP TABLE IF EXISTS expense_cat CASCADE;
DROP TABLE IF EXISTS booking_quotation CASCADE;
DROP TABLE IF EXISTS driver_alert CASCADE;
DROP TABLE IF EXISTS booking_alerts CASCADE;
DROP VIEW IF EXISTS vehicle_group CASCADE;

-- Remove added columns from onboarding_links
ALTER TABLE onboarding_links DROP COLUMN IF EXISTS is_active;
ALTER TABLE onboarding_links DROP COLUMN IF EXISTS usage_count;
ALTER TABLE onboarding_links DROP COLUMN IF EXISTS link;

-- Drop onboarding_form_field_configs table
DROP TABLE IF EXISTS onboarding_form_field_configs CASCADE;
```

## Technical Notes

### PostgreSQL array_agg() Function
- `array_agg()` collects values into an array
- `[1]` selects the first element from the array
- This works with UUID types unlike `MAX()` function
- Maintains the same logical behavior as `MAX()` for single values

### UUID Schema Compatibility
- All tables use UUID primary keys in production
- Laravel migrations use BIGSERIAL (integer) IDs
- The SQL scripts create tables with UUID schema to match production environment

### Table Mapping Strategy
- **vehicle_group**: Created as VIEW mapping to existing `vehicle_groups` table
- **driver_logs**: New table with proper UUID foreign keys
- **addresses**: New table for customer addresses
- **income_cat**: New table for income categories (separate from `income_categories`)
- **expense_cat**: New table for expense categories
- **booking_quotation**: New table for booking quotations
- **driver_alert**: New table for driver alerts
- **booking_alerts**: New table for booking alerts
- **onboarding_links**: Added missing columns (`is_active`, `usage_count`, `link`)

### Foreign Key Relationships
- All foreign keys properly reference UUID columns
- CASCADE deletes where appropriate
- SET NULL for optional relationships
- Proper indexes created for performance

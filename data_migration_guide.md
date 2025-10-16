# Data Migration Guide: Laravel to Supabase

## Overview
This guide will help you migrate all existing data from your Laravel database to the new Supabase database schema.

## Prerequisites
- Supabase schema has been created (supabase_fleet_schema.sql)
- Laravel database is accessible
- PHP CLI available

## Step 1: Configure Database Connections

### Update the migration script
Edit `migrate_data_to_supabase.php` and update the database configurations:

```php
// Laravel Database (source)
$laravelConfig = [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'f7', // Your Laravel database name
    'username' => 'root', // Update with your credentials
    'password' => 'your_password', // Update with your password
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
];

// Supabase Database (target)
$supabaseConfig = [
    'driver' => 'pgsql',
    'host' => 'db.byjigntaprgwvvfodirl.supabase.co',
    'port' => '5432',
    'database' => 'postgres',
    'username' => 'postgres',
    'password' => 'your_supabase_password', // Replace with actual password
    'charset' => 'utf8',
];
```

## Step 2: Run the Migration Script

```bash
# Navigate to your project directory
cd /Users/yaminahmed/fleet

# Run the migration script
php migrate_data_to_supabase.php
```

This will generate `supabase_data_migration.sql` containing all your data transformed for Supabase.

## Step 3: Import Data to Supabase

### Option A: Supabase Dashboard
1. Go to your Supabase project dashboard
2. Navigate to SQL Editor
3. Copy and paste the contents of `supabase_data_migration.sql`
4. Click "Run" to execute

### Option B: Command Line
```bash
# Using psql (replace with your actual password)
psql "postgresql://postgres:your_password@db.byjigntaprgwvvfodirl.supabase.co:5432/postgres" -f supabase_data_migration.sql
```

## Step 4: Verify Migration

Run these queries in Supabase to verify the migration:

```sql
-- Check companies
SELECT COUNT(*) FROM companies;

-- Check users
SELECT COUNT(*) FROM users;

-- Check vehicles
SELECT COUNT(*) FROM vehicles;

-- Check bookings
SELECT COUNT(*) FROM bookings;

-- Check user types distribution
SELECT user_type, COUNT(*) FROM users GROUP BY user_type;

-- Check company assignments
SELECT c.name, COUNT(u.id) as user_count 
FROM companies c 
LEFT JOIN users u ON c.id = u.company_id 
GROUP BY c.id, c.name;
```

## Step 5: Test Data Integrity

```sql
-- Test relationships
SELECT 
    c.name as company_name,
    u.name as user_name,
    u.user_type,
    v.license_plate
FROM companies c
JOIN users u ON c.id = u.company_id
LEFT JOIN vehicles v ON c.id = v.company_id
LIMIT 10;

-- Test metadata
SELECT 
    u.name,
    um.metadata
FROM users u
JOIN user_metadata um ON u.id = um.user_id
LIMIT 5;

-- Test bookings
SELECT 
    b.id,
    c.name as customer_name,
    d.name as driver_name,
    v.license_plate,
    b.status
FROM bookings b
LEFT JOIN users c ON b.customer_id = c.id
LEFT JOIN users d ON b.driver_id = d.id
LEFT JOIN vehicles v ON b.vehicle_id = v.id
LIMIT 10;
```

## Data Transformation Details

### What Gets Migrated

1. **Companies** - All company records with UUIDs
2. **Users** - All users with proper company assignments
3. **Vehicles** - All vehicles with metadata converted to JSONB
4. **Bookings** - All bookings with proper relationships
5. **Settings** - All application settings
6. **User Metadata** - All user_data converted to JSONB
7. **Driver-Vehicle Assignments** - All assignments preserved

### Key Transformations

- **IDs**: Integer IDs → UUIDs
- **Metadata**: Separate meta tables → JSONB columns
- **Timestamps**: MySQL format → PostgreSQL format
- **Relationships**: Foreign keys updated to use new UUIDs
- **Data Types**: MySQL types → PostgreSQL types

### Special Handling

- **Boss Admins**: `company_id` set to NULL (as per schema)
- **User Types**: Preserved exactly (B, S, O, D, C)
- **Passwords**: Kept as-is (already hashed)
- **Soft Deletes**: `deleted_at` timestamps preserved

## Troubleshooting

### Common Issues

1. **Connection Errors**
   - Verify database credentials
   - Check network connectivity
   - Ensure database server is running

2. **Data Type Errors**
   - Check for NULL values in required fields
   - Verify date formats
   - Check string length limits

3. **Foreign Key Errors**
   - Ensure parent records exist
   - Check UUID format
   - Verify relationship mappings

### Rollback Plan

If migration fails:
1. Drop all tables in Supabase
2. Re-run the schema migration
3. Fix issues in the migration script
4. Re-run the data migration

## Post-Migration Tasks

1. **Update Application Code**
   - Change database connection to Supabase
   - Update models to use UUIDs
   - Modify queries for PostgreSQL

2. **Test Application**
   - Verify login functionality
   - Test all CRUD operations
   - Check data relationships

3. **Performance Optimization**
   - Add indexes for frequently queried columns
   - Optimize JSONB queries
   - Monitor query performance

## Support

If you encounter issues:
1. Check the generated SQL file for errors
2. Verify data mappings in the script
3. Test with a small subset of data first
4. Use Supabase logs for debugging

## Next Steps

After successful migration:
1. Update your Laravel application to use Supabase
2. Implement new features using Supabase capabilities
3. Set up real-time subscriptions
4. Configure Row Level Security policies
5. Set up automated backups

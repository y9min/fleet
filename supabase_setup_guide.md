# Supabase Setup Guide

## 1. Database Migration

### Option A: Supabase Dashboard (Easiest)
1. Go to your Supabase project dashboard
2. Navigate to SQL Editor
3. Copy and paste the contents of `supabase_fleet_schema.sql`
4. Click "Run" to execute the migration

### Option B: Supabase CLI (Recommended for Development)
```bash
# Install Supabase CLI
npm install -g supabase

# Login to Supabase
supabase login

# Link your project
supabase link --project-ref byjigntaprgwvvfodirl

# Run the migration
supabase db push
```

### Option C: Direct psql Connection
```bash
# Replace [YOUR-PASSWORD] with your actual database password
psql "postgresql://postgres:[YOUR-PASSWORD]@db.byjigntaprgwvvfodirl.supabase.co:5432/postgres" -f supabase_fleet_schema.sql
```

## 2. Environment Variables Setup

Create a `.env` file in your project root:

```env
# Supabase Configuration
SUPABASE_URL=https://byjigntaprgwvvfodirl.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJ5amlnbnRhcHJnd3Z2Zm9kaXJsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjAyODYzMDMsImV4cCI6MjA3NTg2MjMwM30.kdvuPuCiO2XyEjnO8KSJWABytFsK_td8c8V5GqJA5G4
SUPABASE_SERVICE_ROLE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJ5amlnbnRhcHJnd3Z2Zm9kaXJsIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2MDI4NjMwMywiZXhwIjoyMDc1ODYyMzAzfQ.VnDahnzfhbL5DveSCW_FMVuhU8wrd709Q3j3oaols7A

# Database Connection
DATABASE_URL=postgresql://postgres:[YOUR-PASSWORD]@db.byjigntaprgwvvfodirl.supabase.co:5432/postgres

# Stripe Configuration (Add your actual keys)
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Application Settings
APP_NAME="Fleet Management System"
APP_ENV=production
APP_DEBUG=false
```

## 3. Initial Data Seeding

After running the migration, execute this SQL in the Supabase SQL Editor:

```sql
-- 1. Create a default company
INSERT INTO companies (id, name, description, email, phone, address, is_active) 
VALUES (
    uuid_generate_v4(),
    'Default Fleet Company',
    'Default company for initial setup',
    'admin@fleetcompany.com',
    '+1-555-0123',
    '123 Fleet Street, City, State 12345',
    true
);

-- 2. Create boss admin user
INSERT INTO users (id, company_id, name, email, password, user_type, is_active, is_verified)
VALUES (
    uuid_generate_v4(),
    NULL, -- Boss admin has no company
    'Boss Admin',
    'boss@admin.com',
    crypt('password123', gen_salt('bf')),
    'B',
    true,
    true
);

-- 3. Create default vehicle types
INSERT INTO vehicle_types (id, name, display_name, seats, is_enabled) VALUES
    (uuid_generate_v4(), 'hatchback', 'Hatchback', 4, true),
    (uuid_generate_v4(), 'sedan', 'Sedan', 4, true),
    (uuid_generate_v4(), 'suv', 'SUV', 6, true),
    (uuid_generate_v4(), 'minivan', 'Mini Van', 7, true),
    (uuid_generate_v4(), 'bus', 'Bus', 40, true),
    (uuid_generate_v4(), 'truck', 'Truck', 3, true);

-- 4. Create default roles
INSERT INTO roles (id, name, display_name, description) VALUES
    (uuid_generate_v4(), 'boss_admin', 'Boss Admin', 'Full system access'),
    (uuid_generate_v4(), 'super_admin', 'Super Admin', 'Company-level admin'),
    (uuid_generate_v4(), 'office_admin', 'Office Admin', 'Office management'),
    (uuid_generate_v4(), 'driver', 'Driver', 'Vehicle operator'),
    (uuid_generate_v4(), 'customer', 'Customer', 'Service customer');

-- 5. Create default permissions
INSERT INTO permissions (id, name, display_name, description) VALUES
    (uuid_generate_v4(), 'manage_users', 'Manage Users', 'Create, edit, delete users'),
    (uuid_generate_v4(), 'manage_vehicles', 'Manage Vehicles', 'Manage vehicle fleet'),
    (uuid_generate_v4(), 'manage_bookings', 'Manage Bookings', 'Handle booking requests'),
    (uuid_generate_v4(), 'view_reports', 'View Reports', 'Access reporting features'),
    (uuid_generate_v4(), 'manage_billing', 'Manage Billing', 'Handle billing and payments');

-- 6. Create default settings
INSERT INTO settings (company_id, key, value, type, description) VALUES
    (SELECT id FROM companies LIMIT 1, 'app_name', 'Fleet Management System', 'string', 'Application name'),
    (SELECT id FROM companies LIMIT 1, 'currency', 'USD', 'string', 'Default currency'),
    (SELECT id FROM companies LIMIT 1, 'timezone', 'UTC', 'string', 'Default timezone'),
    (SELECT id FROM companies LIMIT 1, 'language', 'en', 'string', 'Default language');
```

## 4. Stripe Webhook Setup

1. Go to your Stripe Dashboard
2. Navigate to Developers > Webhooks
3. Add endpoint: `https://your-domain.com/api/stripe/webhook`
4. Select events to listen for:
   - `customer.created`
   - `customer.updated`
   - `subscription.created`
   - `subscription.updated`
   - `subscription.deleted`
   - `invoice.created`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`

## 5. Testing RLS Policies

Test that Row Level Security is working:

```sql
-- Test as different user types
-- This should only return data for the user's company
SELECT * FROM vehicles WHERE company_id = (SELECT company_id FROM users WHERE id = auth.uid());

-- Test boss admin access (should see all companies)
-- This requires setting up proper authentication context
```

## 6. Verification Checklist

- [ ] Migration completed successfully
- [ ] All tables created (40+ tables)
- [ ] RLS policies enabled
- [ ] Initial data seeded
- [ ] Environment variables configured
- [ ] Stripe webhook endpoint configured
- [ ] Test user can log in
- [ ] Data isolation working correctly

## 7. Troubleshooting

### Common Issues:

1. **Migration fails**: Check for syntax errors in SQL
2. **RLS policies not working**: Verify user authentication context
3. **Stripe webhook fails**: Check endpoint URL and event selection
4. **Connection issues**: Verify database credentials and network access

### Useful Commands:

```sql
-- Check if RLS is enabled
SELECT schemaname, tablename, rowsecurity 
FROM pg_tables 
WHERE schemaname = 'public';

-- List all tables
\dt

-- Check table structure
\d table_name

-- Test RLS policies
SET LOCAL ROLE authenticated;
SELECT * FROM companies;
```

## 8. Next Steps

1. **Application Integration**: Update your Laravel app to use Supabase
2. **Authentication**: Implement Supabase Auth or custom auth
3. **API Development**: Create REST/GraphQL endpoints
4. **Frontend**: Build React/Vue.js frontend
5. **Monitoring**: Set up logging and monitoring
6. **Backup**: Configure automated backups
7. **Scaling**: Plan for horizontal scaling

## Support

- Supabase Documentation: https://supabase.com/docs
- Stripe Documentation: https://stripe.com/docs
- PostgreSQL Documentation: https://www.postgresql.org/docs/

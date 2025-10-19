#!/bin/bash
# IMMEDIATE DATABASE FIX SCRIPT
# Run this in your Render service shell

echo "🔧 Fixing users_meta table..."

# Connect to your database and add the missing column
psql $DATABASE_URL -c "
ALTER TABLE users_meta ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL;
"

echo "✅ users_meta.deleted_at column added successfully!"

# Also fix other meta tables that might have the same issue
echo "🔧 Fixing other meta tables..."

psql $DATABASE_URL -c "
ALTER TABLE vehicles_meta ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL;
ALTER TABLE bookings_meta ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL;
"

echo "✅ All meta tables fixed!"

# Verify the fix
echo "🔍 Verifying fix..."
psql $DATABASE_URL -c "
SELECT column_name, data_type 
FROM information_schema.columns 
WHERE table_name = 'users_meta' AND column_name = 'deleted_at';
"

echo "🎉 Database fix complete! Try logging in now."

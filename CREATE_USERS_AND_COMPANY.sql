-- CHECK USER DATA AND CREATE MASTER USER IF NEEDED

-- First, let's see what users exist and their ID format
SELECT id, name, email, user_type, created_at FROM public.users ORDER BY created_at;

-- Check if master@admin.com exists
SELECT id, name, email, user_type FROM public.users WHERE email = 'master@admin.com';

-- If no users exist or master@admin.com doesn't exist, create it
INSERT INTO public.users (id, company_id, name, email, password, user_type, is_active, is_verified, created_at, updated_at)
SELECT 
    uuid_generate_v4(),
    NULL,
    'Master Admin',
    'master@admin.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
    'S', -- Super Admin
    true,
    true,
    now(),
    now()
WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'master@admin.com');

-- Also create yamzahmed@hotmail.com if it doesn't exist
INSERT INTO public.users (id, company_id, name, email, password, user_type, is_active, is_verified, created_at, updated_at)
SELECT 
    uuid_generate_v4(),
    NULL,
    'Yamz Ahmed',
    'yamzahmed@hotmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
    'S', -- Super Admin
    true,
    true,
    now(),
    now()
WHERE NOT EXISTS (SELECT 1 FROM public.users WHERE email = 'yamzahmed@hotmail.com');

-- Create a default company if none exists
INSERT INTO public.companies (id, name, description, email, phone, address, is_active, created_at, updated_at)
SELECT 
    uuid_generate_v4(),
    'Default Company',
    'Default company for the application',
    'admin@company.com',
    '+1234567890',
    '123 Main St, City, Country',
    true,
    now(),
    now()
WHERE NOT EXISTS (SELECT 1 FROM public.companies LIMIT 1);

-- Update users to have a company_id if they don't have one
UPDATE public.users 
SET company_id = (SELECT id FROM public.companies LIMIT 1)
WHERE company_id IS NULL;

-- Show final user data
SELECT id, name, email, user_type, company_id FROM public.users ORDER BY created_at;

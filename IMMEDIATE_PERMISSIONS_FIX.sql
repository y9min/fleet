-- IMMEDIATE PERMISSIONS FIX
-- Run this FIRST in Supabase SQL Editor to fix the 403 permission errors

-- ==============================================
-- 1. CREATE BASIC PERMISSIONS TABLES (if missing)
-- ==============================================

CREATE TABLE IF NOT EXISTS roles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL UNIQUE,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS permissions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL UNIQUE,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS role_has_permissions (
    permission_id UUID NOT NULL,
    role_id UUID NOT NULL,
    PRIMARY KEY (permission_id, role_id)
);

CREATE TABLE IF NOT EXISTS model_has_roles (
    role_id UUID NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id UUID NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type)
);

CREATE TABLE IF NOT EXISTS model_has_permissions (
    permission_id UUID NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id UUID NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type)
);

-- ==============================================
-- 2. CREATE SUPER ADMIN ROLE WITH ALL PERMISSIONS
-- ==============================================

-- Insert Super Admin role
INSERT INTO roles (id, name, guard_name) 
VALUES (uuid_generate_v4(), 'Super Admin', 'web')
ON CONFLICT (name) DO NOTHING;

-- Create a comprehensive set of permissions
INSERT INTO permissions (id, name, guard_name) VALUES
-- Core permissions
(uuid_generate_v4(), 'Users add', 'web'),
(uuid_generate_v4(), 'Users edit', 'web'),
(uuid_generate_v4(), 'Users delete', 'web'),
(uuid_generate_v4(), 'Users list', 'web'),
(uuid_generate_v4(), 'Drivers add', 'web'),
(uuid_generate_v4(), 'Drivers edit', 'web'),
(uuid_generate_v4(), 'Drivers delete', 'web'),
(uuid_generate_v4(), 'Drivers list', 'web'),
(uuid_generate_v4(), 'Customer add', 'web'),
(uuid_generate_v4(), 'Customer edit', 'web'),
(uuid_generate_v4(), 'Customer delete', 'web'),
(uuid_generate_v4(), 'Customer list', 'web'),
(uuid_generate_v4(), 'Vehicles add', 'web'),
(uuid_generate_v4(), 'Vehicles edit', 'web'),
(uuid_generate_v4(), 'Vehicles delete', 'web'),
(uuid_generate_v4(), 'Vehicles list', 'web'),
(uuid_generate_v4(), 'Bookings add', 'web'),
(uuid_generate_v4(), 'Bookings edit', 'web'),
(uuid_generate_v4(), 'Bookings delete', 'web'),
(uuid_generate_v4(), 'Bookings list', 'web'),
(uuid_generate_v4(), 'Settings add', 'web'),
(uuid_generate_v4(), 'Settings edit', 'web'),
(uuid_generate_v4(), 'Settings delete', 'web'),
(uuid_generate_v4(), 'Settings list', 'web')
ON CONFLICT (name) DO NOTHING;

-- ==============================================
-- 3. ASSIGN ALL PERMISSIONS TO SUPER ADMIN ROLE
-- ==============================================

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p
CROSS JOIN roles r
WHERE r.name = 'Super Admin'
ON CONFLICT (permission_id, role_id) DO NOTHING;

-- ==============================================
-- 4. ASSIGN SUPER ADMIN ROLE TO ALL USERS
-- ==============================================

INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\Model\\User', u.id
FROM roles r
CROSS JOIN users u
WHERE r.name = 'Super Admin'
ON CONFLICT (role_id, model_id, model_type) DO NOTHING;

-- ==============================================
-- 5. VERIFY SETUP
-- ==============================================

SELECT 
    '✅ PERMISSIONS FIX COMPLETE!' as status,
    'Super Admin role created and assigned to all users' as message,
    (SELECT COUNT(*) FROM roles) as roles_count,
    (SELECT COUNT(*) FROM permissions) as permissions_count,
    (SELECT COUNT(*) FROM model_has_roles) as user_assignments;


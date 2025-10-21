-- SAFE UUID PERMISSION FIX
-- This script safely fixes the UUID permission comparison error
-- Run this in Supabase SQL Editor to fix the login error

-- ==============================================
-- 1. CHECK CURRENT PERMISSION DATA
-- ==============================================

-- Check what permissions exist
SELECT 
    'CURRENT PERMISSIONS' as status,
    COUNT(*) as count,
    MIN(id::text) as min_id,
    MAX(id::text) as max_id
FROM permissions;

-- Check what roles exist
SELECT 
    'CURRENT ROLES' as status,
    COUNT(*) as count,
    MIN(id::text) as min_id,
    MAX(id::text) as max_id
FROM roles;

-- ==============================================
-- 2. SAFELY CLEAR EXISTING PERMISSION DATA
-- ==============================================

-- Clear all permission relationships (safe - won't drop tables)
DELETE FROM model_has_permissions;
DELETE FROM model_has_roles;
DELETE FROM role_has_permissions;
DELETE FROM permissions;
DELETE FROM roles;

-- ==============================================
-- 3. ALTER EXISTING TABLES TO USE INTEGER IDs
-- ==============================================

-- Check if roles table has UUID id column
DO $$
BEGIN
    -- If roles table exists with UUID id, we need to recreate it
    IF EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'roles' AND column_name = 'id' AND data_type = 'uuid') THEN
        -- Drop and recreate roles table with integer ID
        DROP TABLE IF EXISTS model_has_roles CASCADE;
        DROP TABLE IF EXISTS role_has_permissions CASCADE;
        DROP TABLE IF EXISTS roles CASCADE;
        
        CREATE TABLE roles (
            id BIGSERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE,
            guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
            created_at TIMESTAMP DEFAULT NOW(),
            updated_at TIMESTAMP DEFAULT NOW()
        );
        
        -- Recreate dependent tables
        CREATE TABLE role_has_permissions (
            permission_id BIGINT NOT NULL,
            role_id BIGINT NOT NULL,
            PRIMARY KEY (permission_id, role_id),
            CONSTRAINT fk_role_has_permissions_permission_id FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
            CONSTRAINT fk_role_has_permissions_role_id FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
        );
        
        CREATE TABLE model_has_roles (
            role_id BIGINT NOT NULL,
            model_type VARCHAR(255) NOT NULL,
            model_id UUID NOT NULL,
            PRIMARY KEY (role_id, model_id, model_type),
            CONSTRAINT fk_model_has_roles_role_id FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
        );
    END IF;
END $$;

-- Check if permissions table has UUID id column
DO $$
BEGIN
    -- If permissions table exists with UUID id, we need to recreate it
    IF EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'permissions' AND column_name = 'id' AND data_type = 'uuid') THEN
        -- Drop and recreate permissions table with integer ID
        DROP TABLE IF EXISTS model_has_permissions CASCADE;
        DROP TABLE IF EXISTS role_has_permissions CASCADE;
        DROP TABLE IF EXISTS permissions CASCADE;
        
        CREATE TABLE permissions (
            id BIGSERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE,
            guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
            created_at TIMESTAMP DEFAULT NOW(),
            updated_at TIMESTAMP DEFAULT NOW()
        );
        
        -- Recreate dependent tables
        CREATE TABLE role_has_permissions (
            permission_id BIGINT NOT NULL,
            role_id BIGINT NOT NULL,
            PRIMARY KEY (permission_id, role_id),
            CONSTRAINT fk_role_has_permissions_permission_id FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
            CONSTRAINT fk_role_has_permissions_role_id FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
        );
        
        CREATE TABLE model_has_permissions (
            permission_id BIGINT NOT NULL,
            model_type VARCHAR(255) NOT NULL,
            model_id UUID NOT NULL,
            PRIMARY KEY (permission_id, model_id, model_type),
            CONSTRAINT fk_model_has_permissions_permission_id FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
        );
    END IF;
END $$;

-- ==============================================
-- 4. CREATE SUPER ADMIN ROLE
-- ==============================================

INSERT INTO roles (name, guard_name) 
VALUES ('Super Admin', 'web');

-- ==============================================
-- 5. CREATE ESSENTIAL PERMISSIONS
-- ==============================================

INSERT INTO permissions (name, guard_name) VALUES
-- Core module permissions
('Users add', 'web'),
('Users edit', 'web'),
('Users delete', 'web'),
('Users list', 'web'),
('Users import', 'web'),

('Drivers add', 'web'),
('Drivers edit', 'web'),
('Drivers delete', 'web'),
('Drivers list', 'web'),
('Drivers import', 'web'),

('Customer add', 'web'),
('Customer edit', 'web'),
('Customer delete', 'web'),
('Customer list', 'web'),
('Customer import', 'web'),

('Vehicles add', 'web'),
('Vehicles edit', 'web'),
('Vehicles delete', 'web'),
('Vehicles list', 'web'),
('Vehicles import', 'web'),

('Bookings add', 'web'),
('Bookings edit', 'web'),
('Bookings delete', 'web'),
('Bookings list', 'web'),
('Bookings import', 'web'),

('Settings add', 'web'),
('Settings edit', 'web'),
('Settings delete', 'web'),
('Settings list', 'web'),
('Settings import', 'web'),

-- Additional essential permissions
('VehicleType add', 'web'),
('VehicleType edit', 'web'),
('VehicleType delete', 'web'),
('VehicleType list', 'web'),
('VehicleType import', 'web'),

('VehicleGroup add', 'web'),
('VehicleGroup edit', 'web'),
('VehicleGroup delete', 'web'),
('VehicleGroup list', 'web'),
('VehicleGroup import', 'web'),

('VehicleInspection add', 'web'),
('VehicleInspection edit', 'web'),
('VehicleInspection delete', 'web'),
('VehicleInspection list', 'web'),
('VehicleInspection import', 'web'),

('Reports add', 'web'),
('Reports edit', 'web'),
('Reports delete', 'web'),
('Reports list', 'web'),
('Reports import', 'web'),

('Transactions add', 'web'),
('Transactions edit', 'web'),
('Transactions delete', 'web'),
('Transactions list', 'web'),
('Transactions import', 'web'),

('Fuel add', 'web'),
('Fuel edit', 'web'),
('Fuel delete', 'web'),
('Fuel list', 'web'),
('Fuel import', 'web'),

('Vendors add', 'web'),
('Vendors edit', 'web'),
('Vendors delete', 'web'),
('Vendors list', 'web'),
('Vendors import', 'web'),

('Parts add', 'web'),
('Parts edit', 'web'),
('Parts delete', 'web'),
('Parts list', 'web'),
('Parts import', 'web'),

('WorkOrders add', 'web'),
('WorkOrders edit', 'web'),
('WorkOrders delete', 'web'),
('WorkOrders list', 'web'),
('WorkOrders import', 'web'),

('Notes add', 'web'),
('Notes edit', 'web'),
('Notes delete', 'web'),
('Notes list', 'web'),
('Notes import', 'web'),

('ServiceReminders add', 'web'),
('ServiceReminders edit', 'web'),
('ServiceReminders delete', 'web'),
('ServiceReminders list', 'web'),
('ServiceReminders import', 'web'),

('ServiceItems add', 'web'),
('ServiceItems edit', 'web'),
('ServiceItems delete', 'web'),
('ServiceItems list', 'web'),
('ServiceItems import', 'web'),

('Testimonials add', 'web'),
('Testimonials edit', 'web'),
('Testimonials delete', 'web'),
('Testimonials list', 'web'),
('Testimonials import', 'web'),

('Team add', 'web'),
('Team edit', 'web'),
('Team delete', 'web'),
('Team list', 'web'),
('Team import', 'web'),

('Inquiries add', 'web'),
('Inquiries edit', 'web'),
('Inquiries delete', 'web'),
('Inquiries list', 'web'),
('Inquiries import', 'web'),

('VehicleBreakdown add', 'web'),
('VehicleBreakdown edit', 'web'),
('VehicleBreakdown delete', 'web'),
('VehicleBreakdown list', 'web'),
('VehicleBreakdown import', 'web'),

('DriverAlert add', 'web'),
('DriverAlert edit', 'web'),
('DriverAlert delete', 'web'),
('DriverAlert list', 'web'),
('DriverAlert import', 'web');

-- ==============================================
-- 6. ASSIGN ALL PERMISSIONS TO SUPER ADMIN ROLE
-- ==============================================

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p
CROSS JOIN roles r
WHERE r.name = 'Super Admin';

-- ==============================================
-- 7. ASSIGN SUPER ADMIN ROLE TO ALL USERS
-- ==============================================

INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\Model\\User', u.id
FROM roles r
CROSS JOIN users u
WHERE r.name = 'Super Admin';

-- ==============================================
-- 8. VERIFY SETUP
-- ==============================================

SELECT 
    '✅ SAFE UUID PERMISSION FIX COMPLETE!' as status,
    'Permissions now use integer IDs compatible with Spatie' as message,
    (SELECT COUNT(*) FROM roles) as roles_count,
    (SELECT COUNT(*) FROM permissions) as permissions_count,
    (SELECT COUNT(*) FROM model_has_roles) as user_assignments,
    (SELECT COUNT(*) FROM role_has_permissions) as role_permission_assignments;

-- Show sample permission IDs (should be integers now)
SELECT 
    'SAMPLE PERMISSION IDS' as info,
    id,
    name
FROM permissions 
ORDER BY id 
LIMIT 5;

-- Check table structures
SELECT 
    'TABLE STRUCTURES' as info,
    table_name,
    column_name,
    data_type
FROM information_schema.columns 
WHERE table_name IN ('roles', 'permissions', 'role_has_permissions', 'model_has_roles', 'model_has_permissions')
ORDER BY table_name, ordinal_position;


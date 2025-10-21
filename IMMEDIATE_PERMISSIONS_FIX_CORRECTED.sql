-- IMMEDIATE PERMISSIONS FIX - CORRECTED VERSION
-- Run this in Supabase SQL Editor to fix the 403 permission errors

-- ==============================================
-- 1. CREATE OR ALTER PERMISSIONS TABLES
-- ==============================================

-- Drop existing tables if they exist (to ensure clean setup)
DROP TABLE IF EXISTS model_has_permissions CASCADE;
DROP TABLE IF EXISTS model_has_roles CASCADE;
DROP TABLE IF EXISTS role_has_permissions CASCADE;
DROP TABLE IF EXISTS permissions CASCADE;
DROP TABLE IF EXISTS roles CASCADE;

-- Create roles table with correct structure
CREATE TABLE roles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL UNIQUE,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Create permissions table with correct structure
CREATE TABLE permissions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL UNIQUE,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Create role_has_permissions table
CREATE TABLE role_has_permissions (
    permission_id UUID NOT NULL,
    role_id UUID NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    CONSTRAINT fk_role_has_permissions_permission_id FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_has_permissions_role_id FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- Create model_has_roles table
CREATE TABLE model_has_roles (
    role_id UUID NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id UUID NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    CONSTRAINT fk_model_has_roles_role_id FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- Create model_has_permissions table
CREATE TABLE model_has_permissions (
    permission_id UUID NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id UUID NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type),
    CONSTRAINT fk_model_has_permissions_permission_id FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- ==============================================
-- 2. CREATE SUPER ADMIN ROLE
-- ==============================================

INSERT INTO roles (id, name, guard_name) 
VALUES (uuid_generate_v4(), 'Super Admin', 'web');

-- ==============================================
-- 3. CREATE ESSENTIAL PERMISSIONS
-- ==============================================

INSERT INTO permissions (id, name, guard_name) VALUES
-- Core module permissions
(uuid_generate_v4(), 'Users add', 'web'),
(uuid_generate_v4(), 'Users edit', 'web'),
(uuid_generate_v4(), 'Users delete', 'web'),
(uuid_generate_v4(), 'Users list', 'web'),
(uuid_generate_v4(), 'Users import', 'web'),

(uuid_generate_v4(), 'Drivers add', 'web'),
(uuid_generate_v4(), 'Drivers edit', 'web'),
(uuid_generate_v4(), 'Drivers delete', 'web'),
(uuid_generate_v4(), 'Drivers list', 'web'),
(uuid_generate_v4(), 'Drivers import', 'web'),

(uuid_generate_v4(), 'Customer add', 'web'),
(uuid_generate_v4(), 'Customer edit', 'web'),
(uuid_generate_v4(), 'Customer delete', 'web'),
(uuid_generate_v4(), 'Customer list', 'web'),
(uuid_generate_v4(), 'Customer import', 'web'),

(uuid_generate_v4(), 'Vehicles add', 'web'),
(uuid_generate_v4(), 'Vehicles edit', 'web'),
(uuid_generate_v4(), 'Vehicles delete', 'web'),
(uuid_generate_v4(), 'Vehicles list', 'web'),
(uuid_generate_v4(), 'Vehicles import', 'web'),

(uuid_generate_v4(), 'Bookings add', 'web'),
(uuid_generate_v4(), 'Bookings edit', 'web'),
(uuid_generate_v4(), 'Bookings delete', 'web'),
(uuid_generate_v4(), 'Bookings list', 'web'),
(uuid_generate_v4(), 'Bookings import', 'web'),

(uuid_generate_v4(), 'Settings add', 'web'),
(uuid_generate_v4(), 'Settings edit', 'web'),
(uuid_generate_v4(), 'Settings delete', 'web'),
(uuid_generate_v4(), 'Settings list', 'web'),
(uuid_generate_v4(), 'Settings import', 'web'),

-- Additional essential permissions
(uuid_generate_v4(), 'VehicleType add', 'web'),
(uuid_generate_v4(), 'VehicleType edit', 'web'),
(uuid_generate_v4(), 'VehicleType delete', 'web'),
(uuid_generate_v4(), 'VehicleType list', 'web'),
(uuid_generate_v4(), 'VehicleType import', 'web'),

(uuid_generate_v4(), 'VehicleGroup add', 'web'),
(uuid_generate_v4(), 'VehicleGroup edit', 'web'),
(uuid_generate_v4(), 'VehicleGroup delete', 'web'),
(uuid_generate_v4(), 'VehicleGroup list', 'web'),
(uuid_generate_v4(), 'VehicleGroup import', 'web'),

(uuid_generate_v4(), 'VehicleInspection add', 'web'),
(uuid_generate_v4(), 'VehicleInspection edit', 'web'),
(uuid_generate_v4(), 'VehicleInspection delete', 'web'),
(uuid_generate_v4(), 'VehicleInspection list', 'web'),
(uuid_generate_v4(), 'VehicleInspection import', 'web'),

(uuid_generate_v4(), 'Reports add', 'web'),
(uuid_generate_v4(), 'Reports edit', 'web'),
(uuid_generate_v4(), 'Reports delete', 'web'),
(uuid_generate_v4(), 'Reports list', 'web'),
(uuid_generate_v4(), 'Reports import', 'web');

-- ==============================================
-- 4. ASSIGN ALL PERMISSIONS TO SUPER ADMIN ROLE
-- ==============================================

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p
CROSS JOIN roles r
WHERE r.name = 'Super Admin';

-- ==============================================
-- 5. ASSIGN SUPER ADMIN ROLE TO ALL USERS
-- ==============================================

INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\Model\\User', u.id
FROM roles r
CROSS JOIN users u
WHERE r.name = 'Super Admin';

-- ==============================================
-- 6. VERIFY SETUP
-- ==============================================

SELECT 
    '✅ PERMISSIONS FIX COMPLETE!' as status,
    'Super Admin role created and assigned to all users' as message,
    (SELECT COUNT(*) FROM roles) as roles_count,
    (SELECT COUNT(*) FROM permissions) as permissions_count,
    (SELECT COUNT(*) FROM model_has_roles) as user_assignments,
    (SELECT COUNT(*) FROM role_has_permissions) as role_permission_assignments;


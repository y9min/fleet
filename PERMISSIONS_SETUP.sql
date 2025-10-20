-- COMPREHENSIVE PERMISSIONS SETUP FOR SUPABASE
-- This script creates all roles, permissions, and assigns them to users
-- Run this in your Supabase SQL Editor to fix the 403 permission errors

-- ==============================================
-- 1. CREATE ROLES TABLE (if missing)
-- ==============================================

CREATE TABLE IF NOT EXISTS roles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL UNIQUE,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- ==============================================
-- 2. CREATE PERMISSIONS TABLE (if missing)
-- ==============================================

CREATE TABLE IF NOT EXISTS permissions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL UNIQUE,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- ==============================================
-- 3. CREATE ROLE_HAS_PERMISSIONS TABLE (if missing)
-- ==============================================

CREATE TABLE IF NOT EXISTS role_has_permissions (
    permission_id UUID NOT NULL,
    role_id UUID NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    CONSTRAINT fk_role_has_permissions_permission_id FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_has_permissions_role_id FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- ==============================================
-- 4. CREATE MODEL_HAS_ROLES TABLE (if missing)
-- ==============================================

CREATE TABLE IF NOT EXISTS model_has_roles (
    role_id UUID NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id UUID NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    CONSTRAINT fk_model_has_roles_role_id FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- ==============================================
-- 5. CREATE MODEL_HAS_PERMISSIONS TABLE (if missing)
-- ==============================================

CREATE TABLE IF NOT EXISTS model_has_permissions (
    permission_id UUID NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id UUID NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type),
    CONSTRAINT fk_model_has_permissions_permission_id FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- ==============================================
-- 6. CREATE ALL PERMISSIONS
-- ==============================================

-- Clear existing permissions first
DELETE FROM role_has_permissions;
DELETE FROM model_has_permissions;
DELETE FROM permissions;

-- Insert all module permissions
INSERT INTO permissions (id, name, guard_name) VALUES
-- Users module
(uuid_generate_v4(), 'Users add', 'web'),
(uuid_generate_v4(), 'Users edit', 'web'),
(uuid_generate_v4(), 'Users delete', 'web'),
(uuid_generate_v4(), 'Users list', 'web'),
(uuid_generate_v4(), 'Users import', 'web'),

-- Drivers module
(uuid_generate_v4(), 'Drivers add', 'web'),
(uuid_generate_v4(), 'Drivers edit', 'web'),
(uuid_generate_v4(), 'Drivers delete', 'web'),
(uuid_generate_v4(), 'Drivers list', 'web'),
(uuid_generate_v4(), 'Drivers import', 'web'),

-- Customer module
(uuid_generate_v4(), 'Customer add', 'web'),
(uuid_generate_v4(), 'Customer edit', 'web'),
(uuid_generate_v4(), 'Customer delete', 'web'),
(uuid_generate_v4(), 'Customer list', 'web'),
(uuid_generate_v4(), 'Customer import', 'web'),

-- VehicleType module
(uuid_generate_v4(), 'VehicleType add', 'web'),
(uuid_generate_v4(), 'VehicleType edit', 'web'),
(uuid_generate_v4(), 'VehicleType delete', 'web'),
(uuid_generate_v4(), 'VehicleType list', 'web'),
(uuid_generate_v4(), 'VehicleType import', 'web'),

-- VehicleMaker module
(uuid_generate_v4(), 'VehicleMaker add', 'web'),
(uuid_generate_v4(), 'VehicleMaker edit', 'web'),
(uuid_generate_v4(), 'VehicleMaker delete', 'web'),
(uuid_generate_v4(), 'VehicleMaker list', 'web'),
(uuid_generate_v4(), 'VehicleMaker import', 'web'),

-- VehicleModels module
(uuid_generate_v4(), 'VehicleModels add', 'web'),
(uuid_generate_v4(), 'VehicleModels edit', 'web'),
(uuid_generate_v4(), 'VehicleModels delete', 'web'),
(uuid_generate_v4(), 'VehicleModels list', 'web'),
(uuid_generate_v4(), 'VehicleModels import', 'web'),

-- VehicleColors module
(uuid_generate_v4(), 'VehicleColors add', 'web'),
(uuid_generate_v4(), 'VehicleColors edit', 'web'),
(uuid_generate_v4(), 'VehicleColors delete', 'web'),
(uuid_generate_v4(), 'VehicleColors list', 'web'),
(uuid_generate_v4(), 'VehicleColors import', 'web'),

-- VehicleGroup module
(uuid_generate_v4(), 'VehicleGroup add', 'web'),
(uuid_generate_v4(), 'VehicleGroup edit', 'web'),
(uuid_generate_v4(), 'VehicleGroup delete', 'web'),
(uuid_generate_v4(), 'VehicleGroup list', 'web'),
(uuid_generate_v4(), 'VehicleGroup import', 'web'),

-- VehicleInspection module
(uuid_generate_v4(), 'VehicleInspection add', 'web'),
(uuid_generate_v4(), 'VehicleInspection edit', 'web'),
(uuid_generate_v4(), 'VehicleInspection delete', 'web'),
(uuid_generate_v4(), 'VehicleInspection list', 'web'),
(uuid_generate_v4(), 'VehicleInspection import', 'web'),

-- BookingQuotations module
(uuid_generate_v4(), 'BookingQuotations add', 'web'),
(uuid_generate_v4(), 'BookingQuotations edit', 'web'),
(uuid_generate_v4(), 'BookingQuotations delete', 'web'),
(uuid_generate_v4(), 'BookingQuotations list', 'web'),
(uuid_generate_v4(), 'BookingQuotations import', 'web'),

-- PartsCategory module
(uuid_generate_v4(), 'PartsCategory add', 'web'),
(uuid_generate_v4(), 'PartsCategory edit', 'web'),
(uuid_generate_v4(), 'PartsCategory delete', 'web'),
(uuid_generate_v4(), 'PartsCategory list', 'web'),
(uuid_generate_v4(), 'PartsCategory import', 'web'),

-- Mechanics module
(uuid_generate_v4(), 'Mechanics add', 'web'),
(uuid_generate_v4(), 'Mechanics edit', 'web'),
(uuid_generate_v4(), 'Mechanics delete', 'web'),
(uuid_generate_v4(), 'Mechanics list', 'web'),
(uuid_generate_v4(), 'Mechanics import', 'web'),

-- Vehicles module
(uuid_generate_v4(), 'Vehicles add', 'web'),
(uuid_generate_v4(), 'Vehicles edit', 'web'),
(uuid_generate_v4(), 'Vehicles delete', 'web'),
(uuid_generate_v4(), 'Vehicles list', 'web'),
(uuid_generate_v4(), 'Vehicles import', 'web'),

-- Transactions module
(uuid_generate_v4(), 'Transactions add', 'web'),
(uuid_generate_v4(), 'Transactions edit', 'web'),
(uuid_generate_v4(), 'Transactions delete', 'web'),
(uuid_generate_v4(), 'Transactions list', 'web'),
(uuid_generate_v4(), 'Transactions import', 'web'),

-- Bookings module
(uuid_generate_v4(), 'Bookings add', 'web'),
(uuid_generate_v4(), 'Bookings edit', 'web'),
(uuid_generate_v4(), 'Bookings delete', 'web'),
(uuid_generate_v4(), 'Bookings list', 'web'),
(uuid_generate_v4(), 'Bookings import', 'web'),

-- Reports module
(uuid_generate_v4(), 'Reports add', 'web'),
(uuid_generate_v4(), 'Reports edit', 'web'),
(uuid_generate_v4(), 'Reports delete', 'web'),
(uuid_generate_v4(), 'Reports list', 'web'),
(uuid_generate_v4(), 'Reports import', 'web'),

-- Fuel module
(uuid_generate_v4(), 'Fuel add', 'web'),
(uuid_generate_v4(), 'Fuel edit', 'web'),
(uuid_generate_v4(), 'Fuel delete', 'web'),
(uuid_generate_v4(), 'Fuel list', 'web'),
(uuid_generate_v4(), 'Fuel import', 'web'),

-- Vendors module
(uuid_generate_v4(), 'Vendors add', 'web'),
(uuid_generate_v4(), 'Vendors edit', 'web'),
(uuid_generate_v4(), 'Vendors delete', 'web'),
(uuid_generate_v4(), 'Vendors list', 'web'),
(uuid_generate_v4(), 'Vendors import', 'web'),

-- Parts module
(uuid_generate_v4(), 'Parts add', 'web'),
(uuid_generate_v4(), 'Parts edit', 'web'),
(uuid_generate_v4(), 'Parts delete', 'web'),
(uuid_generate_v4(), 'Parts list', 'web'),
(uuid_generate_v4(), 'Parts import', 'web'),

-- WorkOrders module
(uuid_generate_v4(), 'WorkOrders add', 'web'),
(uuid_generate_v4(), 'WorkOrders edit', 'web'),
(uuid_generate_v4(), 'WorkOrders delete', 'web'),
(uuid_generate_v4(), 'WorkOrders list', 'web'),
(uuid_generate_v4(), 'WorkOrders import', 'web'),

-- Notes module
(uuid_generate_v4(), 'Notes add', 'web'),
(uuid_generate_v4(), 'Notes edit', 'web'),
(uuid_generate_v4(), 'Notes delete', 'web'),
(uuid_generate_v4(), 'Notes list', 'web'),
(uuid_generate_v4(), 'Notes import', 'web'),

-- ServiceReminders module
(uuid_generate_v4(), 'ServiceReminders add', 'web'),
(uuid_generate_v4(), 'ServiceReminders edit', 'web'),
(uuid_generate_v4(), 'ServiceReminders delete', 'web'),
(uuid_generate_v4(), 'ServiceReminders list', 'web'),
(uuid_generate_v4(), 'ServiceReminders import', 'web'),

-- ServiceItems module
(uuid_generate_v4(), 'ServiceItems add', 'web'),
(uuid_generate_v4(), 'ServiceItems edit', 'web'),
(uuid_generate_v4(), 'ServiceItems delete', 'web'),
(uuid_generate_v4(), 'ServiceItems list', 'web'),
(uuid_generate_v4(), 'ServiceItems import', 'web'),

-- Testimonials module
(uuid_generate_v4(), 'Testimonials add', 'web'),
(uuid_generate_v4(), 'Testimonials edit', 'web'),
(uuid_generate_v4(), 'Testimonials delete', 'web'),
(uuid_generate_v4(), 'Testimonials list', 'web'),
(uuid_generate_v4(), 'Testimonials import', 'web'),

-- Team module
(uuid_generate_v4(), 'Team add', 'web'),
(uuid_generate_v4(), 'Team edit', 'web'),
(uuid_generate_v4(), 'Team delete', 'web'),
(uuid_generate_v4(), 'Team list', 'web'),
(uuid_generate_v4(), 'Team import', 'web'),

-- Settings module
(uuid_generate_v4(), 'Settings add', 'web'),
(uuid_generate_v4(), 'Settings edit', 'web'),
(uuid_generate_v4(), 'Settings delete', 'web'),
(uuid_generate_v4(), 'Settings list', 'web'),
(uuid_generate_v4(), 'Settings import', 'web'),

-- Inquiries module
(uuid_generate_v4(), 'Inquiries add', 'web'),
(uuid_generate_v4(), 'Inquiries edit', 'web'),
(uuid_generate_v4(), 'Inquiries delete', 'web'),
(uuid_generate_v4(), 'Inquiries list', 'web'),
(uuid_generate_v4(), 'Inquiries import', 'web'),

-- VehicleBreakdown module
(uuid_generate_v4(), 'VehicleBreakdown add', 'web'),
(uuid_generate_v4(), 'VehicleBreakdown edit', 'web'),
(uuid_generate_v4(), 'VehicleBreakdown delete', 'web'),
(uuid_generate_v4(), 'VehicleBreakdown list', 'web'),
(uuid_generate_v4(), 'VehicleBreakdown import', 'web'),

-- DriverAlert module
(uuid_generate_v4(), 'DriverAlert add', 'web'),
(uuid_generate_v4(), 'DriverAlert edit', 'web'),
(uuid_generate_v4(), 'DriverAlert delete', 'web'),
(uuid_generate_v4(), 'DriverAlert list', 'web'),
(uuid_generate_v4(), 'DriverAlert import', 'web');

-- ==============================================
-- 7. CREATE ROLES
-- ==============================================

-- Clear existing roles first
DELETE FROM model_has_roles;
DELETE FROM roles;

-- Create Super Admin role
INSERT INTO roles (id, name, guard_name) VALUES
(uuid_generate_v4(), 'Super Admin', 'web');

-- Create Admin role
INSERT INTO roles (id, name, guard_name) VALUES
(uuid_generate_v4(), 'Admin', 'web');

-- ==============================================
-- 8. ASSIGN ALL PERMISSIONS TO SUPER ADMIN
-- ==============================================

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p
CROSS JOIN roles r
WHERE r.name = 'Super Admin';

-- ==============================================
-- 9. ASSIGN LIMITED PERMISSIONS TO ADMIN
-- ==============================================

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p
CROSS JOIN roles r
WHERE r.name = 'Admin'
AND p.name IN (
    'Bookings list', 'Bookings add', 'Bookings edit', 'Bookings delete',
    'Drivers list', 'Drivers add', 'Drivers edit', 'Drivers delete',
    'Customer list', 'Customer add', 'Customer edit', 'Customer delete'
);

-- ==============================================
-- 10. ASSIGN ROLES TO USERS
-- ==============================================

-- Assign Super Admin role to all users with user_type 'S' (Super Admin)
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\Model\\User', u.id
FROM roles r
CROSS JOIN users u
WHERE r.name = 'Super Admin'
AND u.user_type = 'S';

-- Assign Admin role to all users with user_type 'O' (Office Admin)
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\Model\\User', u.id
FROM roles r
CROSS JOIN users u
WHERE r.name = 'Admin'
AND u.user_type = 'O';

-- ==============================================
-- 11. VERIFICATION QUERIES
-- ==============================================

-- Check if roles were created
SELECT 
    'ROLES CREATED' as status,
    COUNT(*) as count,
    STRING_AGG(name, ', ') as role_names
FROM roles;

-- Check if permissions were created
SELECT 
    'PERMISSIONS CREATED' as status,
    COUNT(*) as count
FROM permissions;

-- Check role assignments
SELECT 
    'ROLE ASSIGNMENTS' as status,
    COUNT(*) as count
FROM model_has_roles;

-- Check permission assignments
SELECT 
    'PERMISSION ASSIGNMENTS' as status,
    COUNT(*) as count
FROM role_has_permissions;

-- ==============================================
-- 12. SUCCESS MESSAGE
-- ==============================================

SELECT 
    '🎉 PERMISSIONS SETUP COMPLETE!' as message,
    'All roles and permissions have been created and assigned' as details,
    'The 403 permission errors should now be resolved' as result;

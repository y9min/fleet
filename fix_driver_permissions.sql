-- Fix Driver Permissions Script
-- This script ensures that Super Admin users have the required permissions to see Add Driver and Import Drivers buttons

-- Insert permissions if they don't exist
INSERT IGNORE INTO permissions (name, guard_name, created_at, updated_at) VALUES
('Drivers add', 'web', NOW(), NOW()),
('Drivers edit', 'web', NOW(), NOW()),
('Drivers delete', 'web', NOW(), NOW()),
('Drivers list', 'web', NOW(), NOW()),
('Drivers import', 'web', NOW(), NOW()),
('Drivers map', 'web', NOW(), NOW());

-- Create Super Admin role if it doesn't exist
INSERT IGNORE INTO roles (name, guard_name, created_at, updated_at) VALUES
('Super Admin', 'web', NOW(), NOW());

-- Create Admin role if it doesn't exist
INSERT IGNORE INTO roles (name, guard_name, created_at, updated_at) VALUES
('Admin', 'web', NOW(), NOW());

-- Assign all permissions to Super Admin role
INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p, roles r
WHERE r.name = 'Super Admin';

-- Assign driver permissions to Admin role
INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p, roles r
WHERE r.name = 'Admin' 
AND p.name IN ('Drivers add', 'Drivers edit', 'Drivers delete', 'Drivers list', 'Drivers import');

-- Assign Super Admin role to all Super Admin users (user_type = 'S')
INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\Model\\User', u.id
FROM roles r, users u
WHERE r.name = 'Super Admin' 
AND u.user_type = 'S';

-- Assign Admin role to all Office Admin users (user_type = 'O')
INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\Model\\User', u.id
FROM roles r, users u
WHERE r.name = 'Admin' 
AND u.user_type = 'O';

-- Show results
SELECT 'Permissions created/updated successfully!' as message;
SELECT COUNT(*) as total_permissions FROM permissions WHERE name LIKE 'Drivers%';
SELECT COUNT(*) as super_admin_users FROM users WHERE user_type = 'S';
SELECT COUNT(*) as admin_users FROM users WHERE user_type = 'O';

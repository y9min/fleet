<?php
/**
 * Fix Driver Permissions Script
 * This script ensures that Super Admin users have the required permissions to see Add Driver and Import Drivers buttons
 */

require_once 'framework/bootstrap/app.php';

use App\Model\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

echo "=== Fixing Driver Permissions ===\n";

try {
    // Ensure permissions exist
    $permissions = [
        'Drivers add',
        'Drivers edit', 
        'Drivers delete',
        'Drivers list',
        'Drivers import',
        'Drivers map'
    ];
    
    echo "Creating/Checking permissions...\n";
    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
        echo "✓ Permission: $permission\n";
    }
    
    // Ensure Super Admin role exists and has all permissions
    $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
    $superAdminRole->givePermissionTo(Permission::all());
    echo "✓ Super Admin role updated with all permissions\n";
    
    // Assign Super Admin role to all Super Admin users (user_type = 'S')
    $superAdminUsers = User::where('user_type', 'S')->get();
    echo "Found " . $superAdminUsers->count() . " Super Admin users\n";
    
    foreach ($superAdminUsers as $user) {
        $user->assignRole('Super Admin');
        echo "✓ Assigned Super Admin role to: " . $user->name . " (" . $user->email . ")\n";
    }
    
    // Also ensure Admin role has driver permissions
    $adminRole = Role::firstOrCreate(['name' => 'Admin']);
    $adminRole->givePermissionTo([
        'Drivers add',
        'Drivers edit', 
        'Drivers delete',
        'Drivers list',
        'Drivers import'
    ]);
    echo "✓ Admin role updated with driver permissions\n";
    
    // Assign Admin role to Office Admin users (user_type = 'O')
    $officeAdminUsers = User::where('user_type', 'O')->get();
    echo "Found " . $officeAdminUsers->count() . " Office Admin users\n";
    
    foreach ($officeAdminUsers as $user) {
        $user->assignRole('Admin');
        echo "✓ Assigned Admin role to: " . $user->name . " (" . $user->email . ")\n";
    }
    
    echo "\n=== Permission Fix Complete ===\n";
    echo "The Add Driver and Import Drivers buttons should now be visible!\n";
    echo "Please refresh your browser and check the Drivers page.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
}

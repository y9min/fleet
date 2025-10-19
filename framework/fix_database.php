<?php
// IMMEDIATE DATABASE FIX - Run this via Render shell
// php fix_database.php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing users_meta table...\n";

try {
    // Check if users_meta table exists
    if (!Schema::hasTable('users_meta')) {
        echo "❌ users_meta table doesn't exist. Running migrations...\n";
        Artisan::call('migrate', ['--force' => true]);
    } else {
        // Check if deleted_at column exists
        if (!Schema::hasColumn('users_meta', 'deleted_at')) {
            echo "➕ Adding deleted_at column to users_meta...\n";
            Schema::table('users_meta', function ($table) {
                $table->softDeletes();
            });
        }
        
        // Fix other meta tables
        $metaTables = ['vehicles_meta', 'bookings_meta'];
        foreach ($metaTables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {
                echo "➕ Adding deleted_at column to {$table}...\n";
                Schema::table($table, function ($table) {
                    $table->softDeletes();
                });
            }
        }
    }
    
    echo "✅ Database fix completed successfully!\n";
    echo "🎉 You can now try logging in again.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
}

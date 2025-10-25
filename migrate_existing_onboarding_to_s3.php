<?php

/**
 * Migration script to upload existing onboarding documents to S3
 * Run this on production: php migrate_existing_onboarding_to_s3.php
 */

require __DIR__ . '/framework/vendor/autoload.php';

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/framework/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Starting migration of onboarding documents to S3...\n";

// Check if S3 is configured
$useS3 = env('AWS_BUCKET') && env('AWS_KEY') && env('AWS_SECRET');

if (!$useS3) {
    echo "S3 is not configured. Skipping migration.\n";
    exit(0);
}

$s3BaseUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_REGION') . '.amazonaws.com/';
echo "S3 Bucket: " . env('AWS_BUCKET') . "\n";
echo "S3 Region: " . env('AWS_REGION') . "\n\n";

// Get all onboarding drivers with documents
$drivers = DB::table('onboarding_drivers')
    ->whereNotNull('license_upload_path')
    ->orWhereNotNull('insurance_upload_path')
    ->get();

echo "Found " . count($drivers) . " drivers with documents\n\n";

$uploaded = 0;
$skipped = 0;
$errors = 0;

foreach ($drivers as $driver) {
    echo "Processing driver: {$driver->name} (ID: {$driver->id})\n";
    
    // Handle license document
    if ($driver->license_upload_path) {
        echo "  License: {$driver->license_upload_path}\n";
        
        // Check if already in S3 format
        if (strpos($driver->license_upload_path, 'onboarding/documents/') === 0) {
            // Old format - try to upload from local storage
            $localPath = storage_path('app/public/' . $driver->license_upload_path);
            
            if (file_exists($localPath)) {
                try {
                    $filename = basename($driver->license_upload_path);
                    $s3Path = 'uploads/onboarding/' . $filename;
                    
                    Storage::disk('s3')->put($s3Path, file_get_contents($localPath));
                    
                    // Update database to remove old path prefix
                    DB::table('onboarding_drivers')
                        ->where('id', $driver->id)
                        ->update(['license_upload_path' => $filename]);
                    
                    echo "    ✓ Uploaded to S3: {$s3Path}\n";
                    echo "    ✓ Updated database\n";
                    $uploaded++;
                } catch (\Exception $e) {
                    echo "    ✗ Error: " . $e->getMessage() . "\n";
                    $errors++;
                }
            } else {
                echo "    ⚠ File not found locally: {$localPath}\n";
                $skipped++;
            }
        } else {
            echo "    → Already in correct format\n";
            $skipped++;
        }
    }
    
    // Handle insurance document
    if ($driver->insurance_upload_path) {
        echo "  Insurance: {$driver->insurance_upload_path}\n";
        
        // Check if already in S3 format
        if (strpos($driver->insurance_upload_path, 'onboarding/documents/') === 0) {
            // Old format - try to upload from local storage
            $localPath = storage_path('app/public/' . $driver->insurance_upload_path);
            
            if (file_exists($localPath)) {
                try {
                    $filename = basename($driver->insurance_upload_path);
                    $s3Path = 'uploads/onboarding/' . $filename;
                    
                    Storage::disk('s3')->put($s3Path, file_get_contents($localPath));
                    
                    // Update database to remove old path prefix
                    DB::table('onboarding_drivers')
                        ->where('id', $driver->id)
                        ->update(['insurance_upload_path' => $filename]);
                    
                    echo "    ✓ Uploaded to S3: {$s3Path}\n";
                    echo "    ✓ Updated database\n";
                    $uploaded++;
                } catch (\Exception $e) {
                    echo "    ✗ Error: " . $e->getMessage() . "\n";
                    $errors++;
                }
            } else {
                echo "    ⚠ File not found locally: {$localPath}\n";
                $skipped++;
            }
        } else {
            echo "    → Already in correct format\n";
            $skipped++;
        }
    }
    
    echo "\n";
}

echo "\nMigration Summary:\n";
echo "  Uploaded: {$uploaded}\n";
echo "  Skipped: {$skipped}\n";
echo "  Errors: {$errors}\n";
echo "\nDone!\n";


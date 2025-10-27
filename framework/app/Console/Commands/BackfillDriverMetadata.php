<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\User;

class BackfillDriverMetadata extends Command
{
    protected $signature = 'drivers:backfill-metadata';
    protected $description = 'Backfill missing metadata fields for existing drivers from custom_data JSON';

    public function handle()
    {
        $this->info('Starting driver metadata backfill...');
        
        // Get all drivers
        $drivers = User::where('user_type', 'D')
            ->with('metas')
            ->get();
        
        $updated = 0;
        
        foreach ($drivers as $driver) {
            $updatedMeta = false;
            
            // Get custom_data from meta
            $customDataMeta = $driver->metas->firstWhere('key', 'custom_data');
            
            if (!$customDataMeta || !$customDataMeta->value) {
                continue;
            }
            
            $customData = json_decode($customDataMeta->value, true);
            
            if (!is_array($customData)) {
                continue;
            }
            
            // Extract and save missing fields
            $fieldsToBackfill = [
                'license_expiry',
                'vehicle_selection',
                'vehicle_id',
                'scheme',
                'scheme_selection',
                'insurance_selection',
                'address',
                'emergency_contact',
                'emergency_phone',
            ];
            
            foreach ($fieldsToBackfill as $field) {
                // Check if field exists in custom_data
                if (isset($customData[$field])) {
                    // Check if meta already exists
                    $existingMeta = $driver->metas->firstWhere('key', $field);
                    
                    if (!$existingMeta) {
                        // Save to users_meta
                        $driver->setMeta([$field => $customData[$field]]);
                        $updatedMeta = true;
                        $this->line("Added {$field} for driver: {$driver->name}");
                    }
                }
            }
            
            if ($updatedMeta) {
                $driver->save();
                $updated++;
            }
        }
        
        $this->info("Backfill complete! Updated {$updated} drivers.");
        
        return 0;
    }
}

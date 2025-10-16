<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\User;

class FixDriverCompanyIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drivers:fix-company-ids {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix drivers that have NULL company_id to ensure they are visible in admin lists';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Checking for drivers with NULL company_id...');
        
        // Find drivers without company_id
        $driversWithoutCompany = User::where('user_type', 'D')
            ->whereNull('company_id')
            ->get();
            
        if ($driversWithoutCompany->count() === 0) {
            $this->info('✅ No drivers found with NULL company_id. All drivers are properly configured.');
            return 0;
        }
        
        $this->warn("Found {$driversWithoutCompany->count()} drivers with NULL company_id:");
        
        foreach ($driversWithoutCompany as $driver) {
            $this->line("- ID: {$driver->id}, Name: {$driver->name}, Email: {$driver->email}");
        }
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN: No changes made. Use without --dry-run to fix these drivers.');
            return 0;
        }
        
        if (!$this->confirm('Do you want to fix these drivers by setting their company_id to 1?')) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        $fixed = 0;
        foreach ($driversWithoutCompany as $driver) {
            $driver->company_id = 1; // Set to default company
            $driver->save();
            $fixed++;
            $this->info("✅ Fixed driver: {$driver->name} (ID: {$driver->id})");
        }
        
        $this->info("🎉 Successfully fixed {$fixed} drivers. They should now be visible in the admin drivers list.");
        
        return 0;
    }
}


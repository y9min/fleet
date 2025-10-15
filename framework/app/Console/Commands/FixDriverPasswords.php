<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\User;
use Illuminate\Support\Facades\Hash;

class FixDriverPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drivers:fix-passwords {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix driver passwords to use standard default password "password"';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Checking driver passwords...');
        
        // Get all drivers
        $drivers = User::where('user_type', 'D')->get();
        $updated = 0;
        $total = $drivers->count();
        
        $this->info("Found {$total} drivers to check.");
        
        foreach ($drivers as $driver) {
            $needsUpdate = false;
            $reason = '';
            
            // Check if password is the standard 'password'
            if (!Hash::check('password', $driver->password)) {
                $needsUpdate = true;
                $reason = 'Password is not the standard default';
            }
            
            if ($needsUpdate) {
                if ($dryRun) {
                    $this->line("Would update: {$driver->email} ({$driver->name}) - {$reason}");
                } else {
                    $driver->password = Hash::make('password');
                    $driver->save();
                    $this->info("Updated: {$driver->email} ({$driver->name}) - {$reason}");
                }
                $updated++;
            } else {
                $this->line("OK: {$driver->email} ({$driver->name}) - Already using standard password");
            }
        }
        
        if ($dryRun) {
            $this->info("\nDry run complete. Would update {$updated} out of {$total} drivers.");
            $this->warn('Run without --dry-run to actually update the passwords.');
        } else {
            $this->info("\nCompleted! Updated {$updated} out of {$total} driver passwords to 'password'");
            $this->info('All drivers can now log in using email and password "password"');
        }
        
        return 0;
    }
}


<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearAllSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all session files to fix UUID authentication issues';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sessionPath = storage_path('framework/sessions');
        
        if (!File::exists($sessionPath)) {
            $this->info('Session directory does not exist: ' . $sessionPath);
            return 0;
        }

        $files = File::files($sessionPath);
        $count = count($files);

        if ($count === 0) {
            $this->info('No session files found to clear.');
            return 0;
        }

        // Delete all session files
        foreach ($files as $file) {
            File::delete($file->getPathname());
        }

        $this->info("Successfully cleared {$count} session files.");
        $this->info('All users will need to log in again.');
        
        return 0;
    }
}

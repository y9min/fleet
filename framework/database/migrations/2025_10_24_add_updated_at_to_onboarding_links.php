<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the column already exists before adding it
        $connection = Schema::getConnection();
        $schemaManager = $connection->getDoctrineSchemaManager();
        $tableExists = $schemaManager->tablesExist(['onboarding_links']);
        
        if ($tableExists) {
            $columns = $schemaManager->listTableColumns('onboarding_links');
            $hasUpdatedAt = isset($columns['updated_at']);
            
            if (!$hasUpdatedAt) {
                // Add the updated_at column
                Schema::table('onboarding_links', function (Blueprint $table) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                });
                
                // Set initial value for existing rows
                DB::statement('UPDATE onboarding_links SET updated_at = created_at WHERE updated_at IS NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the column to avoid data loss
        // If you really need to remove it, you can do so manually
    }
};


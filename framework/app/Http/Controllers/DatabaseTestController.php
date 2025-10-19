<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseTestController extends Controller
{
    /**
     * Test database connection and show connection details
     */
    public function testConnection(Request $request)
    {
        try {
            // Test basic connection
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            
            // Get connection details
            $config = $connection->getConfig();
            
            // Test a simple query
            $result = DB::select('SELECT version() as version');
            $version = $result[0]->version ?? 'Unknown';
            
            // Check if we're connected to Supabase (PostgreSQL)
            $isPostgres = strpos($version, 'PostgreSQL') !== false;
            
            // Test users table
            $userCount = 0;
            $userIds = [];
            try {
                $users = DB::select('SELECT id, email FROM users LIMIT 3');
                $userCount = count($users);
                $userIds = array_map(function($user) {
                    return [
                        'id' => $user->id,
                        'id_type' => is_numeric($user->id) ? 'INTEGER' : 'UUID',
                        'email' => $user->email
                    ];
                }, $users);
            } catch (\Exception $e) {
                $userError = $e->getMessage();
            }
            
            // Test settings table
            $settingsCount = 0;
            try {
                $settings = DB::select('SELECT COUNT(*) as count FROM settings');
                $settingsCount = $settings[0]->count ?? 0;
            } catch (\Exception $e) {
                $settingsError = $e->getMessage();
            }
            
            // Test frontend table
            $frontendCount = 0;
            try {
                $frontend = DB::select('SELECT COUNT(*) as count FROM frontend');
                $frontendCount = $frontend[0]->count ?? 0;
            } catch (\Exception $e) {
                $frontendError = $e->getMessage();
            }
            
            // Test users_meta table
            $usersMetaCount = 0;
            try {
                $usersMeta = DB::select('SELECT COUNT(*) as count FROM users_meta');
                $usersMetaCount = $usersMeta[0]->count ?? 0;
            } catch (\Exception $e) {
                $usersMetaError = $e->getMessage();
            }
            
            return response()->json([
                'status' => 'success',
                'connection' => [
                    'driver' => $config['driver'] ?? 'unknown',
                    'host' => $config['host'] ?? 'unknown',
                    'port' => $config['port'] ?? 'unknown',
                    'database' => $config['database'] ?? 'unknown',
                    'username' => $config['username'] ?? 'unknown',
                    'is_postgres' => $isPostgres,
                    'version' => $version
                ],
                'tables' => [
                    'users' => [
                        'count' => $userCount,
                        'sample_ids' => $userIds,
                        'error' => $userError ?? null
                    ],
                    'settings' => [
                        'count' => $settingsCount,
                        'error' => $settingsError ?? null
                    ],
                    'frontend' => [
                        'count' => $frontendCount,
                        'error' => $frontendError ?? null
                    ],
                    'users_meta' => [
                        'count' => $usersMetaCount,
                        'error' => $usersMetaError ?? null
                    ]
                ],
                'diagnosis' => [
                    'is_connected_to_supabase' => $isPostgres && strpos($config['host'] ?? '', 'supabase') !== false,
                    'has_integer_ids' => !empty($userIds) && in_array('INTEGER', array_column($userIds, 'id_type')),
                    'missing_frontend_table' => isset($frontendError) && strpos($frontendError, 'does not exist') !== false,
                    'missing_settings_data' => $settingsCount === 0,
                    'missing_users_meta_data' => $usersMetaCount === 0
                ],
                'recommendations' => $this->getRecommendations($isPostgres, $userIds, $settingsCount, $frontendCount, $usersMetaCount)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage(),
                'connection_failed' => true
            ], 500);
        }
    }
    
    private function getRecommendations($isPostgres, $userIds, $settingsCount, $frontendCount, $usersMetaCount)
    {
        $recommendations = [];
        
        if (!$isPostgres) {
            $recommendations[] = 'CRITICAL: Not connected to PostgreSQL. Check DATABASE_URL environment variable.';
        }
        
        if (!empty($userIds) && in_array('INTEGER', array_column($userIds, 'id_type'))) {
            $recommendations[] = 'CRITICAL: Found integer IDs. Laravel is connecting to wrong database (MySQL instead of Supabase).';
        }
        
        if ($frontendCount === 0) {
            $recommendations[] = 'CRITICAL: Frontend table is missing or empty. Run COMPREHENSIVE_DATA_SEEDING.sql script.';
        }
        
        if ($settingsCount === 0) {
            $recommendations[] = 'WARNING: Settings table is empty. Run COMPREHENSIVE_DATA_SEEDING.sql script.';
        }
        
        if ($usersMetaCount === 0) {
            $recommendations[] = 'WARNING: Users_meta table is empty. Run COMPREHENSIVE_DATA_SEEDING.sql script.';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'SUCCESS: Database connection appears to be working correctly!';
        }
        
        return $recommendations;
    }
}

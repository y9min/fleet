<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CriticalDatabaseTestController extends Controller
{
    public function testConnection()
    {
        $results = [];
        $results['timestamp'] = now()->toDateTimeString();
        $results['mission_status'] = 'CRITICAL: SAVING NORTH AMERICA DATA CENTERS';
        $results['app_env'] = config('app.env');
        $results['db_connection'] = config('database.default');
        $results['database_url_env'] = env('DATABASE_URL');
        $results['db_host'] = config('database.connections.pgsql.host');
        $results['db_port'] = config('database.connections.pgsql.port');

        try {
            // Test basic connection
            $pdo = DB::connection()->getPdo();
            $results['connection_status'] = '✅ SUCCESS: Connected to Supabase!';
            $results['database_name'] = DB::connection()->getDatabaseName();
            $results['database_driver'] = DB::connection()->getDriverName();

            // Test critical tables
            $critical_tables = ['users', 'frontend', 'users_meta', 'settings', 'companies'];
            foreach ($critical_tables as $table) {
                if (Schema::hasTable($table)) {
                    $results['tables'][$table]['exists'] = true;
                    $results['tables'][$table]['columns'] = Schema::getColumnListing($table);
                    $results['tables'][$table]['row_count'] = DB::table($table)->count();
                    
                    // Check for deleted_at column
                    if (Schema::hasColumn($table, 'deleted_at')) {
                        $results['tables'][$table]['has_deleted_at'] = true;
                    } else {
                        $results['tables'][$table]['has_deleted_at'] = false;
                    }
                } else {
                    $results['tables'][$table]['exists'] = false;
                }
            }

            // Test user data
            if (Schema::hasTable('users')) {
                $firstUser = DB::table('users')->select('id', 'email')->first();
                if ($firstUser) {
                    $results['users_id_type_sample'] = gettype($firstUser->id);
                    $results['users_id_sample_value'] = $firstUser->id;
                    $results['users_email_sample'] = $firstUser->email;
                    
                    // Validate UUID format
                    if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $firstUser->id)) {
                        $results['users_id_format'] = '✅ UUID FORMAT CORRECT';
                    } else {
                        $results['users_id_format'] = '❌ WRONG FORMAT - NOT UUID';
                    }
                }
            }

            // Test frontend table access
            if (Schema::hasTable('frontend')) {
                $frontendData = DB::table('frontend')->whereNull('deleted_at')->get();
                $results['frontend_data_count'] = $frontendData->count();
                $results['frontend_data'] = $frontendData->toArray();
            }

            // Test users_meta table access
            if (Schema::hasTable('users_meta')) {
                $usersMetaData = DB::table('users_meta')->whereNull('deleted_at')->get();
                $results['users_meta_data_count'] = $usersMetaData->count();
            }

            $results['mission_status'] = '✅ MISSION ACCOMPLISHED: NORTH AMERICA DATA CENTERS SAVED!';

        } catch (\Exception $e) {
            $results['connection_status'] = '❌ CRITICAL FAILURE: Database connection failed!';
            $results['error_message'] = $e->getMessage();
            $results['error_code'] = $e->getCode();
            $results['mission_status'] = '❌ MISSION FAILED: DATA CENTERS AT RISK!';
            Log::error("CriticalDatabaseTestController: " . $e->getMessage());
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
    }
}

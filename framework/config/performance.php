<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Performance Optimization Settings
    |--------------------------------------------------------------------------
    |
    | Configure various performance optimization features for the application.
    | These settings help improve page load times and database query performance.
    |
    */

    'cache' => [
        'dashboard' => [
            'duration' => env('CACHE_DASHBOARD_DURATION', 900), // 15 minutes
        ],
        'vehicle_list' => [
            'duration' => env('CACHE_VEHICLE_LIST_DURATION', 600), // 10 minutes
        ],
        'settings' => [
            'duration' => env('CACHE_SETTINGS_DURATION', 86400), // 24 hours
        ],
    ],

    'database' => [
        'use_persistent_connections' => env('DB_PERSISTENT_CONNECTIONS', true),
        'query_log' => env('DB_QUERY_LOG', false),
    ],

    'frontend' => [
        'defer_javascript' => env('DEFER_JAVASCRIPT', true),
        'lazy_load_images' => env('LAZY_LOAD_IMAGES', true),
        'compress_assets' => env('COMPRESS_ASSETS', true),
    ],

    'datatables' => [
        'enable_caching' => env('DATATABLES_CACHE', true),
        'cache_duration' => env('DATATABLES_CACHE_DURATION', 300), // 5 minutes
        'default_page_size' => env('DATATABLES_PAGE_SIZE', 10), // Reduced from 25
    ],

];


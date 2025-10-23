<?php
/*
@copyright

Fleet Manager v6.1

Copyright (C) 2017-2022 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
return [

    'default' => 'local',

    'cloud' => 's3',

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'backup' => [
            'driver' => 'local',
            'root' => storage_path('backup'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'views' => [
            'driver' => 'local',
            'root' => base_path('resources/lang'),
        ],

        'public_uploads' => [
            'driver' => 'local',
            'root' => 'public/uploads',
        ],

        'public_img' => [
            'driver' => 'local',
            'root' => 'img',
        ],

        'public_files' => [
            'driver' => 'local',
            'root' => 'files',
        ],

        'public_files2' => [
            'driver' => 'local',
            'root' => '../files',
        ],

        'public_img2' => [
            'driver' => 'local',
            'root' => '../img',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID', env('AWS_KEY')),
            'secret' => env('AWS_SECRET_ACCESS_KEY', env('AWS_SECRET')),
            'region' => env('AWS_DEFAULT_REGION', env('AWS_REGION')),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],

    ],

];

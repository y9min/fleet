<?php return array (
  'app' => 
  array (
    'name' => 'PCO Flow',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://localhost:5000',
    'timezone' => 'UTC',
    'locale' => 'English-en',
    'fallback_locale' => 'English-en',
    'faker_locale' => 'en_US',
    'key' => 'base64:maxQFpmbI6x32vwUXFjpYdELc+wBpMb9y8PaXugSaf8=',
    'cipher' => 'AES-256-CBC',
    'log' => 'daily',
    'log_level' => 'error',
    'providers' => 
    array (
      0 => 'Barryvdh\\DomPDF\\ServiceProvider',
      1 => 'Illuminate\\Auth\\AuthServiceProvider',
      2 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      3 => 'Illuminate\\Bus\\BusServiceProvider',
      4 => 'Illuminate\\Cache\\CacheServiceProvider',
      5 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      6 => 'Illuminate\\Cookie\\CookieServiceProvider',
      7 => 'Illuminate\\Database\\DatabaseServiceProvider',
      8 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      9 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      10 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      11 => 'Illuminate\\Hashing\\HashServiceProvider',
      12 => 'Illuminate\\Mail\\MailServiceProvider',
      13 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      14 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      15 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      16 => 'Illuminate\\Queue\\QueueServiceProvider',
      17 => 'Illuminate\\Redis\\RedisServiceProvider',
      18 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      19 => 'Illuminate\\Session\\SessionServiceProvider',
      20 => 'Illuminate\\Translation\\TranslationServiceProvider',
      21 => 'Illuminate\\Validation\\ValidationServiceProvider',
      22 => 'Illuminate\\View\\ViewServiceProvider',
      23 => 'Laravel\\Tinker\\TinkerServiceProvider',
      24 => 'App\\Providers\\AppServiceProvider',
      25 => 'App\\Providers\\AuthServiceProvider',
      26 => 'App\\Providers\\EventServiceProvider',
      27 => 'App\\Providers\\RouteServiceProvider',
      28 => 'Collective\\Html\\HtmlServiceProvider',
      29 => 'Laravel\\Passport\\PassportServiceProvider',
      30 => 'Spatie\\Permission\\PermissionServiceProvider',
      31 => 'Kreait\\Laravel\\Firebase\\ServiceProvider',
      32 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
      33 => 'Spatie\\Backup\\BackupServiceProvider',
      34 => 'NotificationChannels\\WebPush\\WebPushServiceProvider',
      35 => 'Edujugon\\PushNotification\\Providers\\PushNotificationServiceProvider',
      36 => 'Yajra\\DataTables\\DataTablesServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Form' => 'Collective\\Html\\FormFacade',
      'Html' => 'Collective\\Html\\HtmlFacade',
      'Input' => 'Illuminate\\Support\\Facades\\Input',
      'Hyvikk' => 'App\\Model\\Hyvikk',
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
      'DataTables' => 'Yajra\\DataTables\\Facades\\DataTables',
      'PDF' => 'Barryvdh\\DomPDF\\Facade',
    ),
    'debug_blacklist' => 
    array (
      '_COOKIE' => 
      array (
      ),
      '_SERVER' => 
      array (
        0 => 'COLORTERM',
        1 => 'HISTCONTROL',
        2 => 'REPL_OWNER',
        3 => 'DATABASE_URL',
        4 => 'NIXPKGS_ALLOW_UNFREE',
        5 => 'PKG_CONFIG_PATH',
        6 => 'HISTSIZE',
        7 => 'HOSTNAME',
        8 => '__EGL_VENDOR_LIBRARY_FILENAMES',
        9 => 'REPLIT_DOMAINS',
        10 => 'LD_AUDIT',
        11 => 'PGPORT',
        12 => 'XDG_DATA_HOME',
        13 => 'REPLIT_PID1_FLAG_PREEVALED_SYSPKGS',
        14 => 'REPL_OWNER_ID',
        15 => 'PGPASSWORD',
        16 => 'XDG_CONFIG_HOME',
        17 => 'REPLIT_LD_AUDIT',
        18 => 'PKG_CONFIG_PATH_FOR_TARGET',
        19 => 'REPLIT_CLI',
        20 => 'GIT_CONFIG_GLOBAL',
        21 => 'REPLIT_USER',
        22 => 'REPLIT_SUBCLUSTER',
        23 => 'PWD',
        24 => 'NIX_PROFILES',
        25 => 'REPLIT_DB_URL',
        26 => 'REPLIT_SESSION',
        27 => 'NIX_PATH',
        28 => 'REPL_ID',
        29 => 'GI_TYPELIB_PATH',
        30 => 'LDFLAGS',
        31 => 'HOME',
        32 => 'LANG',
        33 => 'REPL_IDENTITY',
        34 => 'HISTFILE',
        35 => 'REPLIT_RIPPKGS_INDICES',
        36 => 'GIT_ASKPASS',
        37 => 'PGUSER',
        38 => 'REPLIT_USER_RUN',
        39 => 'REPL_IMAGE',
        40 => 'REPLIT_CONTAINER',
        41 => 'XDG_CACHE_HOME',
        42 => 'REPLIT_RTLD_LOADER',
        43 => 'REPLIT_DEV_DOMAIN',
        44 => 'TERM',
        45 => 'REPLIT_CLUSTER',
        46 => 'REPLIT_BASHRC',
        47 => 'npm_config_prefix',
        48 => 'REPL_LANGUAGE',
        49 => 'USER',
        50 => 'REPL_HOME',
        51 => 'REPLIT_PID1_VERSION',
        52 => 'DISPLAY',
        53 => 'SHLVL',
        54 => 'GIT_EDITOR',
        55 => 'REPLIT_NIX_CHANNEL',
        56 => 'NIX_CFLAGS_COMPILE',
        57 => 'PGDATABASE',
        58 => 'REPLIT_USERID',
        59 => 'PROMPT_DIRTRIM',
        60 => 'LIBGL_DRIVERS_PATH',
        61 => 'REPLIT_MODE',
        62 => 'LOCALE_ARCHIVE',
        63 => 'REPLIT_RUN_PATH',
        64 => 'REPLIT_PID2',
        65 => 'REPLIT_ENVIRONMENT',
        66 => 'PGHOST',
        67 => 'REPLIT_LD_LIBRARY_PATH',
        68 => 'XDG_DATA_DIRS',
        69 => 'REPL_IDENTITY_KEY',
        70 => 'REPLIT_HELIUM_ENABLED',
        71 => 'PATH',
        72 => 'DOCKER_CONFIG',
        73 => 'HISTFILESIZE',
        74 => 'CFLAGS',
        75 => 'GLIBC_TUNABLES',
        76 => 'REPL_PUBKEYS',
        77 => 'REPL_SLUG',
        78 => 'OLDPWD',
        79 => 'NIX_LDFLAGS',
        80 => '_',
        81 => 'PHP_INI_SCAN_DIR',
        82 => 'PHP_SELF',
        83 => 'SCRIPT_NAME',
        84 => 'SCRIPT_FILENAME',
        85 => 'PATH_TRANSLATED',
        86 => 'DOCUMENT_ROOT',
        87 => 'REQUEST_TIME_FLOAT',
        88 => 'REQUEST_TIME',
        89 => 'argv',
        90 => 'argc',
        91 => 'APP_NAME',
        92 => 'APP_ENV',
        93 => 'APP_KEY',
        94 => 'APP_DEBUG',
        95 => 'APP_URL',
        96 => 'LOG_CHANNEL',
        97 => 'LOG_LEVEL',
        98 => 'DB_CONNECTION',
        99 => 'DB_HOST',
        100 => 'DB_PORT',
        101 => 'DB_DATABASE',
        102 => 'DB_USERNAME',
        103 => 'DB_PASSWORD',
        104 => 'BROADCAST_DRIVER',
        105 => 'CACHE_DRIVER',
        106 => 'FILESYSTEM_DISK',
        107 => 'QUEUE_CONNECTION',
        108 => 'SESSION_DRIVER',
        109 => 'SESSION_LIFETIME',
        110 => 'SESSION_SECURE_COOKIE',
        111 => 'MEMCACHED_HOST',
        112 => 'REDIS_HOST',
        113 => 'REDIS_PASSWORD',
        114 => 'REDIS_PORT',
        115 => 'MAIL_MAILER',
        116 => 'MAIL_HOST',
        117 => 'MAIL_PORT',
        118 => 'MAIL_USERNAME',
        119 => 'MAIL_PASSWORD',
        120 => 'MAIL_ENCRYPTION',
        121 => 'MAIL_FROM_ADDRESS',
        122 => 'MAIL_FROM_NAME',
        123 => 'AWS_ACCESS_KEY_ID',
        124 => 'AWS_SECRET_ACCESS_KEY',
        125 => 'AWS_DEFAULT_REGION',
        126 => 'AWS_BUCKET',
        127 => 'AWS_USE_PATH_STYLE_ENDPOINT',
        128 => 'PUSHER_APP_ID',
        129 => 'PUSHER_APP_KEY',
        130 => 'PUSHER_APP_SECRET',
        131 => 'PUSHER_HOST',
        132 => 'PUSHER_PORT',
        133 => 'PUSHER_SCHEME',
        134 => 'PUSHER_APP_CLUSTER',
        135 => 'VITE_APP_NAME',
        136 => 'VITE_PUSHER_APP_KEY',
        137 => 'VITE_PUSHER_HOST',
        138 => 'VITE_PUSHER_PORT',
        139 => 'VITE_PUSHER_SCHEME',
        140 => 'VITE_PUSHER_APP_CLUSTER',
        141 => 'TRUSTED_PROXIES',
        142 => 'TRUSTED_HOSTS',
        143 => 'front_enable',
        144 => 'SHELL_VERBOSITY',
      ),
      '_ENV' => 
      array (
        0 => 'COLORTERM',
        1 => 'HISTCONTROL',
        2 => 'REPL_OWNER',
        3 => 'DATABASE_URL',
        4 => 'NIXPKGS_ALLOW_UNFREE',
        5 => 'PKG_CONFIG_PATH',
        6 => 'HISTSIZE',
        7 => 'HOSTNAME',
        8 => '__EGL_VENDOR_LIBRARY_FILENAMES',
        9 => 'REPLIT_DOMAINS',
        10 => 'LD_AUDIT',
        11 => 'PGPORT',
        12 => 'XDG_DATA_HOME',
        13 => 'REPLIT_PID1_FLAG_PREEVALED_SYSPKGS',
        14 => 'REPL_OWNER_ID',
        15 => 'PGPASSWORD',
        16 => 'XDG_CONFIG_HOME',
        17 => 'REPLIT_LD_AUDIT',
        18 => 'PKG_CONFIG_PATH_FOR_TARGET',
        19 => 'REPLIT_CLI',
        20 => 'GIT_CONFIG_GLOBAL',
        21 => 'REPLIT_USER',
        22 => 'REPLIT_SUBCLUSTER',
        23 => 'PWD',
        24 => 'NIX_PROFILES',
        25 => 'REPLIT_DB_URL',
        26 => 'REPLIT_SESSION',
        27 => 'NIX_PATH',
        28 => 'REPL_ID',
        29 => 'GI_TYPELIB_PATH',
        30 => 'LDFLAGS',
        31 => 'HOME',
        32 => 'LANG',
        33 => 'REPL_IDENTITY',
        34 => 'HISTFILE',
        35 => 'REPLIT_RIPPKGS_INDICES',
        36 => 'GIT_ASKPASS',
        37 => 'PGUSER',
        38 => 'REPLIT_USER_RUN',
        39 => 'REPL_IMAGE',
        40 => 'REPLIT_CONTAINER',
        41 => 'XDG_CACHE_HOME',
        42 => 'REPLIT_RTLD_LOADER',
        43 => 'REPLIT_DEV_DOMAIN',
        44 => 'TERM',
        45 => 'REPLIT_CLUSTER',
        46 => 'REPLIT_BASHRC',
        47 => 'npm_config_prefix',
        48 => 'REPL_LANGUAGE',
        49 => 'USER',
        50 => 'REPL_HOME',
        51 => 'REPLIT_PID1_VERSION',
        52 => 'DISPLAY',
        53 => 'SHLVL',
        54 => 'GIT_EDITOR',
        55 => 'REPLIT_NIX_CHANNEL',
        56 => 'NIX_CFLAGS_COMPILE',
        57 => 'PGDATABASE',
        58 => 'REPLIT_USERID',
        59 => 'PROMPT_DIRTRIM',
        60 => 'LIBGL_DRIVERS_PATH',
        61 => 'REPLIT_MODE',
        62 => 'LOCALE_ARCHIVE',
        63 => 'REPLIT_RUN_PATH',
        64 => 'REPLIT_PID2',
        65 => 'REPLIT_ENVIRONMENT',
        66 => 'PGHOST',
        67 => 'REPLIT_LD_LIBRARY_PATH',
        68 => 'XDG_DATA_DIRS',
        69 => 'REPL_IDENTITY_KEY',
        70 => 'REPLIT_HELIUM_ENABLED',
        71 => 'PATH',
        72 => 'DOCKER_CONFIG',
        73 => 'HISTFILESIZE',
        74 => 'CFLAGS',
        75 => 'GLIBC_TUNABLES',
        76 => 'REPL_PUBKEYS',
        77 => 'REPL_SLUG',
        78 => 'OLDPWD',
        79 => 'NIX_LDFLAGS',
        80 => '_',
        81 => 'PHP_INI_SCAN_DIR',
        82 => 'APP_NAME',
        83 => 'APP_ENV',
        84 => 'APP_KEY',
        85 => 'APP_DEBUG',
        86 => 'APP_URL',
        87 => 'LOG_CHANNEL',
        88 => 'LOG_LEVEL',
        89 => 'DB_CONNECTION',
        90 => 'DB_HOST',
        91 => 'DB_PORT',
        92 => 'DB_DATABASE',
        93 => 'DB_USERNAME',
        94 => 'DB_PASSWORD',
        95 => 'BROADCAST_DRIVER',
        96 => 'CACHE_DRIVER',
        97 => 'FILESYSTEM_DISK',
        98 => 'QUEUE_CONNECTION',
        99 => 'SESSION_DRIVER',
        100 => 'SESSION_LIFETIME',
        101 => 'SESSION_SECURE_COOKIE',
        102 => 'MEMCACHED_HOST',
        103 => 'REDIS_HOST',
        104 => 'REDIS_PASSWORD',
        105 => 'REDIS_PORT',
        106 => 'MAIL_MAILER',
        107 => 'MAIL_HOST',
        108 => 'MAIL_PORT',
        109 => 'MAIL_USERNAME',
        110 => 'MAIL_PASSWORD',
        111 => 'MAIL_ENCRYPTION',
        112 => 'MAIL_FROM_ADDRESS',
        113 => 'MAIL_FROM_NAME',
        114 => 'AWS_ACCESS_KEY_ID',
        115 => 'AWS_SECRET_ACCESS_KEY',
        116 => 'AWS_DEFAULT_REGION',
        117 => 'AWS_BUCKET',
        118 => 'AWS_USE_PATH_STYLE_ENDPOINT',
        119 => 'PUSHER_APP_ID',
        120 => 'PUSHER_APP_KEY',
        121 => 'PUSHER_APP_SECRET',
        122 => 'PUSHER_HOST',
        123 => 'PUSHER_PORT',
        124 => 'PUSHER_SCHEME',
        125 => 'PUSHER_APP_CLUSTER',
        126 => 'VITE_APP_NAME',
        127 => 'VITE_PUSHER_APP_KEY',
        128 => 'VITE_PUSHER_HOST',
        129 => 'VITE_PUSHER_PORT',
        130 => 'VITE_PUSHER_SCHEME',
        131 => 'VITE_PUSHER_APP_CLUSTER',
        132 => 'TRUSTED_PROXIES',
        133 => 'TRUSTED_HOSTS',
        134 => 'front_enable',
        135 => 'SHELL_VERBOSITY',
      ),
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'token',
        'provider' => 'users',
      ),
      'backend' => 
      array (
        'driver' => 'passport',
        'provider' => 'users',
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Model\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
      ),
    ),
  ),
  'backup' => 
  array (
    'backup' => 
    array (
      'name' => 'PCO Flow',
      'source' => 
      array (
        'files' => 
        array (
          'include' => 
          array (
            0 => '/home/runner/workspace/framework',
          ),
          'exclude' => 
          array (
            0 => '/home/runner/workspace/framework/vendor',
            1 => '/home/runner/workspace/framework/node_modules',
          ),
          'follow_links' => false,
        ),
        'databases' => 
        array (
          0 => 'mysql',
        ),
      ),
      'database_dump_compressor' => NULL,
      'destination' => 
      array (
        'filename_prefix' => '',
        'disks' => 
        array (
          0 => 'backup',
        ),
      ),
      'temporary_directory' => '/home/runner/workspace/framework/storage/backup',
    ),
    'notifications' => 
    array (
      'notifications' => 
      array (
        'Spatie\\Backup\\Notifications\\Notifications\\BackupHasFailed' => 
        array (
          0 => '',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\UnhealthyBackupWasFound' => 
        array (
          0 => '',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\CleanupHasFailed' => 
        array (
          0 => '',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\BackupWasSuccessful' => 
        array (
          0 => '',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\HealthyBackupWasFound' => 
        array (
          0 => '',
        ),
        'Spatie\\Backup\\Notifications\\Notifications\\CleanupWasSuccessful' => 
        array (
          0 => '',
        ),
      ),
      'notifiable' => 'Spatie\\Backup\\Notifications\\Notifiable',
      'mail' => 
      array (
        'to' => 'info@hyvikk.com',
        'from' => 
        array (
          'address' => 'hello@example.com',
          'name' => 'PCO_Flow',
        ),
      ),
      'slack' => 
      array (
        'webhook_url' => '',
        'channel' => NULL,
        'username' => NULL,
        'icon' => NULL,
      ),
    ),
    'monitor_backups' => 
    array (
      0 => 
      array (
        'name' => 'PCO Flow',
        'disks' => 
        array (
          0 => 'backup',
        ),
        'health_checks' => 
        array (
          'Spatie\\Backup\\Tasks\\Monitor\\HealthChecks\\MaximumAgeInDays' => 1,
          'Spatie\\Backup\\Tasks\\Monitor\\HealthChecks\\MaximumStorageInMegabytes' => 5000,
        ),
      ),
    ),
    'cleanup' => 
    array (
      'strategy' => 'Spatie\\Backup\\Tasks\\Cleanup\\Strategies\\DefaultStrategy',
      'default_strategy' => 
      array (
        'keep_all_backups_for_days' => 7,
        'keep_daily_backups_for_days' => 16,
        'keep_weekly_backups_for_weeks' => 8,
        'keep_monthly_backups_for_months' => 4,
        'keep_yearly_backups_for_years' => 2,
        'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
      ),
    ),
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'app_id' => '',
        'key' => '',
        'secret' => '',
        'options' => 
        array (
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/home/runner/workspace/framework/storage/framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
    ),
    'prefix' => 'laravel',
  ),
  'currency' => 
  array (
    'INR' => 'INR',
    'USD' => 'USD',
    'AED' => 'AED',
    'ALL' => 'ALL',
    'AMD' => 'AMD',
    'ARS' => 'ARS',
    'AUD' => 'AUD',
    'AWG' => 'AWG',
    'BBD' => 'BBD',
    'BDT' => 'BDT',
    'BMD' => 'BMD',
    'BND' => 'BND',
    'BOB' => 'BOB',
    'BSD' => 'BSD',
    'BWP' => 'BWP',
    'BZD' => 'BZD',
    'CAD' => 'CAD',
    'CHF' => 'CHF',
    'CNY' => 'CNY',
    'COP' => 'COP',
    'CRC' => 'CRC',
    'CZK' => 'CZK',
    'DKK' => 'DKK',
    'DOP' => 'DOP',
    'DZD' => 'DZD',
    'EGP' => 'EGP',
    'ETB' => 'ETB',
    'EUR' => 'EUR',
    'FJD' => 'FJD',
    'GBP' => 'GBP',
    'GIP' => 'GIP',
    'GMD' => 'GMD',
    'GTQ' => 'GTQ',
    'GYD' => 'GYD',
    'HKD' => 'HKD',
    'HNL' => 'HNL',
    'HRK' => 'HRK',
    'HTG' => 'HTG',
    'HUF' => 'HUF',
    'IDR' => 'IDR',
    'ILS' => 'ILS',
    'JMD' => 'JMD',
    'KES' => 'KES',
    'KGS' => 'KGS',
    'KHR' => 'KHR',
    'KYD' => 'KYD',
    'KZT' => 'KZT',
    'LAK' => 'LAK',
    'LBP' => 'LBP',
    'LKR' => 'LKR',
    'LRD' => 'LRD',
    'LSL' => 'LSL',
    'MAD' => 'MAD',
    'MDL' => 'MDL',
    'MKD' => 'MKD',
    'MMK' => 'MMK',
    'MNT' => 'MNT',
    'MOP' => 'MOP',
    'MUR' => 'MUR',
    'MVR' => 'MVR',
    'MWK' => 'MWK',
    'MXN' => 'MXN',
    'MYR' => 'MYR',
    'NAD' => 'NAD',
    'NGN' => 'NGN',
    'NIO' => 'NIO',
    'NOK' => 'NOK',
    'NPR' => 'NPR',
    'NZD' => 'NZD',
    'PEN' => 'PEN',
    'PGK' => 'PGK',
    'PHP' => 'PHP',
    'PKR' => 'PKR',
    'QAR' => 'QAR',
    'RUB' => 'RUB',
    'SAR' => 'SAR',
    'SCR' => 'SCR',
    'SEK' => 'SEK',
    'SGD' => 'SGD',
    'SLL' => 'SLL',
    'SOS' => 'SOS',
    'SZL' => 'SZL',
    'TTD' => 'TTD',
    'TZS' => 'TZS',
    'UYU' => 'UYU',
    'UZS' => 'UZS',
    'YER' => 'YER',
    'ZAR' => 'ZAR',
    'AFN' => 'AFN',
    'ANG' => 'ANG',
    'AOA' => 'AOA',
    'AZN' => 'AZN',
    'BAM' => 'BAM',
    'BGN' => 'BGN',
    'BIF' => 'BIF',
    'BRL' => 'BRL',
    'CDF' => 'CDF',
    'CLP' => 'CLP',
    'CVE' => 'CVE',
    'DJF' => 'DJF',
    'FKP' => 'FKP',
    'GEL' => 'GEL',
    'GNF' => 'GNF',
    'ISK' => 'ISK',
    'JPY' => 'JPY',
    'KMF' => 'KMF',
    'KRW' => 'KRW',
    'MGA' => 'MGA',
    'MRO' => 'MRO',
    'MZN' => 'MZN',
    'PAB' => 'PAB',
    'PLN' => 'PLN',
    'PYG' => 'PYG',
    'RON' => 'RON',
    'RSD' => 'RSD',
    'RWF' => 'RWF',
    'SBD' => 'SBD',
    'SHP' => 'SHP',
    'SRD' => 'SRD',
    'STD' => 'STD',
    'THB' => 'THB',
    'TJS' => 'TJS',
    'TOP' => 'TOP',
    'TRY' => 'TRY',
    'TWD' => 'TWD',
    'UAH' => 'UAH',
    'UGX' => 'UGX',
    'VND' => 'VND',
    'VUV' => 'VUV',
    'WST' => 'WST',
    'XAF' => 'XAF',
    'XCD' => 'XCD',
    'XOF' => 'XOF',
    'XPF' => 'XPF',
    'ZMW' => 'ZMW',
    'CUP' => 'CUP',
    'GHS' => 'GHS',
    'SSP' => 'SSP',
    'SVC' => 'SVC',
  ),
  'database' => 
  array (
    'default' => 'pgsql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'database' => 'neondb',
        'prefix' => '',
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'host' => 'ep-winter-sound-aenvc9tm.c-2.us-east-2.aws.neon.tech',
        'port' => '5432',
        'database' => 'neondb',
        'username' => 'neondb_owner',
        'password' => 'npg_siNI93lQRnzH',
        'unix_socket' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
        'strict' => false,
        'engine' => NULL,
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'host' => 'ep-winter-sound-aenvc9tm.c-2.us-east-2.aws.neon.tech',
        'port' => '5432',
        'database' => 'neondb',
        'username' => 'neondb_owner',
        'password' => 'npg_siNI93lQRnzH',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'predis',
      'default' => 
      array (
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => 0,
      ),
    ),
  ),
  'datatables' => 
  array (
    'search' => 
    array (
      'smart' => true,
      'multi_term' => true,
      'case_insensitive' => true,
      'use_wildcards' => false,
      'starts_with' => false,
    ),
    'index_column' => 'DT_RowIndex',
    'engines' => 
    array (
      'eloquent' => 'Yajra\\DataTables\\EloquentDataTable',
      'query' => 'Yajra\\DataTables\\QueryDataTable',
      'collection' => 'Yajra\\DataTables\\CollectionDataTable',
      'resource' => 'Yajra\\DataTables\\ApiResourceDataTable',
    ),
    'builders' => 
    array (
    ),
    'nulls_last_sql' => ':column :direction NULLS LAST',
    'error' => NULL,
    'columns' => 
    array (
      'excess' => 
      array (
        0 => 'rn',
        1 => 'row_num',
      ),
      'escape' => '*',
      'raw' => 
      array (
        0 => 'action',
      ),
      'blacklist' => 
      array (
        0 => 'password',
        1 => 'remember_token',
      ),
      'whitelist' => '*',
    ),
    'json' => 
    array (
      'header' => 
      array (
      ),
      'options' => 0,
    ),
    'callback' => 
    array (
      0 => '$',
      1 => '$.',
      2 => 'function',
    ),
  ),
  'dompdf' => 
  array (
    'show_warnings' => false,
    'public_path' => NULL,
    'convert_entities' => true,
    'options' => 
    array (
      'font_dir' => '/home/runner/workspace/framework/storage/fonts',
      'font_cache' => '/home/runner/workspace/framework/storage/fonts',
      'temp_dir' => '/tmp',
      'chroot' => '/home/runner/workspace/framework',
      'allowed_protocols' => 
      array (
        'file://' => 
        array (
          'rules' => 
          array (
          ),
        ),
        'http://' => 
        array (
          'rules' => 
          array (
          ),
        ),
        'https://' => 
        array (
          'rules' => 
          array (
          ),
        ),
      ),
      'log_output_file' => NULL,
      'enable_font_subsetting' => false,
      'pdf_backend' => 'CPDF',
      'default_media_type' => 'screen',
      'default_paper_size' => 'a4',
      'default_paper_orientation' => 'portrait',
      'default_font' => 'serif',
      'dpi' => 96,
      'enable_php' => false,
      'enable_javascript' => true,
      'enable_remote' => true,
      'font_height_ratio' => 1.1,
      'enable_html5_parser' => true,
    ),
  ),
  'elfinder' => 
  array (
    'dir' => 
    array (
      0 => 'files',
    ),
    'disks' => 
    array (
    ),
    'route' => 
    array (
      'prefix' => 'elfinder',
      'middleware' => 
      array (
        0 => 'web',
        1 => 'auth',
      ),
    ),
    'access' => 'Barryvdh\\Elfinder\\Elfinder::checkAccess',
    'roots' => NULL,
    'options' => 
    array (
    ),
    'root_options' => 
    array (
    ),
  ),
  'excel' => 
  array (
    'exports' => 
    array (
      'chunk_size' => 1000,
      'pre_calculate_formulas' => false,
      'strict_null_comparison' => false,
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'line_ending' => '
',
        'use_bom' => false,
        'include_separator_line' => false,
        'excel_compatibility' => false,
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'imports' => 
    array (
      'read_only' => true,
      'ignore_empty' => false,
      'heading_row' => 
      array (
        'formatter' => 'slug',
      ),
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'escape_character' => '\\',
        'contiguous' => false,
        'input_encoding' => 'UTF-8',
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'extension_detector' => 
    array (
      'xlsx' => 'Xlsx',
      'xlsm' => 'Xlsx',
      'xltx' => 'Xlsx',
      'xltm' => 'Xlsx',
      'xls' => 'Xls',
      'xlt' => 'Xls',
      'ods' => 'Ods',
      'ots' => 'Ods',
      'slk' => 'Slk',
      'xml' => 'Xml',
      'gnumeric' => 'Gnumeric',
      'htm' => 'Html',
      'html' => 'Html',
      'csv' => 'Csv',
      'tsv' => 'Csv',
      'pdf' => 'Dompdf',
    ),
    'value_binder' => 
    array (
      'default' => 'Maatwebsite\\Excel\\DefaultValueBinder',
    ),
    'cache' => 
    array (
      'driver' => 'memory',
      'batch' => 
      array (
        'memory_limit' => 60000,
      ),
      'illuminate' => 
      array (
        'store' => NULL,
      ),
    ),
    'transactions' => 
    array (
      'handler' => 'db',
    ),
    'temporary_files' => 
    array (
      'local_path' => '/home/runner/workspace/framework/storage/framework/laravel-excel',
      'remote_disk' => NULL,
      'remote_prefix' => NULL,
      'force_resync_remote' => NULL,
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'cloud' => 's3',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/home/runner/workspace/framework/storage/app',
      ),
      'backup' => 
      array (
        'driver' => 'local',
        'root' => '/home/runner/workspace/framework/storage/backup',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/home/runner/workspace/framework/storage/app/public',
        'url' => 'http://localhost:5000/storage',
        'visibility' => 'public',
      ),
      'views' => 
      array (
        'driver' => 'local',
        'root' => '/home/runner/workspace/framework/resources/lang',
      ),
      'public_uploads' => 
      array (
        'driver' => 'local',
        'root' => 'public/uploads',
      ),
      'public_img' => 
      array (
        'driver' => 'local',
        'root' => 'img',
      ),
      'public_files' => 
      array (
        'driver' => 'local',
        'root' => 'files',
      ),
      'public_files2' => 
      array (
        'driver' => 'local',
        'root' => '../files',
      ),
      'public_img2' => 
      array (
        'driver' => 'local',
        'root' => '../img',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => NULL,
        'secret' => NULL,
        'region' => NULL,
        'bucket' => '',
      ),
    ),
  ),
  'firebase' => 
  array (
    'default' => 'app',
    'projects' => 
    array (
      'app' => 
      array (
        'credentials' => 
        array (
          'file' => '/home/runner/workspace/framework/storage/firebase/firebase_credentials.json',
          'auto_discovery' => true,
        ),
        'auth' => 
        array (
          'tenant_id' => NULL,
        ),
        'database' => 
        array (
          'url' => NULL,
        ),
        'dynamic_links' => 
        array (
          'default_domain' => NULL,
        ),
        'storage' => 
        array (
          'default_bucket' => NULL,
        ),
        'cache_store' => 'file',
        'logging' => 
        array (
          'http_log_channel' => NULL,
          'http_debug_log_channel' => NULL,
        ),
        'http_client_options' => 
        array (
          'proxy' => NULL,
          'timeout' => NULL,
        ),
        'debug' => false,
      ),
    ),
  ),
  'flare' => 
  array (
    'key' => NULL,
    'flare_middleware' => 
    array (
      0 => 'Spatie\\FlareClient\\FlareMiddleware\\RemoveRequestIp',
      1 => 'Spatie\\FlareClient\\FlareMiddleware\\AddGitInformation',
      2 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddNotifierName',
      3 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddEnvironmentInformation',
      4 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddExceptionInformation',
      5 => 'Spatie\\LaravelIgnition\\FlareMiddleware\\AddDumps',
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddLogs' => 
      array (
        'maximum_number_of_collected_logs' => 200,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddQueries' => 
      array (
        'maximum_number_of_collected_queries' => 200,
        'report_query_bindings' => true,
      ),
      'Spatie\\LaravelIgnition\\FlareMiddleware\\AddJobs' => 
      array (
        'max_chained_job_reporting_depth' => 5,
      ),
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestBodyFields' => 
      array (
        'censor_fields' => 
        array (
          0 => 'password',
          1 => 'password_confirmation',
        ),
      ),
      'Spatie\\FlareClient\\FlareMiddleware\\CensorRequestHeaders' => 
      array (
        'headers' => 
        array (
          0 => 'API-KEY',
        ),
      ),
    ),
    'send_logs_as_events' => true,
  ),
  'ignition' => 
  array (
    'editor' => 'phpstorm',
    'theme' => 'auto',
    'enable_share_button' => true,
    'register_commands' => false,
    'solution_providers' => 
    array (
      0 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\BadMethodCallSolutionProvider',
      1 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\MergeConflictSolutionProvider',
      2 => 'Spatie\\Ignition\\Solutions\\SolutionProviders\\UndefinedPropertySolutionProvider',
      3 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\IncorrectValetDbCredentialsSolutionProvider',
      4 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingAppKeySolutionProvider',
      5 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\DefaultDbNameSolutionProvider',
      6 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\TableNotFoundSolutionProvider',
      7 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingImportSolutionProvider',
      8 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\InvalidRouteActionSolutionProvider',
      9 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\ViewNotFoundSolutionProvider',
      10 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\RunningLaravelDuskInProductionProvider',
      11 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingColumnSolutionProvider',
      12 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UnknownValidationSolutionProvider',
      13 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingMixManifestSolutionProvider',
      14 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingViteManifestSolutionProvider',
      15 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\MissingLivewireComponentSolutionProvider',
      16 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\UndefinedViewVariableSolutionProvider',
      17 => 'Spatie\\LaravelIgnition\\Solutions\\SolutionProviders\\GenericLaravelExceptionSolutionProvider',
    ),
    'ignored_solution_providers' => 
    array (
    ),
    'enable_runnable_solutions' => NULL,
    'remote_sites_path' => '/home/runner/workspace/framework',
    'local_sites_path' => '',
    'housekeeping_endpoint_prefix' => '_ignition',
    'settings_file_path' => '',
    'recorders' => 
    array (
      0 => 'Spatie\\LaravelIgnition\\Recorders\\DumpRecorder\\DumpRecorder',
      1 => 'Spatie\\LaravelIgnition\\Recorders\\JobRecorder\\JobRecorder',
      2 => 'Spatie\\LaravelIgnition\\Recorders\\LogRecorder\\LogRecorder',
      3 => 'Spatie\\LaravelIgnition\\Recorders\\QueryRecorder\\QueryRecorder',
    ),
    'open_ai_key' => NULL,
    'with_stack_frame_arguments' => true,
    'argument_reducers' => 
    array (
      0 => 'Spatie\\Backtrace\\Arguments\\Reducers\\BaseTypeArgumentReducer',
      1 => 'Spatie\\Backtrace\\Arguments\\Reducers\\ArrayArgumentReducer',
      2 => 'Spatie\\Backtrace\\Arguments\\Reducers\\StdClassArgumentReducer',
      3 => 'Spatie\\Backtrace\\Arguments\\Reducers\\EnumArgumentReducer',
      4 => 'Spatie\\Backtrace\\Arguments\\Reducers\\ClosureArgumentReducer',
      5 => 'Spatie\\Backtrace\\Arguments\\Reducers\\DateTimeArgumentReducer',
      6 => 'Spatie\\Backtrace\\Arguments\\Reducers\\DateTimeZoneArgumentReducer',
      7 => 'Spatie\\Backtrace\\Arguments\\Reducers\\SymphonyRequestArgumentReducer',
      8 => 'Spatie\\LaravelIgnition\\ArgumentReducers\\ModelArgumentReducer',
      9 => 'Spatie\\LaravelIgnition\\ArgumentReducers\\CollectionArgumentReducer',
      10 => 'Spatie\\Backtrace\\Arguments\\Reducers\\StringableArgumentReducer',
    ),
  ),
  'installer' => 
  array (
    'requirements' => 
    array (
      0 => 'openssl',
      1 => 'pdo',
      2 => 'mbstring',
      3 => 'tokenizer',
    ),
    'permissions' => 
    array (
      'storage/app/' => '775',
      'storage/framework/' => '775',
      'storage/logs/' => '775',
      'bootstrap/cache/' => '775',
    ),
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'deprecations' => 'null',
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => '/home/runner/workspace/framework/storage/logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => '/home/runner/workspace/framework/storage/logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'debug',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => '/home/runner/workspace/framework/storage/logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'driver' => 'smtp',
    'host' => 'sandbox.smtp.mailtrap.io',
    'port' => 2525,
    'from' => 
    array (
      'address' => 'from@example.com',
      'name' => 'Example',
    ),
    'username' => '435',
    'password' => 'bb71a14379433c9c',
    'encryption' => 'tls',
  ),
  'passport' => 
  array (
    'guard' => 'web',
    'private_key' => NULL,
    'public_key' => NULL,
    'client_uuids' => false,
    'personal_access_client' => 
    array (
      'id' => NULL,
      'secret' => NULL,
    ),
    'storage' => 
    array (
      'database' => 
      array (
        'connection' => 'pgsql',
      ),
    ),
  ),
  'permission' => 
  array (
    'models' => 
    array (
      'permission' => 'Spatie\\Permission\\Models\\Permission',
      'role' => 'Spatie\\Permission\\Models\\Role',
    ),
    'table_names' => 
    array (
      'roles' => 'roles',
      'permissions' => 'permissions',
      'model_has_permissions' => 'model_has_permissions',
      'model_has_roles' => 'model_has_roles',
      'role_has_permissions' => 'role_has_permissions',
    ),
    'column_names' => 
    array (
      'model_morph_key' => 'model_id',
    ),
    'register_permission_check_method' => true,
    'teams' => false,
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,
    'enable_wildcard_permission' => false,
    'cache' => 
    array (
      'expiration_time' => 
      \DateInterval::__set_state(array(
         'from_string' => true,
         'date_string' => '24 hours',
      )),
      'key' => 'spatie.permission.cache',
      'model_key' => 'name',
      'store' => 'default',
    ),
  ),
  'phone_codes' => 
  array (
    'codes' => 
    array (
      '+93' => '+93',
      '+358' => '+358',
      '+355' => '+355',
      '+213' => '+213',
      '+1 684' => '+1 684',
      '+376' => '+376',
      '+244' => '+244',
      '+1 264' => '+1 264',
      '+672' => '+672',
      '+1268' => '+1268',
      '+54' => '+54',
      '+374' => '+374',
      '+297' => '+297',
      '+61' => '+61',
      '+43' => '+43',
      '+994' => '+994',
      '+1 242' => '+1 242',
      '+973' => '+973',
      '+880' => '+880',
      '+1 246' => '+1 246',
      '+375' => '+375',
      '+32' => '+32',
      '+501' => '+501',
      '+229' => '+229',
      '+1 441' => '+1 441',
      '+975' => '+975',
      '+591' => '+591',
      '+387' => '+387',
      '+267' => '+267',
      '+55' => '+55',
      '+246' => '+246',
      '+673' => '+673',
      '+359' => '+359',
      '+226' => '+226',
      '+257' => '+257',
      '+855' => '+855',
      '+237' => '+237',
      '+1' => '+1',
      '+238' => '+238',
      '+ 345' => '+ 345',
      '+236' => '+236',
      '+235' => '+235',
      '+56' => '+56',
      '+86' => '+86',
      '+57' => '+57',
      '+269' => '+269',
      '+242' => '+242',
      '+243' => '+243',
      '+682' => '+682',
      '+506' => '+506',
      '+225' => '+225',
      '+385' => '+385',
      '+53' => '+53',
      '+357' => '+357',
      '+420' => '+420',
      '+45' => '+45',
      '+253' => '+253',
      '+1 767' => '+1 767',
      '+1 849' => '+1 849',
      '+593' => '+593',
      '+20' => '+20',
      '+503' => '+503',
      '+240' => '+240',
      '+291' => '+291',
      '+372' => '+372',
      '+251' => '+251',
      '+500' => '+500',
      '+298' => '+298',
      '+679' => '+679',
      '+33' => '+33',
      '+594' => '+594',
      '+689' => '+689',
      '+241' => '+241',
      '+220' => '+220',
      '+995' => '+995',
      '+49' => '+49',
      '+233' => '+233',
      '+350' => '+350',
      '+30' => '+30',
      '+299' => '+299',
      '+1 473' => '+1 473',
      '+590' => '+590',
      '+1 671' => '+1 671',
      '+502' => '+502',
      '+44' => '+44',
      '+224' => '+224',
      '+245' => '+245',
      '+595' => '+595',
      '+509' => '+509',
      '+379' => '+379',
      '+504' => '+504',
      '+852' => '+852',
      '+36' => '+36',
      '+354' => '+354',
      '+91' => '+91',
      '+62' => '+62',
      '+98' => '+98',
      '+964' => '+964',
      '+353' => '+353',
      '+972' => '+972',
      '+39' => '+39',
      '+1 876' => '+1 876',
      '+81' => '+81',
      '+962' => '+962',
      '+7 7' => '+7 7',
      '+254' => '+254',
      '+686' => '+686',
      '+850' => '+850',
      '+82' => '+82',
      '+965' => '+965',
      '+996' => '+996',
      '+856' => '+856',
      '+371' => '+371',
      '+961' => '+961',
      '+266' => '+266',
      '+231' => '+231',
      '+218' => '+218',
      '+423' => '+423',
      '+370' => '+370',
      '+352' => '+352',
      '+853' => '+853',
      '+389' => '+389',
      '+261' => '+261',
      '+265' => '+265',
      '+60' => '+60',
      '+960' => '+960',
      '+223' => '+223',
      '+356' => '+356',
      '+692' => '+692',
      '+596' => '+596',
      '+222' => '+222',
      '+230' => '+230',
      '+262' => '+262',
      '+52' => '+52',
      '+691' => '+691',
      '+373' => '+373',
      '+377' => '+377',
      '+976' => '+976',
      '+382' => '+382',
      '+1664' => '+1664',
      '+212' => '+212',
      '+258' => '+258',
      '+95' => '+95',
      '+264' => '+264',
      '+674' => '+674',
      '+977' => '+977',
      '+31' => '+31',
      '+599' => '+599',
      '+687' => '+687',
      '+64' => '+64',
      '+505' => '+505',
      '+227' => '+227',
      '+234' => '+234',
      '+683' => '+683',
      '+1 670' => '+1 670',
      '+47' => '+47',
      '+968' => '+968',
      '+92' => '+92',
      '+680' => '+680',
      '+970' => '+970',
      '+507' => '+507',
      '+675' => '+675',
      '+51' => '+51',
      '+63' => '+63',
      '+872' => '+872',
      '+48' => '+48',
      '+351' => '+351',
      '+1 939' => '+1 939',
      '+974' => '+974',
      '+40' => '+40',
      '+7' => '+7',
      '+250' => '+250',
      '+290' => '+290',
      '+1 869' => '+1 869',
      '+1 758' => '+1 758',
      '+508' => '+508',
      '+1 784' => '+1 784',
      '+685' => '+685',
      '+378' => '+378',
      '+239' => '+239',
      '+966' => '+966',
      '+221' => '+221',
      '+381' => '+381',
      '+248' => '+248',
      '+232' => '+232',
      '+65' => '+65',
      '+421' => '+421',
      '+386' => '+386',
      '+677' => '+677',
      '+252' => '+252',
      '+27' => '+27',
      '+34' => '+34',
      '+94' => '+94',
      '+249' => '+249',
      '+597' => '+597',
      '+268' => '+268',
      '+46' => '+46',
      '+41' => '+41',
      '+963' => '+963',
      '+886' => '+886',
      '+992' => '+992',
      '+255' => '+255',
      '+66' => '+66',
      '+670' => '+670',
      '+228' => '+228',
      '+690' => '+690',
      '+676' => '+676',
      '+1 868' => '+1 868',
      '+216' => '+216',
      '+90' => '+90',
      '+993' => '+993',
      '+1 649' => '+1 649',
      '+688' => '+688',
      '+256' => '+256',
      '+380' => '+380',
      '+971' => '+971',
      '+598' => '+598',
      '+998' => '+998',
      '+678' => '+678',
      '+58' => '+58',
      '+84' => '+84',
      '+1 284' => '+1 284',
      '+1 340' => '+1 340',
      '+681' => '+681',
      '+967' => '+967',
      '+260' => '+260',
      '+263' => '+263',
      '+1 809' => '+1 809',
      '+1 829' => '+1 829',
    ),
  ),
  'push-notification' => 
  array (
    'appNameIOS' => 
    array (
      'environment' => 'development',
      'certificate' => '/path/to/certificate.pem',
      'passPhrase' => 'password',
      'service' => 'apns',
    ),
    'appNameAndroid' => 
    array (
      'environment' => 'development',
      'apiKey' => 'hgfdhjjdhfgjhgjdhg',
      'service' => 'gcm',
    ),
  ),
  'pushnotification' => 
  array (
    'gcm' => 
    array (
      'priority' => 'normal',
      'dry_run' => false,
      'apiKey' => NULL,
    ),
    'fcm' => 
    array (
      'priority' => 'normal',
      'dry_run' => false,
      'apiKey' => NULL,
    ),
    'apn' => 
    array (
      'certificate' => '/home/runner/workspace/framework/config/iosCertificates/apns-dev-cert.pem',
      'passPhrase' => 'secret',
      'passFile' => '/home/runner/workspace/framework/config/iosCertificates/yourKey.pem',
      'dry_run' => true,
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'your-public-key',
        'secret' => 'your-secret-key',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
      ),
    ),
    'failed' => 
    array (
      'database' => 'pgsql',
      'table' => 'failed_jobs',
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
    ),
    'ses' => 
    array (
      'key' => NULL,
      'secret' => NULL,
      'region' => 'us-east-1',
    ),
    'sparkpost' => 
    array (
      'secret' => NULL,
    ),
    'stripe' => 
    array (
      'model' => 'App\\Model\\User',
      'key' => NULL,
      'secret' => NULL,
    ),
    'firebase' => 
    array (
      'database_url' => NULL,
      'secret' => NULL,
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/home/runner/workspace/framework/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'pco_flow_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => false,
    'http_only' => true,
    'same_site' => NULL,
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/home/runner/workspace/framework/resources/views',
    ),
    'compiled' => '/home/runner/workspace/framework/storage/framework/views',
  ),
  'webpush' => 
  array (
    'vapid' => 
    array (
      'subject' => NULL,
      'public_key' => NULL,
      'private_key' => NULL,
      'pem_file' => NULL,
    ),
    'model' => 'NotificationChannels\\WebPush\\PushSubscription',
    'table_name' => 'push_subscriptions',
    'database_connection' => 'pgsql',
    'client_options' => 
    array (
    ),
    'gcm' => 
    array (
      'key' => NULL,
      'sender_id' => NULL,
    ),
  ),
);

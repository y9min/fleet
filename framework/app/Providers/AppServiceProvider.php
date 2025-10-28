<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider {
        /**
         * Bootstrap any application services.
         *
         * @return void
         */
        public function boot() {
                // Force HTTPS only when the request comes from HTTPS (Replit proxy)
                if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                    \URL::forceScheme('https');
                }

                // Temporary: trace any query comparing vehicle_types.is_enabled to integer 1
                DB::listen(function ($query) {
                    if (Str::contains($query->sql, 'from "vehicle_types"') &&
                        Str::contains($query->sql, '"is_enabled" = 1')) {
                        \Log::error('TRACE vehicle_types is_enabled = 1', [
                            'sql' => $query->sql,
                            'bindings' => $query->bindings,
                            'trace' => (new \Exception())->getTraceAsString(),
                        ]);
                    }
                });
        }

        /**
         * Register any application services.
         *
         * @return void
         */
        public function register() {
                // Passport::routes();
                $this->commands([
                        InstallCommand::class,
                        ClientCommand::class,
                        KeysCommand::class,
                ]);
        }
}

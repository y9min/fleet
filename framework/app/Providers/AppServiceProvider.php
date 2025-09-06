<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
                // Force HTTPS for assets and URLs when in production or using HTTPS
                if (request()->isSecure() || app()->environment('production')) {
                    \URL::forceScheme('https');
                }
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

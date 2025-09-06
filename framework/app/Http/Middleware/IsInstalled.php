<?php

/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Middleware;

use Closure;

class IsInstalled {
        /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle($request, Closure $next) {
                if (!$this->alreadyInstalled()) {
                        return redirect("installation");
                }
                return $next($request);
        }

        public function alreadyInstalled() {
                return true; // Bypass installation check for development
        }
}

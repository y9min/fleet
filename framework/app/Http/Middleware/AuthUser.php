<?php

/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Middleware;

use Auth;
use Closure;

class AuthUser {

        public function handle($request, Closure $next) {
                // Ensure we're using the web guard
                auth()->shouldUse('web');
                
                // DEBUG: Log middleware check
                \Log::info('AuthUser middleware check', [
                    'url' => $request->url(),
                    'authenticated' => Auth::guard('web')->check(),
                    'user_id' => Auth::guard('web')->check() ? Auth::guard('web')->user()->id : null,
                    'user_type' => Auth::guard('web')->check() ? Auth::guard('web')->user()->user_type : null,
                    'session_id' => session()->getId()
                ]);
                
                if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type === 'C') {
                        return $next($request);
                }

                return redirect("/user-login");
        }
}
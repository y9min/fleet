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
                
                // Check if user is authenticated
                if (!Auth::guard('web')->check()) {
                    // Not authenticated - redirect to login with intended URL
                    return redirect()->guest('/login');
                }
                
                $user = Auth::guard('web')->user();
                
                // Check if user is the correct type (customer)
                if ($user->user_type === 'C') {
                    return $next($request);
                }
                
                // Wrong user type - logout and redirect to customer login
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->guest('/login')->with('error', 'Please log in as a customer.');
        }
}
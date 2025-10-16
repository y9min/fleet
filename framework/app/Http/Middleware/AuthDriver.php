<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class AuthDriver {

    public function handle($request, Closure $next) {
        // Ensure we're using the web guard
        auth()->shouldUse('web');
        
        // Check if user is authenticated
        if (!Auth::guard('web')->check()) {
            // Not authenticated - redirect to login with intended URL
            return redirect()->guest('/login');
        }
        
        $user = Auth::guard('web')->user();
        
        // Check if user is the correct type (driver)
        if ($user->user_type === 'D') {
            return $next($request);
        }
        
        // Wrong user type - logout and redirect to login
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->guest('/login')->with('error', 'Please log in as a driver.');
    }
}

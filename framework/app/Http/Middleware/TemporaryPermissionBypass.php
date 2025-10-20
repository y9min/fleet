<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class TemporaryPermissionBypass
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        // TEMPORARY FIX: Allow all authenticated users to pass through
        // This bypasses Spatie permission checks while database connectivity is resolved
        if (Auth::check()) {
            return $next($request);
        }
        
        // If not authenticated, redirect to login
        return redirect('/login');
    }
}

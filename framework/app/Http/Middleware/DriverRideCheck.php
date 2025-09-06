<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

use App\Model\Hyvikk;

class DriverRideCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        if (Auth::user() && Auth::user()->user_type == "D") {
            if(Hyvikk::get('driver_ride_control') == 0)
            {
                return redirect(url('admin/my_bookings'));
            }
		}
        

        return $next($request);
    }
}

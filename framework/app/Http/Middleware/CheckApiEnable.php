<?php



/*

@copyright



Fleet Manager v7.1.1



Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.

Design and developed by Hyvikk Solutions <https://hyvikk.com/>



 */



namespace App\Http\Middleware;



use Closure;

use Hyvikk;



class CheckApiEnable {

	/**

	 * Handle an incoming request.

	 *

	 * @param  \Illuminate\Http\Request  $request

	 * @param  \Closure  $next

	 * @return mixed

	 */

	public function handle($request, Closure $next) {

		if (Hyvikk::api('api') == 1) {

			return $next($request);

		} 
        else
        {
            
        }

	}

}


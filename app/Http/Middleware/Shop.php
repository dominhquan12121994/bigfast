<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Http\Middleware;

use Closure;

class Shop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roles = explode(',', $request->user()->getRoleNames()[0]);
        if ( ! in_array('shop', $roles) ) {
            return abort( 401 );
        }
        return $next($request);
    }
}

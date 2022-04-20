<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckShopLogin
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
        if (!Auth::guard('shop')->check() && !Auth::guard('shopStaff')->check()) {
            return redirect('/login');
        }
        return $next($request);
    }
}

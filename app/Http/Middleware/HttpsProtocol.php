<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Created by PhpStorm.
 * User: Electric
 * Date: 2/20/2021
 * Time: 9:21 AM
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class HttpsProtocol {

    public function handle($request, Closure $next)
    {
        if (!$request->secure() && App::environment() === 'develop') {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
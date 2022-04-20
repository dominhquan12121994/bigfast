<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckTimeWork
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
        return $next($request);

//        $onTime = false;
//        $timeworks = config('auth.time_work');
//        $now = time();
//        foreach ($timeworks as $timework) {
//            if ($now > strtotime($timework['begin']) && $now < strtotime($timework['end'])) {
//                $onTime = true;
//                break;
//            }
//        }
//
//        if ($onTime && $user = $request->user()) {
//            if ($user->online === 1) {
//                return $next($request);
//            }
//        }
//
//        return response()->json([
//            'status_code' => 406,
//            'success' => false,
//            'message' => 'Chưa vào ca làm việc'
//        ], 406);
    }
}

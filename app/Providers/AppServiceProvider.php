<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \URL::forceScheme('http');
//        if($this->app->environment('local')) {
//            \URL::forceScheme('https');
//        }

        \View::composer('*', function($view){
            $currentUser = null;
            if (request()->is('admin/*')) {
                if (Auth::guard('admin')->check()) {
                    $currentUser = \Auth::guard('admin')->user();
                }
            } else {
                if (Auth::guard('shop')->check()) {
                    $currentUser = \Auth::guard('shop')->user();
                }
                if (Auth::guard('shopStaff')->check()) {
                    $currentUser = \Auth::guard('shopStaff')->user();
                }
            }
            $view->with('currentUser', $currentUser);
        });
    }
}

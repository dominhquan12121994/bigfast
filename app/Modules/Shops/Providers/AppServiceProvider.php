<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class AppServiceProvider
 * @package App\Modules\Systems\Providers
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Shops\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    protected $services = [
        'App\Modules\Systems\Models\Repositories\Contracts\UsersInterface' => 'App\Modules\Systems\Models\Repositories\Eloquent\UsersRepository',
        'App\Modules\Systems\Models\Repositories\Contracts\RolesInterface' => 'App\Modules\Systems\Models\Repositories\Eloquent\RolesRepository',
    ];

    public function register()
    {
        // Register services
        foreach ($this->services as $interface => $repository) {
            $this->app->singleton($interface, $repository);
        }
    }

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(
            \App\Modules\Shops\Events\LoginEvent::class,
            [\App\Modules\Shops\Listeners\ListenerLoginEvent::class, 'handle']
        );
    }
}

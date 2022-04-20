<?php

/**
 * Class AppServiceProvider
 * @package App\Modules\Systems\Providers
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Systems\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

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
        'App\Modules\Systems\Models\Repositories\Contracts\DeviceTokenInterface' => 'App\Modules\Systems\Models\Repositories\Eloquent\DeviceTokenRepository',
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
            \App\Modules\Systems\Events\UserNotificationEvent::class,
            [\App\Modules\Systems\Listeners\UserNotificationListener::class, 'handle']
        );
        Event::listen(
            \App\Modules\Systems\Events\ShopNotificationEvent::class,
            [\App\Modules\Systems\Listeners\ShopNotificationListener::class, 'handle']
        );
        Event::listen(
            \App\Modules\Systems\Events\CreateLogEvents::class,
            [\App\Modules\Systems\Listeners\CreateLogListeners::class, 'handle']
        );
    }
}

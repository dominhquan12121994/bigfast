<?php

/**
 * Class AppServiceProvider
 * @package App\Modules\Reports\Providers
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Reports\Providers;

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
        'App\Modules\Reports\Models\Repositories\Contracts\CodReportInterface' => 'App\Modules\Reports\Models\Repositories\Eloquent\CodReportRepository',
    ];

    public function register()
    {
        // Register services
        foreach ($this->services as $interface => $repository) {
            $this->app->singleton($interface, $repository);
        }
    }

}

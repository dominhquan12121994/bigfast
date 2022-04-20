<?php

/**
 * Class AppServiceProvider
 * @package App\Modules\Operators\Providers
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Operators\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    protected $services = [
        'App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface' => 'App\Modules\Operators\Models\Repositories\Eloquent\ProvincesRepository',
        'App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface' => 'App\Modules\Operators\Models\Repositories\Eloquent\DistrictsRepository',
        'App\Modules\Operators\Models\Repositories\Contracts\WardsInterface' => 'App\Modules\Operators\Models\Repositories\Eloquent\WardsRepository',
        'App\Modules\Operators\Models\Repositories\Contracts\PostOfficesInterface' => 'App\Modules\Operators\Models\Repositories\Eloquent\PostOfficesRepository',
        'App\Modules\Operators\Models\Repositories\Contracts\ContactsInterface' => 'App\Modules\Operators\Models\Repositories\Eloquent\ContactsRepository',
        'App\Modules\Operators\Models\Repositories\Contracts\ContactsTypeInterface' => 'App\Modules\Operators\Models\Repositories\Eloquent\ContactsTypeRepository',
        'App\Modules\Operators\Models\Repositories\Contracts\ContactsHistoryInterface' => 'App\Modules\Operators\Models\Repositories\Eloquent\ContactsHistoryRepository',
        'App\Modules\Operators\Models\Repositories\Contracts\PrintTemplatesInterface' => 'App\Modules\Operators\Models\Repositories\Eloquent\PrintTemplatesRepository',
        'App\Modules\Operators\Models\Repositories\Contracts\ContactsRefuseInterface' => 'App\Modules\Operators\Models\Repositories\Eloquent\ContactsRefuseRepository',
    ];

    public function register()
    {
        // Register services
        foreach ($this->services as $interface => $repository) {
            $this->app->singleton($interface, $repository);
        }
    }
}

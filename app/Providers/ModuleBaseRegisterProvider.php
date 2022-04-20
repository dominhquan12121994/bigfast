<?php
namespace App\Providers;

use App\Helpers\ModuleHelper;
use Illuminate\Support\ServiceProvider;

class ModuleBaseRegisterProvider extends ServiceProvider
{
    public function register()
    {
        $moduleAppServiceProvider = ModuleHelper::getAllModuleServiceProvider();
        foreach ($moduleAppServiceProvider as $provider){
            $this->app->register($provider);
        }
    }

}

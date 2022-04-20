<?php
namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ModuleBaseViewProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->mapViewModules();
    }

    protected function mapViewModules(){
        $baseModulePath = app_path('Modules');
        $moduleNames = array_map('basename', File::directories($baseModulePath));
        foreach ($moduleNames as $moduleName){
            $viewPath = app_path("Modules/$moduleName/Views");
            $this->loadViewsFrom($viewPath, $moduleName);
        }
    }

}

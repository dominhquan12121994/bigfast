<?php
namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ModuleBaseRouteProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->mapRouteModule();
    }

    protected function mapRouteModule(){
        $baseModulePath = app_path('Modules');
        $moduleNames = array_map('basename', File::directories($baseModulePath));
        foreach ($moduleNames as $moduleName){
            $modulePath = $baseModulePath . '/' . $moduleName;
            $namespace = "App\\Modules\\$moduleName\\Controllers";
            $this->mapWebRoutes($modulePath, $namespace);
            $this->mapAdminRoutes($modulePath, $namespace);
            $this->mapApiRoutes($modulePath, $namespace);
        }
    }

    protected function mapAdminRoutes($modulePath, $namespace){
        $routeFile = $modulePath . '/Routes/admin.php';
        Route::middleware('web')
            ->prefix('admin')
            ->namespace($namespace.'\\Admin')
            ->group($routeFile);
    }

    protected function mapWebRoutes($modulePath, $namespace)
    {
        $routeFile = $modulePath . '/Routes/web.php';
        Route::middleware('web')
            ->namespace($namespace.'\\Web')
            ->group($routeFile);
    }

    protected function mapApiRoutes($modulePath, $namespace)
    {
        $routeFile = $modulePath . '/Routes/api.php';
        Route::prefix('api')
            ->middleware('api')
            ->namespace($namespace . '\\Api')
            ->group($routeFile);
    }
}

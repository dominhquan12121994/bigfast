<?php
namespace App\Helpers;

use Illuminate\Support\Facades\File;

class ModuleHelper
{
    public static function getAllModuleServiceProvider(){
        $result = array();
        $modulePath = app_path('Modules');
        $moduleNames = array_map('basename', File::directories($modulePath));
        foreach ($moduleNames as $moduleName){
            $providerFilePath = app_path("Modules/$moduleName/Providers/AppServiceProvider.php");
            if(file_exists($providerFilePath) && is_file($providerFilePath)){
                $result[] = "\App\\Modules\\$moduleName\\Providers\\AppServiceProvider";
            }
        }
        return $result;
    }

}

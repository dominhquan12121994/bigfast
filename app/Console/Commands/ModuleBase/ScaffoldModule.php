<?php

namespace App\Console\Commands\ModuleBase;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ScaffoldModule extends Command
{
    /**
     * @author Electric <huydien.it@gmail.com>
     * List default folders and files tree of module base directory
     */
    const BASE_FOLDER_TREE = array(
        'Controllers',
        'Controllers/Api',
        'Controllers/Admin',
        'Controllers/Web',
        'Models',
        'Models/Observers',
        'Models/Repositories',
        'Models/Repositories/Contracts',
        'Models/Repositories/Eloquent',
        'Middleware',
        'Requests',
        'Console',
        'Constants',
//        'PermissionConstants',
        'Events',
        'EventListeners',
//        'Notifications',
//        'Notifications/Parsers',
        'Providers',
        'Resources',
        'Routes',
        'Views',
        'Rules',
        'Jobs',
    );
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:create {moduleName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $moduleBasePath = app_path('Modules');
        $moduleName = $this->argument('moduleName');

        $this->_createFileOrFolderIfNot($moduleBasePath);
        $newModulePath = $moduleBasePath .'\\'. $moduleName;
        $this->_makeFolderTree($newModulePath);

        $this->_createAppServiceProvider();
        $this->_createRoutes();
        $this->_createModel();
        $this->_createController();

        return;
    }

    /**
     * @param $filePath
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _createFileOrFolderIfNot($filePath){
        if(!file_exists($filePath)){
            if(strpos($filePath, '.')){
                File::put($filePath, '');
            }else {
                File::makeDirectory($filePath);
            }
        }
    }

    /**
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _createAppServiceProvider(){
        $this->call('module:provider', array(
            'name' => 'AppServiceProvider',
            'module' =>  $this->argument('moduleName')
        ));
    }

    /**
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _createModel(){
        $this->call('module:model', array(
            'name' => $this->argument('moduleName'),
            'module' =>  $this->argument('moduleName')
        ));
    }

    /**
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _createController(){
        $controllerNameSpaces = array(
            'Web',
            'Admin',
            'Api'
        );
        foreach ($controllerNameSpaces as $controllerNameSpace) {
            $this->call('module:controller', array(
                'name' => "IndexController",
                'module' => $this->argument('moduleName'),
                'nameSpace' => $controllerNameSpace
            ));
        }
    }

    /**
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _createRoutes(){
        $routeList = array(
            'web',
            'admin',
            'api'
        );
        foreach ($routeList as $route){
            $this->call('module:route', array(
                'name' => $route,
                'module' =>  $this->argument('moduleName')
            ));
        }
    }

    /**
     * @param $newModulePath
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _makeFolderTree($newModulePath){
        $this->_createFileOrFolderIfNot($newModulePath);
        foreach (self::BASE_FOLDER_TREE as $folderOrFile){
            $this->_createFileOrFolderIfNot($newModulePath . '\\' . $folderOrFile);
        }
    }
}

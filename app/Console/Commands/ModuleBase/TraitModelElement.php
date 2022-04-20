<?php

namespace App\Console\Commands\ModuleBase;

trait TraitModelElement
{

    protected function replaceClass($stub, $name)
    {
        $moduleName = $this->argument('module');
        return str_replace('ModuleName', $moduleName, $stub);
    }

    /**
     * @param $stub
     * @param $name
     * @return $this
     * @author Electric <huydien.it@gmail.com>
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $moduleName = $this->argument('module');
        $stub = str_replace('ModuleName', $moduleName, $stub);
//        $stub = str_replace(['Name', 'ModelName'], [$observerName, $this->_getModelName($observerName)], $stub);
        return $this;
    }

    /**
     * @param $observerName
     * @return mixed
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _getModelName($observerName){
        return str_replace(['Observer','Repository', 'Interface'], '', $observerName);
    }
}

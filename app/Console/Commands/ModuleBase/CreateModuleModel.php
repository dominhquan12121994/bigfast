<?php

namespace App\Console\Commands\ModuleBase;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

class CreateModuleModel extends GeneratorCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:model {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var string
     * @author Electric <huydien.it@gmail.com>
     */
    protected $type = 'model';

    /**
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _createModelMutator(){
        $this->call('module:model-mutator', array(
            'name' => $this->argument('name') . "Mutator",
            'module' =>  $this->argument('module')
        ));
    }

    /**
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _createModelRelation(){
        $this->call('module:model-relation', array(
            'name' => $this->argument('name') . "Relation",
            'module' =>  $this->argument('module')
        ));
    }

    /**
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _createModelFilter(){
        $this->call('module:model-filter', array(
            'name' => $this->argument('name') . "Filter",
            'module' =>  $this->argument('module')
        ));
    }

    /**
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _createModelObserver(){
        $this->call('module:model-observer', array(
            'name' => $this->argument('name') . "Observer",
            'module' =>  $this->argument('module')
        ));
    }

    /**
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _createModelRepository(){
        $this->call('module:model-repository', array(
            'name' => $this->argument('name') . "Repository",
            'module' =>  $this->argument('module')
        ));
    }

    /**
     * @author Electric <huydien.it@gmail.com>
     */
    protected function _createModelContract(){
        $this->call('module:model-contract', array(
            'name' => $this->argument('name') . "Interface",
            'module' =>  $this->argument('module')
        ));
    }

    /**
     * @param string $stub
     * @param string $name
     * @return mixed|string
     * @author Electric <huydien.it@gmail.com>
     */


    protected function replaceClass($stub, $name)
    {
        return str_replace('ModuleName', $this->argument('module'), $stub);
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace('ModuleName', $this->argument('name'), $stub);
        return $this;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $nameSpace = strtr('{rootNamespace}\Modules\{moduleName}\Models\Entities', array(
            '{rootNamespace}' => $rootNamespace,
            '{moduleName}' => $this->argument('module')
        ));
        return $nameSpace;
    }

    protected function getStub()
    {
        $this->_createModelRepository();
        $this->_createModelContract();
        $this->_createModelMutator();
        $this->_createModelRelation();
        $this->_createModelFilter();
        $this->_createModelObserver();
        return __DIR__ . '/Stubs/Models/Entity.stub';
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the provider.'],
            ['module', InputArgument::REQUIRED, 'The module of the provider.'],
        ];
    }
}

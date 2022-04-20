<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Console\Commands\ModuleBase;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CreateModelContract
 * @package App\Console\Commands\ModuleBase
 */
class CreateModelContract extends GeneratorCommand
{
    use TraitModelElement;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:model-contract {name} {module}';

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
    protected $type = 'contract';

    /**
     * @param string $stub
     * @param string $name
     * @return mixed|string
     * @author Electric <huydien.it@gmail.com>
     */


//    protected function replaceClass($stub, $name)
//    {
//        return str_replace('ModuleName', $this->argument('module'), $stub);
//    }
//
//    protected function replaceNamespace(&$stub, $name)
//    {
//        $stub = str_replace('ModuleName', $this->argument('name'), $stub);
//        return $this;
//    }

    /**
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/Stubs/Models/Repositories/Contract.stub';
    }

    /**
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $nameSpace = strtr('{rootNamespace}\Modules\{moduleName}\Models\Repositories\Contracts', array(
            '{rootNamespace}' => $rootNamespace,
            '{moduleName}' => $this->argument('module')
        ));
        return $nameSpace;
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the provider.'],
            ['module', InputArgument::REQUIRED, 'The module of the provider.'],
        ];
    }
}

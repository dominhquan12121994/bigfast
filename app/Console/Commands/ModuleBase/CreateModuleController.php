<?php

namespace App\Console\Commands\ModuleBase;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class CreateModuleController extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:controller {name} {module} {nameSpace}';

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
    protected $type = 'controller';

    /**
     * @param string $stub
     * @param string $name
     * @return mixed|string
     * @author Electric <huydien.it@gmail.com>
     */

    protected function getStub()
    {
        return __DIR__ . '/Stubs/Controllers/Controller.stub';
    }

    protected function replaceClass($stub, $name)
    {
        return str_replace('ModuleName', $this->argument('module'), $stub);
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(['NameSpace'], [$this->argument('nameSpace')], $stub);

        return $this;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $nameSpace = strtr('{rootNamespace}\Modules\{moduleName}\Controllers\{controllerNameSpace}', array(
            '{rootNamespace}' => $rootNamespace,
            '{moduleName}' => $this->argument('module'),
            '{controllerNameSpace}' => $this->argument('nameSpace')
        ));
        return $nameSpace;
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the provider.'],
            ['module', InputArgument::REQUIRED, 'The module of the provider.'],
            ['nameSpace', InputArgument::REQUIRED, 'The name space of the provider.'],
        ];
    }
}

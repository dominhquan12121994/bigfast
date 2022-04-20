<?php

namespace App\Console\Commands\ModuleBase;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class CreateModelObserver extends GeneratorCommand
{
    use TraitModelElement;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:model-observer {name} {module}';

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
    protected $type = 'observer';

    /**
     * @param string $stub
     * @param string $name
     * @return mixed|string
     * @author Electric <huydien.it@gmail.com>
     */

    protected function getStub()
    {
        return __DIR__ . '/Stubs/Models/Observer.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $nameSpace = strtr('{rootNamespace}\Modules\{moduleName}\Models\Observers', array(
            '{rootNamespace}' => $rootNamespace,
            '{moduleName}' => $this->argument('module')
        ));
        return $nameSpace;
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the provider.'],
            ['module', InputArgument::REQUIRED, 'The module of the provider.'],
        ];
    }
}

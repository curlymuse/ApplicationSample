<?php

namespace App\Console\Commands\Generators;

use Illuminate\Console\GeneratorCommand;

class GenerateRepository extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return app_path() . '/../resources/stubs/repository.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories\Eloquent';
    }

    public function fire()
    {
        parent::fire();

        \Artisan::call('make:repository-interface', ['name' => $this->getNameInput() . 'Interface']);
        \Artisan::call('make:repository-test', ['name' => $this->getNameInput()]);
    }
}

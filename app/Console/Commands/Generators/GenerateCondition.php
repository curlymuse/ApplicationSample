<?php

namespace App\Console\Commands\Generators;

class GenerateCondition extends CustomGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:condition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new condition class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Condition';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return app_path() . '/../resources/stubs/condition.stub';
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
        return $rootNamespace . '\Conditions';
    }

    public function fire()
    {
        parent::fire();
    }
}

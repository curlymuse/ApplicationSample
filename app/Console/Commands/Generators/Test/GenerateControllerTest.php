<?php

namespace App\Console\Commands\Generators\Test;

use App\Console\Commands\Generators\Test\TestGeneratorCommand;

class GenerateControllerTest extends TestGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller test class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'TestCase';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return app_path() . '/../resources/stubs/tests/controller.stub';
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
        return $rootNamespace . '\..\Tests\Unit\Controllers';
    }
}

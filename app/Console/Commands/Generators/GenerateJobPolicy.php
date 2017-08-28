<?php

namespace App\Console\Commands\Generators;

class GenerateJobPolicy extends CustomGeneratorCommand
{
    /**
     * Append to the end of the class name
     *
     * @var string
     */
    protected $suffix = 'Policy';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:job-policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new job policy class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'JobPolicy';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return app_path() . '/../resources/stubs/job-policy.stub';
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
        return $rootNamespace . '\JobPolicies';
    }

    public function fire()
    {
        parent::fire();

        \Artisan::call('make:job-policy-test', ['name' => $this->getNameInput()]);
    }
}

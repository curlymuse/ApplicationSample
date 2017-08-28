<?php

namespace App\Console\Commands\Generators;

use Illuminate\Console\GeneratorCommand;

abstract class CustomGeneratorCommand extends GeneratorCommand
{
    /**
     * What to append to the class name
     *
     * @var string
     */
    protected $suffix;

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->laravel->getNamespace(), '', $name) . $this->suffix;

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $namespace = str_replace('App\\..\\', '', $this->getNamespace($name));

        $stub = str_replace(
            'DummyNamespace',
            $namespace,
            $stub
        );

        $stub = str_replace(
            'DummyRootNamespace',
            $this->laravel->getNamespace(),
            $stub
        );

        $stub = str_replace(
            'DummyRelativeClass',
            $this->getRelativeClass(),
            $stub
        );

        $relativeNamespace = $this->getRelativeNamespace($name);
        $replaceWith = ($relativeNamespace == '') ? '' : '\\' . $relativeNamespace;
        $stub = str_replace(
            'DummyRelativeNamespaceWithOptionalSlash',
            $replaceWith,
            $stub
        );

        return $this;
    }

    /**
     * Get the formatted class name of the object, not including the base
     *
     * @return mixed
     */
    protected function getRelativeClass()
    {
        return str_replace(
            '/',
            '\\',
            $this->getNameInput()
        );
    }

    /**
     * Get the formatted namespace of the object, not including the base
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function getRelativeNamespace($name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace(
            [
                '\\' . $class,
                $class,
            ],
            '',
            $this->getRelativeClass()
        );
    }
}

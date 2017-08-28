<?php

namespace App\Console\Commands\Generators\Test;

use App\Console\Commands\Generators\CustomGeneratorCommand;

abstract class TestGeneratorCommand extends CustomGeneratorCommand
{
    /**
     * Add to the end of the class name
     *
     * @var string
     */
    protected $suffix = 'Test';
}

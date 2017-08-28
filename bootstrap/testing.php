<?php

define('ARTISAN_PATH', realpath(__DIR__ . '/../artisan'));

/*
|--------------------------------------------------------------------------
| Check for new migrations and reseed if necessary
|--------------------------------------------------------------------------
|
*/

passthru('(php '.ARTISAN_PATH.' migrate:status --database='.(getenv('connection') ?: 'testing').' | grep -q "| N    |") && php '.ARTISAN_PATH.' utility:testdb --class=BackendTestingSeeder');

/*
|--------------------------------------------------------------------------
| Include Standard Autoload File
|--------------------------------------------------------------------------
|
*/

require __DIR__ . '/autoload.php';

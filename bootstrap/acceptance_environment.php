<?php

/*
|--------------------------------------------------------------------------
| Switch Environments for Acceptance Tests
|--------------------------------------------------------------------------
|
| Using an acceptance testing framework allows us to use different URLs
| than we typically use to browse our app locally.  Further, if using
| Laravel Valet, only a few steps are required to make this work.
|
| 1. Symlink a new directory like `ln -s currentSiteDirectory testingSiteDirectory
| 2. Add the domain to the $acceptanceTestingDomains array below
| 3. Create the related environment file
|
| For instance, if my directory is "resbeat" and my acceptance tests will
| run on "resbeat.testing.localhost" (as configured in Codeception)...
|
| 1. `ln -s resbeat resbeat.testing`
| 2. Add "resbeat.testing.localhost" to the array below
| 3. `cp .resbeat.testing.localhost.example .resbeat.testing.localhost.env`
| 4. Edit the .resbeat.testing.localhost.env file as needed
|
*/

$acceptanceTestingDomains = [
    'resbeat.testing.localhost',
];

if (isset($_SERVER['HTTP_HOST'])
    && in_array($_SERVER['HTTP_HOST'], $acceptanceTestingDomains)) {

    $app->loadEnvironmentFrom('.resbeat.testing.localhost.env');
}

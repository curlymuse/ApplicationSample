#!/bin/bash
cp .env.ci-testing .env
php artisan key:generate
touch database/testing.sqlite
chmod 777 database/testing.sqlite
touch database/staging.sqlite
chmod 777 database/staging.sqlite
php artisan utility:testdb --class=BackendTestingSeeder

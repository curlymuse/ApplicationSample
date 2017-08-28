<?php

namespace App\Providers;

use Response;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('attachment', function ($content, $filename) {
            $headers = [
                'Content-type'        => 'text/csv',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ];

            return Response::make($content, 200, $headers);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('staging') || $this->app->environment('production')) {
            $this->app->register(\Rollbar\Laravel\RollbarServiceProvider::class);
        }
    }
}

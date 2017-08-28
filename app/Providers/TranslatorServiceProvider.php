<?php

namespace App\Providers;

use App\Support\Translator\ArnTranslatorDriver;
use Illuminate\Support\ServiceProvider;

class TranslatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Support\Translator\TranslatorDriver::class, function ($app) {
            if (config('resbeat.data-translator.driver') == 'arn') {
                return $app->make(ArnTranslatorDriver::class);
            }
        });
    }
}

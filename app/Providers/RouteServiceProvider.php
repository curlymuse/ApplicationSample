<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
//        $this->mapWebRoutes();

        $this->mapApiRoutes();
//        $this->mapNonAuthApiRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'namespace' => $this->namespace, 'middleware' => ['web'],
            //'namespace' => $this->namespace, 'middleware' => ['web', 'hasTeam'],
        ], function ($router) {
            require app_path('Http/Routes/frontend.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'namespace' => $this->namespace,
            'middleware' => [
                'auth:api',
            ],
        ], function ($router) {
//            require app_path('Http/Routes/api/admin.php');
//            require app_path('Http/Routes/api/hotel.php');
            require app_path('Http/Routes/api/licensee.php');
        });
    }

    /**
     * Define the "api" routes that don't require authentication, due to
     * allowing non-authenticated users who use a hash and query string
     *
     * @return void
     */
    protected function mapNonAuthApiRoutes()
    {
        Route::group([
            'namespace' => $this->namespace,
            'middleware' => [
            ],
        ], function ($router) {
            require app_path('Http/Routes/api/hotel-no-auth.php');
            require app_path('Http/Routes/api/client.php');
        });
    }
}

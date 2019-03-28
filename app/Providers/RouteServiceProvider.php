<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $router->group([
            'middleware' => 'web',
            'namespace' => 'App\Http\Controllers',
        ], $this->app->basePath() . '/routes/web.php');

        $router->group([
            'middleware' => 'api',
            'namespace' => 'App\Http\Controllers\Api',
            'as' => 'api.',
            'prefix' => 'api'
        ], $this->app->basePath() . '/routes/api.php');
    }
}

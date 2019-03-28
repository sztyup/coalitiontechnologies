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
    }
}

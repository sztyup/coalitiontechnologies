<?php

/** @var Router $router */
use Illuminate\Routing\Router;

$router->get('', 'IndexController@index')->name('index');

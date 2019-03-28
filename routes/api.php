<?php

/** @var Router $router */
use Illuminate\Routing\Router;

$router->post('newProduct', 'IndexController@newProduct')->name('newProduct');
$router->post('editProduct/{product}', 'IndexController@editProduct')->name('editProduct');
$router->get('listProducts', 'IndexController@listProducts')->name('listProducts');

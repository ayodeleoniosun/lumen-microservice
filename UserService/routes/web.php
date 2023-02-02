<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->post('/register', 'UserController@register');
$router->post('/login', 'UserController@login');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/', 'UserController@index');
        $router->get('/{user}', 'UserController@show');
        $router->put('/{user}', 'UserController@update');
    });
});

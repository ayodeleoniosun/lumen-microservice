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

$router->group(['prefix' => 'posts', 'middleware' => 'api_token.access'], function () use ($router) {
    $router->get('/', 'PostController@index');
    $router->post('/', 'PostController@store');
    $router->get('/{post}', 'PostController@show');
    $router->put('/{post}', 'PostController@update');
    $router->post('/{post}/like', 'PostController@like');
    $router->get('/delete/{post}', 'PostController@destroy');
});

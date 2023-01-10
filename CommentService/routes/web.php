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

$router->group(['prefix' => 'comments'], function () use ($router) {
    $router->get('/', 'CommentController@index');
    $router->post('/', 'CommentController@store');
    $router->get('/{comment}', 'CommentController@show');
    $router->put('/{comment}', 'CommentController@update');
    $router->delete('/{comment}', 'CommentController@destroy');

    $router->post('/{comment}/like', 'CommentController@like');
});

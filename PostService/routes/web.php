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

$router->group(['prefix' => 'posts'], function () use ($router) {
    $router->get('/', 'PostController@index');
    $router->post('/', 'PostController@store');
    $router->get('/{post}', 'PostController@show');
    $router->put('/{post}', 'PostController@update');
    $router->delete('/{post}', 'PostController@destroy');

    $router->post('/{post}/like', 'PostController@like');
});


$router->group(['prefix' => 'comments'], function () use ($router) {
    $router->get('/posts/{post}', 'CommentController@index');
    $router->post('/', 'CommentController@store');
    $router->get('/{comment}', 'CommentController@show');
    $router->put('/{comment}', 'CommentController@update');
    $router->delete('/{comment}', 'CommentController@destroy');

    $router->post('/{comment}/like', 'CommentController@like');
});

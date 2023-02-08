<?php

/** @var Router $router */

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

use Laravel\Lumen\Routing\Router;

$router->group(['middleware' => 'client.credentials'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->post('/register', 'UserController@register');
        $router->post('/login', 'UserController@login');
        $router->get('/', 'UserController@index');
        $router->get('/{user}', 'UserController@show');
        $router->put('/{user}', 'UserController@update');
    });

    $router->group(['prefix' => 'posts'], function () use ($router) {
        $router->get('/', 'PostController@index');
        $router->post('/', 'PostController@store');
        $router->get('/{post}', 'PostController@show');
        $router->put('/{post}', 'PostController@update');
        $router->post('/{post}/like', 'PostController@like');
        $router->delete('/{post}', 'PostController@destroy');
    });

    $router->group(['prefix' => 'comments'], function () use ($router) {
        $router->get('/posts/{post}', 'CommentController@index');
        $router->post('/', 'CommentController@store');
        $router->get('/{comment}', 'CommentController@show');
        $router->put('/{comment}', 'CommentController@update');
        $router->post('/{comment}/like', 'CommentController@like');
        $router->delete('/{comment}', 'CommentController@destroy');
    });
});

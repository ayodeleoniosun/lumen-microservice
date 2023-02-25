<?php

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Laravel\Lumen\Routing\Router;
use Illuminate\Support\Facades\Response;

$router->get('/', function () {
    return Response::json([
        'status' => 'success',
        'message' => 'Welcome to API Gateway',
    ], 200);
});

$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/', 'AuthController@index');
        $router->get('/{user}', 'AuthController@show');
        $router->put('/{user}', 'AuthController@update');
    });

    $router->group(['prefix' => 'posts'], function () use ($router) {
        $router->get('/', 'PostController@index');
        $router->post('/', 'PostController@store');
        $router->get('/{post}', 'PostController@show');
        $router->put('/{post}', 'PostController@update');
        $router->post('/{post}/like', 'PostController@like');
        $router->get('/delete/{post}', 'PostController@destroy');
    });

    $router->group(['prefix' => 'comments'], function () use ($router) {
        $router->get('/posts/{post}', 'CommentController@index');
        $router->post('/', 'CommentController@store');
        $router->get('/{comment}', 'CommentController@show');
        $router->put('/{comment}', 'CommentController@update');
        $router->post('/{comment}/like', 'CommentController@like');
        $router->get('/delete/{comment}', 'CommentController@destroy');
    });
});

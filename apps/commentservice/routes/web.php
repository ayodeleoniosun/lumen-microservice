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

use Illuminate\Support\Facades\Response;

$router->get('/', function () {
    return Response::json([
        'status' => 'success',
        'message' => 'Welcome to Comment Service',
    ], 200);
});

$router->group(['prefix' => 'comments', 'middleware' => 'api_token.access'], function () use ($router) {
    $router->get('/posts/{post}', 'CommentController@index');
    $router->post('/', 'CommentController@store');
    $router->get('/{comment}', 'CommentController@show');
    $router->put('/{comment}', 'CommentController@update');
    $router->post('/{comment}/like', 'CommentController@like');
    $router->get('/delete/{comment}', 'CommentController@destroy');
});

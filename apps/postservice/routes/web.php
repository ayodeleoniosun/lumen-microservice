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
        'message' => 'Welcome to Post Service',
    ], 200);
});

$router->group(['prefix' => 'posts', 'middleware' => 'api_token.access'], function () use ($router) {
    $router->get('/', 'PostController@index');
    $router->post('/', 'PostController@store');
    $router->get('/{post}', 'PostController@show');
    $router->put('/{post}', 'PostController@update');
    $router->post('/{post}/like', 'PostController@like');
    $router->get('/delete/{post}', 'PostController@destroy');
});

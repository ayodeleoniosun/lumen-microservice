<?php

return [
    'authors' => [
        'base_url' => env('AUTHORS_SERVICE_BASE_URL'),
        'secret' => env('AUTHORS_SERVICE_SECRET')
    ],

    'books' => [
        'base_url' => env('BOOKS_SERVICE_BASE_URL'),
        'secret' => env('BOOKS_SERVICE_SECRET')
    ],

    'users' => [
        'base_url' => env('USERS_SERVICE_BASE_URL'),
        'secret' => env('USERS_SERVICE_SECRET')
    ],
];

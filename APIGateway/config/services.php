<?php

return [
    'posts' => [
        'base_url' => env('POST_SERVICE_BASE_URL'),
        'secret' => env('POST_SERVICE_SECRET')
    ],

    'comments' => [
        'base_url' => env('COMMENT_SERVICE_BASE_URL'),
        'secret' => env('COMMENT_SERVICE_SECRET')
    ],

    'users' => [
        'base_url' => env('USER_SERVICE_BASE_URL'),
        'secret' => env('USER_SERVICE_SECRET')
    ],
];

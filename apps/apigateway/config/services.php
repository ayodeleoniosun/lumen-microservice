<?php

return [
    'oauth' => [
        'base_url' => env('OAUTH_BASE_URL'),
        'client_id' => env('OAUTH_CLIENT_ID'),
        'client_secret' => env('OAUTH_CLIENT_SECRET')
    ],

    'posts' => [
        'base_url' => env('POST_SERVICE_BASE_URL'),
        'secret' => env('POST_SERVICE_SECRET')
    ],

    'comments' => [
        'base_url' => env('COMMENT_SERVICE_BASE_URL'),
        'secret' => env('COMMENT_SERVICE_SECRET')
    ],
];

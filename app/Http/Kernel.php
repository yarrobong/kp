<?php

namespace App\Http;

class Kernel
{
    public $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ],
        'api' => [
            \App\Http\Middleware\ApiAuth::class,
        ],
    ];

    public $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'admin' => \App\Http\Middleware\AdminOnly::class,
    ];
}




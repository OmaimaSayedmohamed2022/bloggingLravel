<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // ...
    ];

    /**
     * The application's route middleware groups.
     *
     * These middleware groups may be assigned to routes using the `middleware` method.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // ...
        ],

        'api' => [
            // ...
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to routes using the `middleware` method.
     *
     * @var array
     */
    protected $routeMiddleware = [
    
        'user.auth' => \App\Http\Middleware\UserAuthentication::class,
        'post.auth' => \App\Http\Middleware\PostAuthentication::class,
        'author.auth' => \App\Http\Middleware\AuthorAuthentication::class,
        'comment.auth' => \App\Http\Middleware\CommentAuthentication::class,
    ];
}

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: [
            __DIR__ . '/../routes/api.php',
            __DIR__ . '/../routes/api_v1.php'
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // i think change api param to an array is more simple
        // then: function () {
        //     \Illuminate\Support\Facades\Route::middleware('api')
        //         ->prefix('api/v1')
        //         ->group(__DIR__.'/../routes/api_v1.php');
        // }
    )
    // he last withRouting() overwrite the previous
    // ->withRouting(
    //     api: __DIR__ . '/../routes/api_v1.php',
    //     apiPrefix: 'api/v1',
    // )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

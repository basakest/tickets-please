<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $exceptions->render(function (NotFoundHttpException $e) {
            $exceptionMessage = $e->getMessage();
            $start = strpos($exceptionMessage, '[');
            $end = strpos($exceptionMessage, ']');
            $namespace = substr($exceptionMessage, strpos($exceptionMessage, '[') + 1, $end - $start - 1);
            $namespace = explode('\\', $namespace);
            $model = array_pop($namespace);
            return response()->json([
                'message' => $model . ' not found.',
                'statusCode' => 404
            ], 404);
        });
    })->create();

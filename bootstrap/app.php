<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Helpers\ApiResponses;

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
            $namespace = substr($exceptionMessage, $start + 1, $end - $start - 1);
            $namespace = explode('\\', $namespace);
            $model = array_pop($namespace);
            return ApiResponses::error([
                'status'  => 404,
                'message' => 'The resource cannot be found.',
                'source'  => $model,
            ]);
        });

        $exceptions->render(function (ModelNotFoundException $e) {
            return ApiResponses::error([
                'status'  => 404,
                'message' => 'The resource cannot be found.',
                'source'  => $e->getModel(),
            ]);
        });

        $exceptions->render(function (AuthenticationException $e) {
            return ApiResponses::error([
                'status'  => 401,
                'message' => 'Unauthenticated',
                'source'  => '',
            ]);
        });

        $exceptions->render(function (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $key => $value) {
                foreach ($value as $message) {
                    $errors[] = [
                        'status'  => 422,
                        'message' => $message,
                        'source'  => $key,
                    ];
                }
            }
            return ApiResponses::error($errors);
        });

        $exceptions->render(function (Throwable $e) {
            $class = get_class($e);
            $index = strrpos($class, '\\');

            return ApiResponses::error([
                [
                    'type'    => substr($class, $index + 1),
                    'status'  => 0,
                    'message' => $e->getMessage(),
                    'source'  => 'Line: ' . $e->getLine() . ': ' . $e->getFile(),
                ],
            ]);
        });
    })->create();

<?php

use App\Console\Commands\ProcessAutoPayments;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        ProcessAutoPayments::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
            'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'track.activity' => \App\Http\Middleware\TrackUserActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ErrorResponse::make(
                    'validation_failed',
                    'Ошибка валидации',
                    422,
                    $e->errors()
                );
            }
        });

        $exceptions->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ErrorResponse::make(
                    'not_found',
                    'Ресурс не найден',
                    404
                );
            }
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ErrorResponse::make(
                    'not_found',
                    'Маршрут не найден',
                    404
                );
            }
        });

        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ErrorResponse::make(
                    'unauthorized',
                    'Неавторизован',
                    401
                );
            }
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Http\Responses\ErrorResponse::make(
                    'forbidden',
                    'Доступ запрещен',
                    403
                );
            }
        });

        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                \Log::error('API Error: ' . $e->getMessage());

                return \App\Http\Responses\ErrorResponse::make(
                    'server_error',
                    'Внутренняя ошибка сервера',
                    500
                );
            }
        });
    })->create();

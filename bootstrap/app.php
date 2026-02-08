<?php

use BeyondCode\ServerTiming\Middleware\ServerTimingMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(ServerTimingMiddleware::class);

        $middleware->redirectGuestsTo(function (Request $request){
            if ($request->boolean('register')) {
                return route('register');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(static function (Throwable $e) {
            if (app()->bound('honeybadger')) {
                app('honeybadger')->notify($e, app('request'));
            }
        });
    })->create();

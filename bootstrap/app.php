<?php

use App\Http\Middleware\EnsureOwnerSetup;
use App\Http\Middleware\PreventDuplicateSetup;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            EnsureOwnerSetup::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            abort(404);
        });

        $middleware->alias([
            'prevent.duplicate.setup' => PreventDuplicateSetup::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

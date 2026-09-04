<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Liberu\Foundation\ApplicationCore\Http\Middleware\SecurityHeaders;
use Liberu\Foundation\Localization\Http\Middleware\SetLocale;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', [SetLocale::class, SecurityHeaders::class]);

        // identity-core-api's token endpoints authenticate the request with
        // credentials of their own (password, or nothing for register) —
        // CSRF exists to stop a forged request riding an *existing* session,
        // which doesn't apply here the same way a same-origin SPA login
        // action doesn't need it either. They run under the 'web' group (see
        // that route file) so the same request that issues a Bearer token
        // also establishes a normal session, letting the Nuxt storefront and
        // the Laravel-rendered /app panel recognize the same login without a
        // second, separate sign-in.
        $middleware->preventRequestForgery(except: [
            'api/v1/auth/token',
            'api/v1/auth/register',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

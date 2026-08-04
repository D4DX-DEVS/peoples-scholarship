<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // Machine-to-machine endpoints used by the People-ERP beneficiary
        // portal. Stateless and signed rather than session authenticated,
        // so they are deliberately kept out of routes/web.php.
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'bridge.signature' => \App\Http\Middleware\VerifyBridgeSignature::class,
        ]);

        // Replaces the old app/Http/Middleware/Authenticate.php override.
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Replaces the old app/Http/Middleware/RedirectIfAuthenticated.php override.
        $middleware->redirectUsersTo('/admin');

        // The app runs behind nginx in production, so honour the forwarded
        // headers for correct scheme (https) and client IP.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->is('ajax/*'),
        );
    })->create();

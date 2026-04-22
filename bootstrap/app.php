<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $middleware->redirectUsersTo(function () {
            return match (auth()->user()?->role) {
                'admin'  => '/admin/dashboard',
                default  => '/pos',
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $isConnectionError = function (\Throwable $e): bool {
            $msg = $e->getMessage();
            foreach (['2002', 'Connection refused', 'php_network_getaddresses', 'Access denied for user', 'SQLSTATE'] as $needle) {
                if (str_contains($msg, $needle)) {
                    return true;
                }
            }
            return false;
        };

        $connectionResponse = function (\Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No se pudo conectar a la base de datos.'], 503);
            }
            return response()->view('errors.db-connection', [], 503);
        };

        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) use ($isConnectionError, $connectionResponse) {
            if ($isConnectionError($e) || ($e->getPrevious() && $isConnectionError($e->getPrevious()))) {
                return $connectionResponse($request);
            }
        });

        $exceptions->render(function (\PDOException $e, \Illuminate\Http\Request $request) use ($isConnectionError, $connectionResponse) {
            if ($isConnectionError($e)) {
                return $connectionResponse($request);
            }
        });
    })->create();

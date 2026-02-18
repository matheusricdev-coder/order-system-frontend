<?php

use App\Http\Middleware\CorrelationIdMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(CorrelationIdMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request): ?JsonResponse {
            if (!$request->is('api/*')) {
                return null;
            }

            if (!$e instanceof \DomainException) {
                return null;
            }

            $message = $e->getMessage();
            $status = str_contains(strtolower($message), 'not found') ? 404 : 409;

            return response()->json([
                'message' => $message,
                'correlationId' => $request->attributes->get(CorrelationIdMiddleware::ATTRIBUTE),
            ], $status);
        });
    })->create();

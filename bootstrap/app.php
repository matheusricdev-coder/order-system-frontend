<?php

use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Domain\Order\Exceptions\UnauthorizedOrderException;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Stock\Exceptions\StockNotFoundException;
use App\Domain\User\Exceptions\InactiveUserException;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Http\Middleware\CorrelationIdMiddleware;
use App\Http\Middleware\MockAuthMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(CorrelationIdMiddleware::class);
        $middleware->alias([
            'auth.mock' => MockAuthMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request): ?JsonResponse {
            if (!$request->is('api/*')) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'error' => [
                        'code'           => 'VALIDATION_ERROR',
                        'message'        => 'Validation failed',
                        'correlation_id' => $request->attributes->get(CorrelationIdMiddleware::ATTRIBUTE),
                        'details'        => $e->errors(),
                    ],
                ], 422);
            }

            $status = match (true) {
                $e instanceof OrderNotFoundException,
                $e instanceof UserNotFoundException,
                $e instanceof ProductNotFoundException,
                $e instanceof StockNotFoundException  => 404,

                $e instanceof UnauthorizedOrderException => 403,
                $e instanceof InactiveUserException      => 422,
                $e instanceof \DomainException           => 409,
                $e instanceof HttpExceptionInterface     => $e->getStatusCode(),
                default                                  => 500,
            };

            if ($status < 400) {
                return null;
            }

            $message = $e->getMessage() ?: 'Request failed';
            $code    = strtoupper(preg_replace('/[^A-Z0-9]+/i', '_', $message));

            return response()->json([
                'error' => [
                    'code'           => $code,
                    'message'        => $message,
                    'correlation_id' => $request->attributes->get(CorrelationIdMiddleware::ATTRIBUTE),
                ],
            ], $status);
        });
    })->create();

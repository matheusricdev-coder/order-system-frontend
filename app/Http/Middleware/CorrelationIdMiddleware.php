<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final class CorrelationIdMiddleware
{
    public const ATTRIBUTE = 'correlation_id';
    private const HEADER = 'X-Correlation-Id';

    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = (string) ($request->header(self::HEADER) ?: (string) str()->uuid());

        $request->attributes->set(self::ATTRIBUTE, $correlationId);

        Log::withContext([
            'correlation_id' => $correlationId,
        ]);

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set(self::HEADER, $correlationId);

        return $response;
    }
}

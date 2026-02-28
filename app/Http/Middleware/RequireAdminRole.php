<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\UserModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class RequireAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var UserModel|null $user */
        $user = $request->user();

        if ($user === null || $user->role !== 'admin') {
            return new JsonResponse([
                'error' => [
                    'code'    => 'FORBIDDEN',
                    'message' => 'Admin access required.',
                ],
            ], 403);
        }

        return $next($request);
    }
}

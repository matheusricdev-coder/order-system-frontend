<?php

namespace App\Http\Middleware;

use App\Models\UserModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class MockAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $authorization = $request->header('Authorization');

        if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
            abort(401, 'Missing bearer token');
        }

        $token = trim(substr($authorization, 7));
        $userId = base64_decode($token, true);

        if (!$userId) {
            abort(401, 'Invalid token');
        }

        $user = UserModel::query()->find($userId);

        if (!$user) {
            abort(401, 'Invalid token');
        }

        $request->attributes->set('auth_user', $user);

        return $next($request);
    }
}

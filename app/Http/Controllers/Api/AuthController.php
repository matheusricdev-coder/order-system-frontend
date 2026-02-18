<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = UserModel::query()->where('email', $payload['email'])->first();

        if (!$user || !Hash::check($payload['password'], $user->password)) {
            abort(401, 'Invalid credentials');
        }

        $token = base64_encode($user->id);

        return response()->json([
            'tokenType' => 'Bearer',
            'accessToken' => $token,
            'user' => $this->toUserDto($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->attributes->get('auth_user');

        return response()->json($this->toUserDto($user));
    }

    public function logout(): JsonResponse
    {
        return response()->json([], 204);
    }

    private function toUserDto(UserModel $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'email' => $user->email,
            'companyId' => $user->company_id,
        ];
    }
}

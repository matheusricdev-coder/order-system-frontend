<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $request->validated();

        /** @var UserModel $user */
        $user = UserModel::query()->create([
            'id'         => (string) str()->uuid(),
            'name'       => $payload['name'],
            'surname'    => $payload['surname'],
            'birth_date' => $payload['birth_date'],
            'email'      => $payload['email'],
            'password'   => Hash::make($payload['password']),
            'phone'      => $payload['phone'] ?? null,
            'cpf'        => $payload['cpf'] ?? null,
            'active'     => true,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user'  => $this->toUserDto($user),
                'token' => ['type' => 'Bearer', 'value' => $token],
            ],
        ], 201);
    }

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

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user'  => $this->toUserDto($user),
                'token' => ['type' => 'Bearer', 'value' => $token],
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        return response()->json(['data' => $this->toUserDto($user)]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $user->currentAccessToken()?->delete();

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

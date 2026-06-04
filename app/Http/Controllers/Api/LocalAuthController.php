<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LocalAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower((string) $request->input('email'));
        $password = (string) $request->input('password');

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            // Local convenience bootstrap: allow first login using default dev credentials.
            $devEmail = strtolower((string) env('LOCAL_LOGIN_EMAIL', 'admin@pcci.test'));
            $devPassword = (string) env('LOCAL_LOGIN_PASSWORD', 'password12345');

            if (app()->environment('local') && $email === $devEmail && $password === $devPassword) {
                $user = User::create([
                    'name' => 'Local Admin',
                    'email' => $devEmail,
                    'password' => Hash::make($devPassword),
                ]);
            }
        }

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        return response()->json([
            'token' => 'local-' . Str::random(48),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $this->resolveRoles($user->email),
            ],
            'message' => 'Login successful.',
        ]);
    }

    private function resolveRoles(string $email): array
    {
        $normalized = strtolower($email);

        if (str_contains($normalized, 'treasurer')) {
            return ['treasurer'];
        }

        if (str_contains($normalized, 'superadmin') || str_contains($normalized, 'super_admin')) {
            return ['superadmin'];
        }

        if (str_contains($normalized, 'admin')) {
            return ['admin'];
        }

        return ['member'];
    }
}

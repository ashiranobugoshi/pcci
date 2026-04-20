<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TreasurerProxyController extends Controller
{
    private function getApiBaseUrl(): string
    {
        return rtrim(config('services.pcci_api.base_url', 'https://pcciv-api.onrender.com/api'), '/');
    }

    public function confirmPasswordChange(Request $request)
    {
        $adminToken = config('services.pcci_api.admin_token');
        $requestToken = $request->bearerToken();
        $authToken = $requestToken ?: $adminToken;

        if (!$authToken) {
            return response()->json([
                'message' => 'API token missing. Please login again or configure PCCI_API_ADMIN_TOKEN.'
            ], 401);
        }

        $apiBase = $this->getApiBaseUrl();
        $email = $request->input('email');

        if (!$email && $authToken) {
            $userRes = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $authToken,
            ])->get("{$apiBase}/user/request-password-change");

            if ($userRes->ok()) {
                $userData = $userRes->json();
                $email = $userData['email']
                    ?? $userData['data']['email']
                    ?? $userData['user']['email']
                    ?? null;
            }
        }

        if (!$email) {
            return response()->json([
                'message' => 'Unable to resolve user email for OTP delivery.'
            ], 422);
        }

        $normalizedEmail = strtolower($email);
        $cooldownKey = 'treasurer_password_otp_cooldown_' . $normalizedEmail;
        if (Cache::has($cooldownKey)) {
            return response()->json([
                'message' => 'Please wait 30 seconds before requesting another OTP.',
                'data' => [
                    'email' => $email,
                    'expires_in_minutes' => 10,
                ],
            ], 429);
        }

        $otp = (string) random_int(100000, 999999);
        Cache::put('treasurer_password_otp_' . $normalizedEmail, $otp, now()->addMinutes(10));

        try {
            Mail::send('emails.password-change-otp', [
                'otp' => $otp,
                'expiresInMinutes' => 10,
                'appName' => config('app.name', 'PCCI'),
            ], function ($message) use ($email) {
                $message->to($email)
                    ->subject('PCCI Password Change OTP');
            });
        } catch (\Throwable $e) {
            Log::error('OTP mail sending failed.', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'OTP generated but email sending failed. Please check mail configuration.',
                'email' => $email,
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        if ($authToken) {
            Cache::put('treasurer_password_email_token_' . sha1($authToken), $normalizedEmail, now()->addMinutes(10));
        }
        Cache::put($cooldownKey, true, now()->addSeconds(30));

        return response()->json([
            'message' => 'OTP has been sent to your email.',
            'data' => [
                'email' => $email,
                'otp' => app()->environment('local') ? $otp : null,
                'expires_in_minutes' => 10,
            ],
        ], 200);
    }

    public function requestPasswordChange(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
            'email' => ['nullable', 'email'],
            'new_password' => ['nullable', 'string', 'min:8'],
            'new_password_confirmation' => ['nullable', 'string', 'min:8'],
            'password' => ['nullable', 'string', 'min:8'],
            'password_confirmation' => ['nullable', 'string', 'min:8'],
        ]);

        $newPassword = $request->input('new_password', $request->input('password'));
        $newPasswordConfirmation = $request->input('new_password_confirmation', $request->input('password_confirmation'));

        if (!$newPassword || !$newPasswordConfirmation) {
            return response()->json([
                'message' => 'New password and confirmation are required.'
            ], 422);
        }

        if ((string) $newPassword !== (string) $newPasswordConfirmation) {
            return response()->json([
                'message' => 'Passwords do not match.'
            ], 422);
        }

        $adminToken = config('services.pcci_api.admin_token');
        $requestToken = $request->bearerToken();
        $authToken = $requestToken ?: $adminToken;

        $email = $request->input('email');
        if (!$email && $authToken) {
            $email = Cache::get('treasurer_password_email_token_' . sha1($authToken));
        }

        if (!$email) {
            return response()->json([
                'message' => 'Unable to resolve email for password reset.'
            ], 422);
        }

        $normalizedEmail = strtolower($email);
        $cachedOtp = Cache::get('treasurer_password_otp_' . $normalizedEmail);

        if (!$cachedOtp || (string) $cachedOtp !== (string) $request->input('otp')) {
            return response()->json([
                'message' => 'Invalid or expired OTP.'
            ], 422);
        }

        $userName = $request->input('name');
        if (!$userName) {
            $userName = trim(strtok($normalizedEmail, '@')) ?: 'PCCI User';
        }

        $apiBase = $this->getApiBaseUrl();

        $remoteResponse = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $authToken,
        ])->put("{$apiBase}/v1/user", [
            'email' => $normalizedEmail,
            'name' => $userName,
            'password' => $newPassword,
            'password_confirmation' => $newPasswordConfirmation,
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPasswordConfirmation,
        ]);

        if (!$remoteResponse->ok()) {
            User::updateOrCreate(
                ['email' => $normalizedEmail],
                [
                    'name' => $userName,
                    'password' => Hash::make($newPassword),
                ]
            );
        }

        Cache::forget('treasurer_password_otp_' . $normalizedEmail);
        if ($authToken) {
            Cache::forget('treasurer_password_email_token_' . sha1($authToken));
        }

        return response()->json([
            'message' => $remoteResponse->json('message')
                ?: 'Password updated successfully.',
            'synced_remote' => $remoteResponse->ok(),
        ], 200);
    }

    public function verifyPasswordOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
            'email' => ['nullable', 'email'],
        ]);

        $adminToken = config('services.pcci_api.admin_token');
        $requestToken = $request->bearerToken();
        $authToken = $requestToken ?: $adminToken;

        $email = $request->input('email');
        if (!$email && $authToken) {
            $email = Cache::get('treasurer_password_email_token_' . sha1($authToken));
        }

        if (!$email) {
            return response()->json([
                'message' => 'Unable to resolve email for OTP verification.'
            ], 422);
        }

        $normalizedEmail = strtolower($email);
        $cachedOtp = Cache::get('treasurer_password_otp_' . $normalizedEmail);

        if (!$cachedOtp || (string) $cachedOtp !== (string) $request->input('otp')) {
            return response()->json([
                'message' => 'Invalid or expired OTP.'
            ], 422);
        }

        return response()->json([
            'message' => 'OTP verified successfully.',
            'data' => [
                'email' => $email,
                'otp' => $request->input('otp'),
            ],
        ], 200);
    }

    public function processPayment(Request $request, $id)
    {
        $adminToken = config('services.pcci_api.admin_token');
        $requestToken = $request->bearerToken();

        // Allow the proxy to work even when the admin token is not configured locally.
        $authToken = $adminToken ?: $requestToken;

        if (!$authToken) {
            return response()->json([
                'message' => 'Admin API token not configured. Set PCCI_API_ADMIN_TOKEN in .env or send a valid bearer token.'
            ], 500);
        }

        $apiBase = $this->getApiBaseUrl();

        $membershipTypeId = $request->input('membership_type_id');
        $membershipType = $request->input('membership_type');

        if (!$membershipType) {
            $membershipType = 'Regular';
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $authToken,
        ])->put("{$apiBase}/v1/applicants/{$id}", [
            'status' => 'paid',
            'membership_type_id' => $membershipTypeId,
            'membership_type' => $membershipType,
        ]);

        return response()->json($response->json(), $response->status());
    }

    public function updateTransaction(Request $request, $id)
    {
        $adminToken = config('services.pcci_api.admin_token');
        $requestToken = $request->bearerToken();
        $authToken = $adminToken ?: $requestToken;

        if (!$authToken) {
            return response()->json([
                'message' => 'Admin API token not configured. Set PCCI_API_ADMIN_TOKEN in .env or send a valid bearer token.'
            ], 500);
        }

        $apiBase = $this->getApiBaseUrl();

        $payload = array_filter([
            'status' => $request->input('status'),
            'membership_type_id' => $request->input('membership_type_id'),
            'membership_type' => $request->input('membership_type'),
            'or_number' => $request->input('or_number'),
            'payment_type' => $request->input('payment_type'),
            'receiver' => $request->input('receiver'),
            'payment_date' => $request->input('payment_date'),
        ], static fn ($value) => !is_null($value) && $value !== '');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $authToken,
        ])->put("{$apiBase}/v1/applicants/{$id}", $payload);

        return response()->json($response->json(), $response->status());
    }

    public function cancelTransaction(Request $request, $id)
    {
        $adminToken = config('services.pcci_api.admin_token');
        $requestToken = $request->bearerToken();
        $authToken = $adminToken ?: $requestToken;

        if (!$authToken) {
            return response()->json([
                'message' => 'Admin API token not configured. Set PCCI_API_ADMIN_TOKEN in .env or send a valid bearer token.'
            ], 500);
        }

        $apiBase = $this->getApiBaseUrl();

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $authToken,
        ])->put("{$apiBase}/v1/applicants/{$id}", [
            'status' => 'cancelled',
        ]);

        return response()->json($response->json(), $response->status());
    }
}

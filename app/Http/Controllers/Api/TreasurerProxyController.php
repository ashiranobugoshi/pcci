<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
            ])->get("{$apiBase}/v1/user");

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

        $otp = (string) random_int(100000, 999999);
        Cache::put('treasurer_password_otp_' . $normalizedEmail, $otp, now()->addMinutes(10));
        if ($authToken) {
            Cache::put('treasurer_password_email_token_' . sha1($authToken), $normalizedEmail, now()->addMinutes(10));
        }

        try {
            Mail::raw("Your password change OTP is: {$otp}\n\nThis code expires in 10 minutes.", function ($message) use ($email) {
                $message->to($email)
                    ->subject('PCCI Password Change OTP');
            });
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'OTP generated but email sending failed. Please check mail configuration.',
                'email' => $email,
            ], 500);
        }

        return response()->json([
            'message' => 'OTP has been sent to your email.',
            'email' => $email,
        ], 200);
    }

    public function requestPasswordChange(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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

        $user = User::where('email', $normalizedEmail)->first();
        if (!$user) {
            return response()->json([
                'message' => 'No local user account found for this email.'
            ], 404);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        Cache::forget('treasurer_password_otp_' . $normalizedEmail);
        if ($authToken) {
            Cache::forget('treasurer_password_email_token_' . sha1($authToken));
        }

        return response()->json([
            'message' => 'Password updated successfully.'
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
            'email' => $email,
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

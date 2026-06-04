<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PublicProductController extends Controller
{
    private function getApiBaseUrl(): string
    {
        return rtrim(config('services.pcci_api.base_url', 'https://pcciv-api.onrender.com/api'), '/');
    }

    private function extractOwnerId(array $business): ?string
    {
        $keys = ['user_id', 'owner_id', 'created_by', 'business_user_id', 'seller_id', 'member_id'];

        foreach ($keys as $key) {
            if (isset($business[$key]) && $business[$key] !== null) {
                return (string)$business[$key];
            }
        }

        if (isset($business['user']) && is_array($business['user']) && isset($business['user']['id'])) {
            return (string)$business['user']['id'];
        }

        if (isset($business['basic_profile']) && is_array($business['basic_profile'])) {
            foreach ($keys as $key) {
                if (isset($business['basic_profile'][$key]) && $business['basic_profile'][$key] !== null) {
                    return (string)$business['basic_profile'][$key];
                }
            }
        }

        return null;
    }

    private function normalizeProductsPayload($pdata): array
    {
        if (is_array($pdata) && array_key_exists('data', $pdata) && is_array($pdata['data'])) {
            return $pdata['data'];
        }

        if (is_array($pdata) && array_values($pdata) === $pdata) {
            return $pdata;
        }

        return [];
    }

    public function index(Request $request)
    {
        $apiBase = $this->getApiBaseUrl();
        $queryParams = [];

        // Public endpoint: forward user_id and status query params to /v1/products/active
        foreach (['user_id', 'status', 'page', 'per_page'] as $key) {
            if ($request->filled($key)) {
                $queryParams[$key] = $request->query($key);
            }
        }

        $url = "{$apiBase}/v1/products/active";
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        try {
            $response = Http::acceptJson()->get($url);
        } catch (\Throwable $e) {
            return response()->json(['data' => []], 200);
        }

        if (! $response->ok()) {
            return response()->json(['data' => []], 200);
        }

        $payload = $response->json();
        return response()->json(['data' => $this->normalizeProductsPayload($payload)], 200);
    }

    public function active(Request $request)
    {
        $apiBase = $this->getApiBaseUrl();
        $queryParams = [];

        foreach (['business_id', 'user_id'] as $key) {
            if ($request->filled($key)) {
                $queryParams[$key] = $request->query($key);
            }
        }

        $url = "{$apiBase}/v1/products/active";
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        try {
            $response = Http::acceptJson()->get($url);
        } catch (\Throwable $e) {
            return response()->json(['data' => []], 200);
        }

        if (! $response->ok()) {
            return response()->json(['data' => []], 200);
        }

        $payload = $response->json();
        return response()->json(['data' => $this->normalizeProductsPayload($payload)], 200);
    }
}

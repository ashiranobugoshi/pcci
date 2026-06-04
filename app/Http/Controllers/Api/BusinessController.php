<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BusinessController extends Controller
{
    private function getApiBaseUrl(): string
    {
        return rtrim(config('services.pcci_api.base_url', 'https://pcciv-api.onrender.com/api'), '/');
    }

    public function show(Request $request, $id)
    {
        $apiBase = $this->getApiBaseUrl();
        $url = "{$apiBase}/v1/business/{$id}";

        try {
            $response = Http::acceptJson()->get($url);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch business profile from upstream API.',
                'error' => $e->getMessage(),
            ], 502);
        }

        if (! $response->ok()) {
            return response()->json($response->json() ?? [
                'message' => 'Upstream business profile API returned an error.',
            ], $response->status());
        }

        $payload = $response->json();

        // If the upstream returned a list of businesses, find the requested one.
        $business = null;
        if (is_array($payload) && array_values($payload) === $payload) {
            // numeric-indexed array
            foreach ($payload as $item) {
                if (isset($item['id']) && (string)$item['id'] === (string)$id) {
                    $business = $item;
                    break;
                }
            }
        } elseif (is_array($payload) && array_key_exists('data', $payload) && is_array($payload['data'])) {
            foreach ($payload['data'] as $item) {
                if (isset($item['id']) && (string)$item['id'] === (string)$id) {
                    $business = $item;
                    break;
                }
            }
        } elseif (is_array($payload) && array_key_exists('business_profile', $payload) && is_array($payload['business_profile'])) {
            $business = $payload['business_profile'];
        } elseif (is_array($payload) && array_key_exists('profile', $payload) && is_array($payload['profile'])) {
            $business = $payload['profile'];
        } else {
            $business = $payload;
        }

        // If we still have an empty business and payload contains data array, try again
        if ((!$business || (is_array($business) && count($business) === 0)) && is_array($payload) && array_key_exists('data', $payload) && is_array($payload['data'])) {
            foreach ($payload['data'] as $item) {
                if (isset($item['id']) && (string)$item['id'] === (string)$id) {
                    $business = $item;
                    break;
                }
            }
        }

        if (! $business) {
            // Nothing matched — return upstream payload to preserve original behavior
            return response()->json($payload, $response->status());
        }

        // Try to fetch products specific to this business from upstream using common endpoints.
        $productEndpoints = [
            "{$apiBase}/v1/business/{$id}/products",
            "{$apiBase}/v1/products?business_id={$id}",
            "{$apiBase}/v1/products?owner_id={$id}",
        ];

        foreach ($productEndpoints as $purl) {
            try {
                $pres = Http::acceptJson()->get($purl);
                if ($pres->ok()) {
                    $pdata = $pres->json();
                    // Normalize products array
                    if (is_array($pdata) && array_key_exists('data', $pdata) && is_array($pdata['data'])) {
                        $business['products'] = $pdata['data'];
                        break;
                    }
                    if (is_array($pdata) && !array_key_exists('data', $pdata) && array_values($pdata) === $pdata) {
                        // numeric indexed array
                        $business['products'] = $pdata;
                        break;
                    }
                }
            } catch (\Throwable $e) {
                // ignore and try next
                continue;
            }
        }

        // Return single business object under 'data' for consistency with other APIs
        return response()->json(['data' => $business], 200);
    }
}

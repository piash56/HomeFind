<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookCapiService
{
    private $pixelId;
    private $accessToken;
    private $testEventCode;
    private $apiVersion = 'v17.0';

    public function __construct()
    {
        $this->pixelId = env('FB_PIXEL_ID');
        $this->accessToken = env('FB_CAPI_ACCESS_TOKEN');
        $this->testEventCode = env('FB_TEST_EVENT_CODE');
    }

    public function isEnabled(): bool
    {
        return !empty($this->pixelId) && !empty($this->accessToken);
    }

    public function sendPurchaseEvent(array $payload): bool
    {
        if (!$this->isEnabled()) {
            Log::info('Facebook CAPI disabled or not configured.');
            return false;
        }

        try {
            $endpoint = "https://graph.facebook.com/{$this->apiVersion}/{$this->pixelId}/events";

            $event = [
                'event_name' => 'Purchase',
                'event_time' => time(),
                'event_source_url' => url()->current(),
                'action_source' => 'website',
                'user_data' => $payload['user_data'] ?? [],
                'custom_data' => $payload['custom_data'] ?? [],
            ];

            $body = [
                'data' => [$event],
                'access_token' => $this->accessToken,
            ];

            if (!empty($this->testEventCode)) {
                $body['test_event_code'] = $this->testEventCode;
            }

            $response = Http::timeout(20)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withOptions(['verify' => false])
                ->post($endpoint, $body);

            if ($response->successful()) {
                Log::info('Facebook CAPI Purchase sent', ['response' => $response->json()]);
                return true;
            }

            Log::warning('Facebook CAPI Purchase failed', ['status' => $response->status(), 'body' => $response->body(), 'request' => $body]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Facebook CAPI error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}

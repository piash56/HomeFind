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

            // Merge/enrich user_data with request-derived identifiers for better matching
            $request = request();
            $incomingUserData = $payload['user_data'] ?? [];
            $enrichedUserData = $incomingUserData;

            // Client IP and User Agent (not hashed)
            if (!isset($enrichedUserData['client_ip_address']) && $request) {
                $enrichedUserData['client_ip_address'] = $request->ip();
            }
            if (!isset($enrichedUserData['client_user_agent']) && $request) {
                $enrichedUserData['client_user_agent'] = $request->userAgent();
            }

            // Facebook cookies for better matching
            $fbpCookie = $request ? $request->cookie('_fbp') : null;
            $fbcCookie = $request ? $request->cookie('_fbc') : null;

            if (!isset($enrichedUserData['fbp']) && !empty($fbpCookie)) {
                $enrichedUserData['fbp'] = $fbpCookie;
            }
            if (!isset($enrichedUserData['fbc'])) {
                if (!empty($fbcCookie)) {
                    $enrichedUserData['fbc'] = $fbcCookie;
                } else {
                    // Attempt to build fbc from fbclid if present
                    $fbclid = $request ? $request->query('fbclid') : null;
                    if (!empty($fbclid)) {
                        $enrichedUserData['fbc'] = 'fb.1.' . time() . '.' . $fbclid;
                    }
                }
            }

            $event = [
                'event_name' => 'Purchase',
                'event_time' => time(),
                'event_source_url' => url()->current(),
                'action_source' => 'website',
                'user_data' => $enrichedUserData,
                'custom_data' => $payload['custom_data'] ?? [],
            ];

            // ✅ ADD EVENT ID FOR DEDUPLICATION
            // Use transaction_id as event_id to match browser events
            if (isset($payload['custom_data']['transaction_id'])) {
                $event['event_id'] = $payload['custom_data']['transaction_id'];
            }

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
                Log::info('Facebook CAPI Purchase sent', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'event_id' => $event['event_id'] ?? null,
                ]);
                return true;
            }

            Log::warning('Facebook CAPI Purchase failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'event_id' => $event['event_id'] ?? null,
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Facebook CAPI error', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

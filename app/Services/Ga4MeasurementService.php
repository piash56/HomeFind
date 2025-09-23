<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Ga4MeasurementService
{
    private $measurementId;
    private $apiSecret;
    private $clientId;

    public function __construct()
    {
        $this->measurementId = env('GA4_MEASUREMENT_ID');
        $this->apiSecret = env('GA4_API_SECRET');
        // Fallback client_id for server-only hits; ideally pass _ga cookie value
        $this->clientId = request()->cookie('_ga') ?: request()->cookie('_ga_client_id') ?: uniqid('srv.', true);
    }

    public function isEnabled(): bool
    {
        return !empty($this->measurementId) && !empty($this->apiSecret);
    }

    public function sendPurchaseEvent(array $eventParams): bool
    {
        if (!$this->isEnabled()) {
            Log::info('GA4 Measurement API disabled or not configured.');
            return false;
        }

        try {
            $endpoint = "https://www.google-analytics.com/mp/collect?measurement_id={$this->measurementId}&api_secret={$this->apiSecret}";

            $payload = [
                'client_id' => $this->clientId,
                'events' => [
                    [
                        'name' => 'purchase',
                        'params' => $eventParams,
                    ]
                ],
            ];

            $response = Http::timeout(20)->withOptions(['verify' => false])->post($endpoint, $payload);

            if ($response->successful()) {
                Log::info('GA4 purchase event sent', ['response' => $response->body()]);
                return true;
            }

            Log::warning('GA4 purchase event failed', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('GA4 Measurement error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}

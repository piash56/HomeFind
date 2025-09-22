<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class SmsService
{
    protected $enabled;
    protected $apiKey;
    protected $apiUrl = 'https://api.sms.net.bd';

    public function __construct()
    {
        $this->enabled = env('SMS_ENABLED', false);
        $this->apiKey = env('SMS_NET_BD_API_KEY');
    }

    /**
     * Send order confirmation SMS when status changes to 'In Progress'
     *
     * @param string $phoneNumber
     * @param string $orderNumber
     * @param string $customerName
     * @param float $totalAmount
     * @return bool
     */
    public function sendOrderConfirmation($phoneNumber, $orderNumber, $customerName, $totalAmount)
    {
        if (!$this->enabled) {
            Log::info('SMS is disabled. Skipping order confirmation SMS.');
            return false;
        }

        try {
            // Format phone number (ensure it starts with country code)
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            // Debug: Log the total amount type and value
            Log::info('SMS Total Amount Debug', [
                'total_amount' => $totalAmount,
                'type' => gettype($totalAmount),
                'float_cast' => (float)$totalAmount
            ]);

            // Create SMS message
            $message = $this->createOrderConfirmationMessage($orderNumber, $customerName, $totalAmount);

            // Send SMS using custom HTTP client
            $response = $this->sendSmsRequest($message, $formattedPhone);

            Log::info('Order confirmation SMS sent successfully', [
                'phone' => $formattedPhone,
                'order_number' => $orderNumber,
                'customer_name' => $customerName,
                'total_amount' => $totalAmount,
                'response' => $response
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to send order confirmation SMS', [
                'phone' => $phoneNumber,
                'order_number' => $orderNumber,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send SMS request using HTTP client
     *
     * @param string $message
     * @param string $phoneNumber
     * @return array
     * @throws Exception
     */
    private function sendSmsRequest($message, $phoneNumber)
    {
        if (empty($this->apiKey)) {
            throw new Exception('SMS API key is not configured');
        }

        $url = $this->apiUrl . '/sendsms';

        $params = [
            'api_key' => $this->apiKey,
            'msg' => $message,
            'to' => $phoneNumber,
        ];

        try {
            // Use HTTP client with SSL verification disabled for local development
            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for local development
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->post($url, $params);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['error']) && $data['error'] == 0) {
                    return $data['data'] ?? $data['msg'] ?? 'SMS sent successfully';
                } else {
                    throw new Exception($data['msg'] ?? 'Unknown SMS API error');
                }
            } else {
                throw new Exception('HTTP request failed: ' . $response->status());
            }
        } catch (Exception $e) {
            Log::error('SMS API request failed', [
                'url' => $url,
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Format phone number to include Bangladesh country code
     *
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // If it starts with 0, replace with 880
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '880' . substr($phoneNumber, 1);
        }

        // If it doesn't start with 880, add it
        if (substr($phoneNumber, 0, 3) !== '880') {
            $phoneNumber = '880' . $phoneNumber;
        }

        return $phoneNumber;
    }

    /**
     * Create order confirmation message
     *
     * @param string $orderNumber
     * @param string $customerName
     * @param float $totalAmount
     * @return string
     */
    private function createOrderConfirmationMessage($orderNumber, $customerName, $totalAmount)
    {
        $message = "Dear {$customerName},\n\n";
        $message .= "Your order #{$orderNumber} has been confirmed and you will received your goods in 2 to 3 days.\n";
        $message .= "Total Amount: ৳" . number_format((float)$totalAmount, 2) . "\n\n";
        $message .= "Regards,\n";
        $message .= "Home Find";

        return $message;
    }

    /**
     * Test SMS functionality
     *
     * @param string $phoneNumber
     * @return bool
     */
    public function sendTestSms($phoneNumber)
    {
        if (!$this->enabled) {
            Log::info('SMS is disabled. Cannot send test SMS.');
            return false;
        }

        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            $message = "Test SMS from your Laravel application. SMS integration is working correctly!";

            $response = $this->sendSmsRequest($message, $formattedPhone);

            Log::info('Test SMS sent successfully', [
                'phone' => $formattedPhone,
                'response' => $response
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to send test SMS', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}

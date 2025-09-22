<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadFastService
{
    private $apiKey;
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.steadfast.api_key');
        $this->secretKey = config('services.steadfast.secret_key');
        $this->baseUrl = config('services.steadfast.base_url', 'https://portal.packzy.com/api/v1');
    }

    /**
     * Create a parcel/order in SteadFast system
     *
     * @param array $orderData
     * @return array
     */
    public function createParcel($orderData)
    {
        try {
            $response = Http::withOptions([
                'verify' => config('services.steadfast.verify_ssl', true),
                'timeout' => config('services.steadfast.timeout', 30),
            ])->withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/create_order', $orderData);

            // Log the raw response for debugging
            Log::info('SteadFast API Response', [
                'status_code' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'json' => $response->json()
            ]);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info('SteadFast API call successful', [
                    'order_data' => $orderData,
                    'response' => $responseData
                ]);

                // Check if the response indicates actual success
                $isActuallySuccessful = $this->isResponseActuallySuccessful($responseData);

                Log::info('SteadFast response validation', [
                    'response_data' => $responseData,
                    'is_successful' => $isActuallySuccessful
                ]);

                if ($isActuallySuccessful) {
                    return [
                        'success' => true,
                        'data' => $responseData,
                        'message' => 'Parcel created successfully in SteadFast system'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'API call completed but parcel may not have been created. Check SteadFast dashboard.',
                        'error' => $responseData
                    ];
                }
            } else {
                Log::error('SteadFast API error', [
                    'order_data' => $orderData,
                    'status' => $response->status(),
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to create parcel in SteadFast system',
                    'error' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error('SteadFast service exception', [
                'order_data' => $orderData,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while creating parcel: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Prepare order data for SteadFast API
     *
     * @param \App\Models\Order $order
     * @return array
     */
    public function prepareOrderData($order)
    {
        $billingInfo = json_decode($order->billing_info, true);
        $shippingInfo = json_decode($order->shipping_info, true);

        // Use shipping info if available, otherwise use billing info
        $recipientInfo = !empty($shippingInfo) ? $shippingInfo : $billingInfo;

        // Log the recipient info structure for debugging
        Log::info('Recipient info structure', [
            'order_id' => $order->id,
            'billing_info' => $billingInfo,
            'shipping_info' => $shippingInfo,
            'recipient_info' => $recipientInfo
        ]);

        // Extract name with fallback options (check both billing and shipping field names)
        $firstName = $recipientInfo['bill_first_name'] ??
            $recipientInfo['ship_first_name'] ??
            $recipientInfo['first_name'] ??
            $recipientInfo['name'] ??
            'Customer';

        $lastName = $recipientInfo['bill_last_name'] ??
            $recipientInfo['ship_last_name'] ??
            $recipientInfo['last_name'] ??
            '';

        $fullName = trim($firstName . ' ' . $lastName);

        // Extract phone with fallback options (check both billing and shipping field names)
        $phone = $recipientInfo['bill_phone'] ??
            $recipientInfo['ship_phone'] ??
            $recipientInfo['phone'] ??
            $recipientInfo['mobile'] ??
            $recipientInfo['contact'] ??
            $recipientInfo['contact_number'] ??
            '';

        // Clean and validate phone number
        $phone = $this->cleanPhoneNumber($phone);

        // Log the phone extraction for debugging
        Log::info('Phone number extraction', [
            'order_id' => $order->id,
            'bill_phone' => $recipientInfo['bill_phone'] ?? 'not found',
            'ship_phone' => $recipientInfo['ship_phone'] ?? 'not found',
            'cleaned_phone' => $phone,
            'phone_length' => strlen($phone)
        ]);

        // Calculate COD amount (total order amount)
        $codAmount = \App\Helpers\PriceHelper::OrderTotal($order);

        // Prepare item description from cart
        $cart = json_decode($order->cart, true);
        $itemDescription = '';
        if (!empty($cart)) {
            $itemNames = array_map(function ($item) {
                return $item['name'] ?? 'Product';
            }, $cart);
            $itemDescription = implode(', ', $itemNames);
        }

        // Validate required fields
        if (empty($phone)) {
            throw new \Exception('Phone number is required but not found in order data');
        }

        if (empty($fullName) || $fullName === 'Customer') {
            throw new \Exception('Customer name is required but not found in order data');
        }

        $orderData = [
            'invoice' => $order->transaction_number,
            'recipient_name' => $fullName,
            'recipient_phone' => $phone,
            'recipient_address' => $this->formatAddress($recipientInfo),
            'cod_amount' => (float) $codAmount,
            'note' => 'Order from ' . config('app.name'),
            'item_description' => $itemDescription ?: 'General Products',
            'total_lot' => count($cart) ?: 1,
            'delivery_type' => 0 // 0 for home delivery
        ];

        // Log the final order data
        Log::info('Final SteadFast order data', [
            'order_id' => $order->id,
            'order_data' => $orderData
        ]);

        return $orderData;
    }

    /**
     * Format address for SteadFast API
     *
     * @param array $addressInfo
     * @return string
     */
    private function formatAddress($addressInfo)
    {
        $addressParts = [];

        // Try different possible field names for address components (check both billing and shipping field names)
        $address1 = $addressInfo['bill_address1'] ??
            $addressInfo['ship_address1'] ??
            $addressInfo['address1'] ??
            $addressInfo['address'] ??
            '';

        $address2 = $addressInfo['bill_address2'] ??
            $addressInfo['ship_address2'] ??
            $addressInfo['address2'] ??
            '';

        $city = $addressInfo['bill_city'] ??
            $addressInfo['ship_city'] ??
            $addressInfo['city'] ??
            '';

        $state = $addressInfo['bill_state'] ??
            $addressInfo['ship_state'] ??
            $addressInfo['state'] ??
            '';

        $zip = $addressInfo['bill_zip'] ??
            $addressInfo['ship_zip'] ??
            $addressInfo['zip'] ??
            $addressInfo['postal_code'] ??
            '';

        $country = $addressInfo['bill_country'] ??
            $addressInfo['ship_country'] ??
            $addressInfo['country'] ??
            '';

        // Add non-empty address parts
        if (!empty($address1)) {
            $addressParts[] = $address1;
        }

        if (!empty($address2)) {
            $addressParts[] = $address2;
        }

        if (!empty($city)) {
            $addressParts[] = $city;
        }

        if (!empty($state)) {
            $addressParts[] = $state;
        }

        if (!empty($zip)) {
            $addressParts[] = $zip;
        }

        if (!empty($country)) {
            $addressParts[] = $country;
        }

        // If no address parts found, return a default
        if (empty($addressParts)) {
            return 'Address not provided';
        }

        return implode(', ', $addressParts);
    }

    /**
     * Check if the SteadFast API response indicates actual success
     *
     * @param array $responseData
     * @return bool
     */
    private function isResponseActuallySuccessful($responseData)
    {
        // If responseData is empty or null, it's not successful
        if (empty($responseData)) {
            return false;
        }

        // Check for clear error indicators first
        if (isset($responseData['error']) || isset($responseData['errors'])) {
            return false;
        }

        if (isset($responseData['status']) && $responseData['status'] === 'error') {
            return false;
        }

        if (isset($responseData['message'])) {
            $message = strtolower($responseData['message']);
            if (
                strpos($message, 'error') !== false ||
                strpos($message, 'failed') !== false ||
                strpos($message, 'invalid') !== false
            ) {
                return false;
            }
        }

        // Check for common success indicators in SteadFast API responses
        if (isset($responseData['status']) && $responseData['status'] === 'success') {
            return true;
        }

        if (isset($responseData['success']) && $responseData['success'] === true) {
            return true;
        }

        if (isset($responseData['message'])) {
            $message = strtolower($responseData['message']);
            if (
                strpos($message, 'success') !== false ||
                strpos($message, 'created') !== false ||
                strpos($message, 'consignment') !== false
            ) {
                return true;
            }
        }

        // If response contains consignment_id or tracking_id, it's likely successful
        if (isset($responseData['consignment_id']) || isset($responseData['tracking_id'])) {
            return true;
        }

        // If we have a response but no clear success indicators, 
        // and the HTTP status was 200, assume it's successful
        // (since the debug script works, this suggests SteadFast returns a simple response)
        return true;
    }

    /**
     * Clean and format phone number for SteadFast API
     *
     * @param string $phone
     * @return string
     */
    private function cleanPhoneNumber($phone)
    {
        if (empty($phone)) {
            return '';
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Handle different phone number formats
        if (strlen($phone) == 11 && substr($phone, 0, 2) == '01') {
            // Already in correct format: 01XXXXXXXXX
            return $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '1') {
            // Format: 1XXXXXXXXX -> 01XXXXXXXXX
            return '0' . $phone;
        } elseif (strlen($phone) == 13 && substr($phone, 0, 4) == '8801') {
            // Format: 8801XXXXXXXXX -> 01XXXXXXXXX
            return '0' . substr($phone, 3);
        } elseif (strlen($phone) == 14 && substr($phone, 0, 5) == '+8801') {
            // Format: +8801XXXXXXXXX -> 01XXXXXXXXX
            return '0' . substr($phone, 4);
        }

        // If none of the above formats, return as is (might be invalid)
        return $phone;
    }
}

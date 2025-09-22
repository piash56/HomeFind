<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SteadFastService;
use App\Models\Order;

class TestSteadFastAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steadfast:test {order_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SteadFast API integration with a real order';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');

        if (!$orderId) {
            // Get the first "In Progress" order
            $order = Order::where('order_status', 'In Progress')->first();
            if (!$order) {
                $this->error('No orders with "In Progress" status found.');
                return 1;
            }
        } else {
            $order = Order::find($orderId);
            if (!$order) {
                $this->error("Order with ID {$orderId} not found.");
                return 1;
            }
        }

        $this->info("Testing SteadFast API with Order ID: {$order->id}");
        $this->info("Order Transaction Number: {$order->transaction_number}");
        $this->info("Order Status: {$order->order_status}");

        // Check API credentials
        $apiKey = config('services.steadfast.api_key');
        $secretKey = config('services.steadfast.secret_key');

        if (empty($apiKey) || empty($secretKey)) {
            $this->error('SteadFast API credentials are not configured!');
            $this->info('Please add STEADFAST_API_KEY and STEADFAST_SECRET_KEY to your .env file');
            return 1;
        }

        $this->info("API Key: " . substr($apiKey, 0, 8) . "...");
        $this->info("Secret Key: " . substr($secretKey, 0, 8) . "...");

        // Initialize service
        $steadFastService = new SteadFastService();

        // Prepare order data
        $this->info("\nPreparing order data...");
        $orderData = $steadFastService->prepareOrderData($order);

        $this->info("Order data prepared:");
        $this->table(
            ['Field', 'Value'],
            [
                ['Invoice', $orderData['invoice']],
                ['Recipient Name', $orderData['recipient_name']],
                ['Recipient Phone', $orderData['recipient_phone']],
                ['Recipient Address', $orderData['recipient_address']],
                ['COD Amount', $orderData['cod_amount']],
                ['Item Description', $orderData['item_description']],
                ['Total Lot', $orderData['total_lot']],
                ['Delivery Type', $orderData['delivery_type']],
            ]
        );

        // Test API call
        $this->info("\nTesting SteadFast API call...");
        $result = $steadFastService->createParcel($orderData);

        if ($result['success']) {
            $this->info("✅ SUCCESS: " . $result['message']);
            if (isset($result['data'])) {
                $this->info("Response data:");
                $this->line(json_encode($result['data'], JSON_PRETTY_PRINT));
            }
        } else {
            $this->error("❌ FAILED: " . $result['message']);
            if (isset($result['error'])) {
                $this->error("Error details:");
                $this->line(json_encode($result['error'], JSON_PRETTY_PRINT));
            }
        }

        $this->info("\nCheck your SteadFast dashboard to see if the parcel was actually created.");
        $this->info("Also check storage/logs/laravel.log for detailed API response logs.");

        return 0;
    }
}

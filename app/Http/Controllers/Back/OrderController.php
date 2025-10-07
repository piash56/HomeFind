<?php

namespace App\Http\Controllers\Back;

use App\{
    Models\Order,
    Models\PromoCode,
    Models\TrackOrder,
    Http\Controllers\Controller
};
use App\Services\SmsService;
use App\Services\SteadFastService;
use App\Models\Notification;
use App\Helpers\PriceHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        if ($request->type) {
            if ($request->start_date && $request->end_date) {
                $datas = $start_date = Carbon::parse($request->start_date);
                $end_date = Carbon::parse($request->end_date);
                $datas = Order::latest('id')->whereOrderStatus($request->type)->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->get();
            } else {
                $datas = Order::latest('id')->whereOrderStatus($request->type)->get();
            }
        } else {
            if ($request->start_date && $request->end_date) {
                $datas = $start_date = Carbon::parse($request->start_date);
                $end_date = Carbon::parse($request->end_date);
                $datas = Order::latest('id')->whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->get();
            } else {
                $datas = Order::latest('id')->get();
            }
        }
        return view('back.order.index', compact('datas'));
    }


    public function edit($id)
    {

        $order = Order::findOrFail($id);
        return view('back.order.edit', compact('order'));
    }



    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Validate required fields
        $request->validate([
            'transaction_number' => 'required|string|max:255',
            'order_date' => 'required|date',
            'bill_first_name' => 'required|string|max:255',
            'bill_phone' => 'required|string|max:20',
            'bill_address1' => 'required|string|max:500',
            'bill_email' => 'nullable|email|max:255',
            'item_id' => 'required|integer|exists:items,id',
        ]);

        // Check if order_id is available
        if (Order::where('transaction_number', $request->transaction_number)->where('id', '!=', $id)->exists()) {
            return redirect()->route('back.order.index')->withErrors(__('Order ID already exists.'));
        }

        // Get the product
        $item = \App\Models\Item::findOrFail($request->item_id);

        // Determine quantity and pricing
        $quantity = 1;
        $pricePerUnit = $item->discount_price;
        $isBulkPricing = false;
        $totalAttributePrice = 0;
        $attributeOptionNames = [];
        $attributeNames = [];
        $attributeOptionPrices = [];
        $optionsIds = [];

        // Process selected attributes if any
        if ($request->has('selected_attributes') && !empty($request->selected_attributes)) {
            $selectedAttributes = json_decode($request->selected_attributes, true);

            foreach ($selectedAttributes as $attributeId => $optionId) {
                // Get attribute and option details
                $attribute = \App\Models\Attribute::find($attributeId);
                $option = \App\Models\AttributeOption::find($optionId);

                if ($attribute && $option) {
                    $attributeOptionNames[] = $option->name;
                    $attributeNames[] = $attribute->name;
                    $attributeOptionPrices[] = $option->price;
                    $optionsIds[] = $optionId;
                    $totalAttributePrice += $option->price;
                }
            }
        }

        if ($request->has('quantity_option')) {
            // Bulk pricing option selected
            $selectedOption = $request->quantity_option;
            $quantity = (int) $selectedOption;

            // Check if this is a bulk pricing option
            if ($item->enable_bulk_pricing) {
                $bulkPricingData = $item->getBulkPricingData();
                foreach ($bulkPricingData as $option) {
                    if ($option['quantity'] == $quantity) {
                        $pricePerUnit = $option['price'] / $quantity;
                        $isBulkPricing = true;
                        break;
                    }
                }
            }
        } elseif ($request->has('normal_quantity')) {
            // Normal quantity input
            $quantity = (int) $request->normal_quantity;
        }

        // Create new cart structure (matching CheckoutController structure)
        $cart = [
            $item->id => [
                'name' => $item->name,
                'slug' => $item->slug,
                'photo' => $item->photo,
                'main_price' => $pricePerUnit,
                'attribute_price' => $totalAttributePrice,
                'qty' => $quantity,
                'size_qty' => 0,
                'size_price' => 0,
                'size_key' => null,
                'keys' => '',
                'values' => '',
                'item_type' => $item->item_type,
                'license' => '',
                'options_id' => $optionsIds,
                'attribute' => [
                    'option_name' => $attributeOptionNames,
                    'names' => $attributeNames,
                    'option_price' => $attributeOptionPrices
                ]
            ]
        ];

        // Create new billing info
        $billingInfo = [
            'bill_first_name' => $request->bill_first_name,
            'bill_last_name' => '',
            'bill_email' => $request->bill_email ?? '',
            'bill_phone' => $request->bill_phone,
            'bill_company' => '',
            'bill_address1' => $request->bill_address1,
            'bill_address2' => '',
            'bill_zip' => '',
            'bill_city' => 'Dhaka',
            'bill_state' => '',
            'bill_country' => 'Bangladesh',
        ];

        // Create new shipping info (same as billing)
        $shippingInfo = [
            'ship_first_name' => $request->bill_first_name,
            'ship_last_name' => '',
            'ship_email' => $request->bill_email ?? '',
            'ship_phone' => $request->bill_phone,
            'ship_company' => '',
            'ship_address1' => $request->bill_address1,
            'ship_address2' => '',
            'ship_zip' => '',
            'ship_city' => 'Dhaka',
            'ship_state' => '',
            'ship_country' => 'Bangladesh',
        ];

        // Calculate total amount
        $totalAmount = $pricePerUnit * $quantity;

        // Update order
        $order->update([
            'transaction_number' => $request->transaction_number,
            'created_at' => $request->order_date,
            'cart' => json_encode($cart),
            'billing_info' => json_encode($billingInfo),
            'shipping_info' => json_encode($shippingInfo),
        ]);

        return redirect()->route('back.order.index')->withSuccess(__('Order Updated Successfully.'));
    }

    /**
     * Get product data for order editing
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductData(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:items,id'
        ]);

        $product = \App\Models\Item::with(['attributes.options' => function ($query) {
            $query->where('stock', '!=', 0);
        }])->findOrFail($request->product_id);

        // Transform thumbnail URL
        if ($product->thumbnail) {
            if (strpos($product->thumbnail, 'assets/images/') === 0) {
                $product->thumbnail = asset($product->thumbnail);
            } else {
                $product->thumbnail = asset('assets/images/' . $product->thumbnail);
            }
        } else {
            $product->thumbnail = asset('assets/images/noimage.png');
        }

        // Get bulk pricing data
        $bulkPricingData = $product->getBulkPricingData();

        // Format attributes data
        $attributesData = [];
        foreach ($product->attributes as $attribute) {
            if ($attribute->options->count() > 0) {
                $attributesData[] = [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'options' => $attribute->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'name' => $option->name,
                            'price' => $option->price,
                            'stock' => $option->stock
                        ];
                    })
                ];
            }
        }

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'thumbnail' => $product->thumbnail,
                'discount_price' => $product->discount_price,
                'stock' => $product->stock,
                'enable_bulk_pricing' => $product->enable_bulk_pricing,
                'bulk_pricing_data' => $bulkPricingData,
                'attributes' => $attributesData
            ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id)
    {
        $order = Order::findOrfail($id);
        $cart = json_decode($order->cart, true);
        return view('back.order.invoice', compact('order', 'cart'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printOrder($id)
    {
        $order = Order::findOrfail($id);
        $cart = json_decode($order->cart, true);
        return view('back.order.print', compact('order', 'cart'));
    }


    /**
     * Change the status for editing the specified resource.
     *
     * @param  int  $id
     * @param  string  $field
     * @param  string  $value
     * @return \Illuminate\Http\Response
     */
    public function status($id, $field, $value)
    {

        $order = Order::find($id);
        if ($field == 'payment_status') {
            if ($order['payment_status'] == 'Paid') {
                return redirect()->route('back.order.index')->withErrors(__('Order is already paid.'));
            }
        }
        if ($field == 'order_status') {
            if ($order['order_status'] == 'Delivered') {
                return redirect()->route('back.order.index')->withErrors(__('Order is already Delivered.'));
            }
        }

        // Handle delivery cost minus when changing to Delivered status
        if ($field == 'order_status' && $value == 'Delivered') {
            $deliveryCostMinus = request()->query('delivery_cost_minus');
            if (!is_null($deliveryCostMinus) && is_numeric($deliveryCostMinus) && $deliveryCostMinus > 0) {
                $order->delivery_cost_minus = (float)$deliveryCostMinus;
            }
        }

        $order->update([$field => $value]);
        if ($order->payment_status == 'Paid') {
            $this->setPromoCode($order);
        }
        $this->setTrackOrder($order);

        // Send SMS notification only when order status changes to "In Progress"
        if ($field == 'order_status' && $value == 'In Progress') {
            // Read optional delivery_fee from query string
            $deliveryFee = request()->query('delivery_fee');
            $deliveryFeeNumeric = 0.0;
            if (!is_null($deliveryFee) && is_numeric($deliveryFee)) {
                $deliveryFeeNumeric = (float)$deliveryFee;
            }
            $this->sendOrderConfirmationSms($order, $deliveryFeeNumeric);
        }

        return redirect()->route('back.order.index')->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Update delivery cost minus for a delivered order
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDeliveryCost(Request $request, $id)
    {
        $request->validate([
            'delivery_cost_minus' => 'required|numeric|min:0'
        ]);

        $order = Order::findOrFail($id);

        // Only allow updating for delivered orders
        if ($order->order_status !== 'Delivered') {
            return redirect()->back()->withErrors(__('Delivery cost minus can only be updated for delivered orders.'));
        }

        $order->delivery_cost_minus = $request->delivery_cost_minus;
        $order->save();

        return redirect()->route('back.order.invoice', $order->id)
            ->withSuccess(__('Delivery cost minus updated successfully.'));
    }

    /**
     * Send order confirmation SMS when status changes to "In Progress"
     *
     * @param Order $order
     * @return void
     */
    private function sendOrderConfirmationSms($order, $additionalDeliveryFee = 0.0)
    {
        try {
            // Get customer phone number from billing info
            $billingInfo = json_decode($order->billing_info, true);
            $phoneNumber = $billingInfo['bill_phone'] ?? null;

            if (!$phoneNumber) {
                Log::warning('No phone number found for order confirmation SMS', [
                    'order_id' => $order->id,
                    'transaction_number' => $order->transaction_number
                ]);
                return;
            }

            // Get customer name
            $customerName = $billingInfo['bill_first_name'] ?? 'Customer';

            // Get order total using PriceHelper (raw numeric, no formatting)
            $totalAmount = PriceHelper::OrderTotal($order, 'trns');
            // Add optional delivery fee provided during status change
            if (is_numeric($additionalDeliveryFee) && $additionalDeliveryFee > 0) {
                $totalAmount = (float)$totalAmount + (float)$additionalDeliveryFee;
            }

            Log::info('SMS Order Total Calculation', [
                'order_id' => $order->id,
                'transaction_number' => $order->transaction_number,
                'calculated_total' => $totalAmount,
                'additional_delivery_fee' => $additionalDeliveryFee,
                'customer_name' => $customerName
            ]);

            // Send SMS using our service
            $smsService = new SmsService();
            $smsService->sendOrderConfirmation(
                $phoneNumber,
                $order->transaction_number,
                $customerName,
                $totalAmount
            );
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation SMS', [
                'order_id' => $order->id,
                'transaction_number' => $order->transaction_number,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Custom Function
     */
    public function setTrackOrder($order)
    {

        if ($order->order_status == 'In Progress') {
            if (!TrackOrder::whereOrderId($order->id)->whereTitle('In Progress')->exists()) {
                TrackOrder::create([
                    'title' => 'In Progress',
                    'order_id' => $order->id
                ]);
            }
        }
        if ($order->order_status == 'Canceled') {
            if (!TrackOrder::whereOrderId($order->id)->whereTitle('Canceled')->exists()) {

                if (!TrackOrder::whereOrderId($order->id)->whereTitle('In Progress')->exists()) {
                    TrackOrder::create([
                        'title' => 'In Progress',
                        'order_id' => $order->id
                    ]);
                }
                if (!TrackOrder::whereOrderId($order->id)->whereTitle('Delivered')->exists()) {
                    TrackOrder::create([
                        'title' => 'Delivered',
                        'order_id' => $order->id
                    ]);
                }

                if (!TrackOrder::whereOrderId($order->id)->whereTitle('Canceled')->exists()) {
                    TrackOrder::create([
                        'title' => 'Canceled',
                        'order_id' => $order->id
                    ]);
                }
            }
        }

        if ($order->order_status == 'Delivered') {

            if (!TrackOrder::whereOrderId($order->id)->whereTitle('In Progress')->exists()) {
                TrackOrder::create([
                    'title' => 'In Progress',
                    'order_id' => $order->id
                ]);
            }

            if (!TrackOrder::whereOrderId($order->id)->whereTitle('Delivered')->exists()) {
                TrackOrder::create([
                    'title' => 'Delivered',
                    'order_id' => $order->id
                ]);
            }
        }
    }


    public function setPromoCode($order)
    {

        $discount = json_decode($order->discount, true);
        if ($discount != null) {
            $code = PromoCode::find($discount['code']['id']);
            $code->no_of_times--;
            $code->update();
        }
    }


    public function delete($id)
    {
        $order = Order::findOrFail($id);
        if (Notification::where('order_id', $id)->exists()) {
            Notification::where('order_id', $id)->delete();
        }
        if (count($order->tracks_data) > 0) {
            foreach ($order->tracks_data as $track) {
                $track->delete();
            }
        }
        $order->delete();
        return redirect()->back()->withSuccess(__('Order Deleted Successfully.'));
    }

    /**
     * Create parcel in SteadFast system for orders with "In Progress" status
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createSteadFastParcel($id)
    {
        try {
            $order = Order::findOrFail($id);

            // Check if order status is "In Progress"
            if ($order->order_status !== 'In Progress') {
                return redirect()->back()->withErrors(__('Parcel can only be created for orders with "In Progress" status.'));
            }

            // Check if parcel already exists for this order
            if ($this->parcelAlreadyExists($order)) {
                return redirect()->back()->withErrors(__('A parcel has already been created for this order.'));
            }

            // Validate that order has required data
            if (empty($order->billing_info) && empty($order->shipping_info)) {
                return redirect()->back()->withErrors(__('Order is missing billing and shipping information.'));
            }

            // Initialize SteadFast service
            $steadFastService = new SteadFastService();

            // Prepare order data for SteadFast API
            $orderData = $steadFastService->prepareOrderData($order);

            // Log the prepared data for debugging
            Log::info('SteadFast parcel data prepared', [
                'order_id' => $order->id,
                'order_data' => $orderData
            ]);

            // Create parcel in SteadFast system
            $result = $steadFastService->createParcel($orderData);

            if ($result['success']) {
                // Store parcel information in order for future reference
                $this->storeParcelInfo($order, $result['data']);

                return redirect()->back()->withSuccess(__('Parcel created successfully in SteadFast system.'));
            } else {
                return redirect()->back()->withErrors(__('Failed to create parcel: ') . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('SteadFast parcel creation failed', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withErrors(__('An error occurred while creating parcel: ') . $e->getMessage());
        }
    }

    /**
     * Check if a parcel already exists for this order
     *
     * @param \App\Models\Order $order
     * @return bool
     */
    private function parcelAlreadyExists($order)
    {
        // Check if order has SteadFast parcel info stored
        $parcelInfo = json_decode($order->steadfast_parcel_info ?? '{}', true);

        if (!empty($parcelInfo) && isset($parcelInfo['consignment_id'])) {
            return true;
        }

        // Alternative: Check if order has a specific field indicating parcel creation
        // You can also check for a custom field in the order model
        if (!empty($order->steadfast_consignment_id)) {
            return true;
        }

        return false;
    }

    /**
     * Store parcel information in the order
     *
     * @param \App\Models\Order $order
     * @param array $parcelData
     * @return void
     */
    private function storeParcelInfo($order, $parcelData)
    {
        try {
            // Store parcel info as JSON in a custom field
            $parcelInfo = [
                'consignment_id' => $parcelData['consignment']['consignment_id'] ?? null,
                'tracking_code' => $parcelData['consignment']['tracking_code'] ?? null,
                'status' => $parcelData['consignment']['status'] ?? null,
                'created_at' => now()->toISOString(),
                'api_response' => $parcelData
            ];

            // Update order with parcel info
            $order->steadfast_parcel_info = json_encode($parcelInfo);
            $order->save();

            Log::info('SteadFast parcel info stored', [
                'order_id' => $order->id,
                'consignment_id' => $parcelInfo['consignment_id'],
                'tracking_code' => $parcelInfo['tracking_code']
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store SteadFast parcel info', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

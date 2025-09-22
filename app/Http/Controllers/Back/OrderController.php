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

        // Check if order_id is available
        if (Order::where('transaction_number', $request->transaction_number)->where('id', '!=', $id)->exists()) {
            return redirect()->route('back.order.index')->withErrors(__('Order ID already exists.'));
        }

        $order->update($request->all());
        return redirect()->route('back.order.index')->withSuccess(__('Order Updated Successfully.'));
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

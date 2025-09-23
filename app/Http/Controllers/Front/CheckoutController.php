<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\Order,
    Http\Controllers\Controller
};
use App\Helpers\PriceHelper;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Setting;
// Removed shipping/state models for simplified direct order
use App\Models\TrackOrder;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
// Payment gateways removed for simplified direct order

class CheckoutController extends Controller
{

    // All checkout/payment traits removed

    public function __construct()
    {
        $setting = Setting::first();
        if ($setting->is_guest_checkout != 1) {
            // Require auth for traditional checkout pages but allow direct order endpoint for guests
            $this->middleware('auth')->except(['placeDirectOrder', 'paymentSuccess']);
        }
        $this->middleware('localize');
        // Removed gateway initializers
    }

    public function checkoutPage()
    {

        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user() ? Auth::user() : null;
        $cart = Session::get('cart');
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $item) {
            $total += ($item['main_price'] + $item['attribute_price']) * $item['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            // Tax disabled
        }


        $shipping = [];


        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = $cart_total +  $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? Auth::user()->state->price : 0;
        $total_amount = $grand_total + $state_tax;

        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();

        return view('front.checkout.index', $data);
    }

    public function ship_address()
    {
        // Always show simplified checkout now
        // $setting = Setting::first();
        // if ($setting->is_single_checkout == 1) {
        //     return redirect(route("front.checkout"));
        // }


        // Simplified checkout - no redirects
        // Session::forget('shipping_address');
        // if (Session::has('shipping_address')) {
        //     return redirect(route('front.checkout.payment'));
        // }



        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $item) {

            $total += ($item['main_price'] + $item['attribute_price']) * $item['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            // Tax disabled
        }
        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = $cart_total + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;

        $total_amount = $grand_total;
        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();

        return view('front.checkout.billing', $data);
    }



    public function billingStore(Request $request)
    {
        // Store billing address
        Session::put('billing_address', $request->all());

        // If payment method is provided, process order directly (simplified checkout)
        if ($request->has('payment_method')) {
            // Set shipping address same as billing for simplified checkout
            $shipping = [
                "ship_first_name" => $request->bill_first_name,
                "ship_last_name" => $request->bill_last_name,
                "ship_email" => $request->bill_email,
                "ship_phone" => $request->bill_phone,
                "ship_company" => $request->bill_company,
                "ship_address1" => $request->bill_address1,
                "ship_address2" => $request->bill_address2,
                "ship_zip" => $request->bill_zip,
                "ship_city" => $request->bill_city,
                "ship_country" => $request->bill_country,
            ];
            Session::put('shipping_address', $shipping);

            // Set default shipping and state
            Session::put('shipping_id', $request->shipping_id ?? 1);
            Session::put('state_id', $request->state_id ?? '');

            // Process the order directly
            return $this->checkout($request);
        }

        // For simplified checkout, always stay on billing page
        return redirect()->route('front.checkout.billing');
    }


    public function shipping()
    {

        if (Session::has('shipping_address')) {
            return redirect(route('front.checkout.payment'));
        }

        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $item) {

            $total += ($item['main_price'] + $item['attribute_price']) * $item['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            // Tax disabled
        }
        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = $cart_total + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;

        $total_amount = $grand_total;
        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
        return view('front.checkout.shipping', $data);
    }

    public function shippingStore(Request $request)
    {

        Session::put('shipping_address', $request->all());
        return redirect(route('front.checkout.payment'));
    }



    public function payment()
    {
        if (!Session::has('billing_address')) {
            return redirect(route('front.checkout.billing'));
        }

        if (!Session::has('shipping_address')) {
            return redirect(route('front.checkout.shipping'));
        }


        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }
        $data['user'] = Auth::user();
        $cart = Session::get('cart');

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $item) {

            $total += ($item['main_price'] + $item['attribute_price']) * $item['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            // Tax disabled
        }
        $shipping = [];

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        if (!PriceHelper::Digital()) {
            $shipping = null;
        }

        $grand_total = ($cart_total  + $total_tax);
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $state_tax = Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0;
        $grand_total = $grand_total + $state_tax;


        $total_amount = $grand_total;

        $data['cart'] = $cart;
        $data['cart_total'] = $cart_total;
        $data['grand_total'] = $total_amount;
        $data['discount'] = $discount;
        $data['shipping'] = $shipping;
        $data['tax'] = $total_tax;
        $data['payments'] = PaymentSetting::whereStatus(1)->get();
        return view('front.checkout.payment', $data);
    }

    public function checkout(Request $request)
    {
        // For simplified checkout, we handle validation manually
        // PriceHelper::checkCheckout($request);

        // Check if cart exists and is not empty
        if (!Session::has('cart') || empty(Session::get('cart'))) {
            return redirect()->route('front.cart')->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        $input = $request->all();

        $checkout = false;
        $payment_redirect = false;
        $payment = null;

        if (Session::has('currency')) {
            $currency = Currency::findOrFail(Session::get('currency'));
        } else {
            $currency = Currency::where('is_default', 1)->first();
        }


        $usd_supported = array(
            "USD",
            "AED",
            "AFN",
            "ALL",
            "AMD",
            "ANG",
            "AOA",
            "ARS",
            "AUD",
            "AWG",
            "AZN",
            "BAM",
            "BBD",
            "BDT",
            "BGN",
            "BIF",
            "BMD",
            "BND",
            "BOB",
            "BRL",
            "BSD",
            "BWP",
            "BYN",
            "BZD",
            "CAD",
            "CDF",
            "CHF",
            "CLP",
            "CNY",
            "COP",
            "CRC",
            "CVE",
            "CZK",
            "DJF",
            "DKK",
            "DOP",
            "DZD",
            "EGP",
            "ETB",
            "EUR",
            "FJD",
            "FKP",
            "GBP",
            "GEL",
            "GIP",
            "GMD",
            "GNF",
            "GTQ",
            "GYD",
            "HKD",
            "HNL",
            "HTG",
            "HUF",
            "IDR",
            "ILS",
            "INR",
            "ISK",
            "JMD",
            "JPY",
            "KES",
            "KGS",
            "KHR",
            "KMF",
            "KRW",
            "KYD",
            "KZT",
            "LAK",
            "LBP",
            "LKR",
            "LRD",
            "LSL",
            "MAD",
            "MDL",
            "MGA",
            "MKD",
            "MMK",
            "MNT",
            "MOP",
            "MUR",
            "MVR",
            "MWK",
            "MXN",
            "MYR",
            "MZN",
            "NAD",
            "NGN",
            "NIO",
            "NOK",
            "NPR",
            "NZD",
            "PAB",
            "PEN",
            "PGK",
            "PHP",
            "PKR",
            "PLN",
            "PYG",
            "QAR",
            "RON",
            "RSD",
            "RUB",
            "RWF",
            "SAR",
            "SBD",
            "SCR",
            "SEK",
            "SGD",
            "SHP",
            "SLE",
            "SOS",
            "SRD",
            "STD",
            "SZL",
            "THB",
            "TJS",
            "TOP",
            "TRY",
            "TTD",
            "TWD",
            "TZS",
            "UAH",
            "UGX",
            "UYU",
            "UZS",
            "VND",
            "VUV",
            "WST",
            "XAF",
            "XCD",
            "XOF",
            "XPF",
            "YER",
            "ZAR",
            "ZMW"
        );


        $paypal_supported = ['USD', 'EUR', 'AUD', 'BRL', 'CAD', 'HKD', 'JPY', 'MXN', 'NZD', 'PHP', 'GBP', 'RUB'];
        $paystack_supported = ['NGN', "GHS", "USD", "ZAR", "KES"];
        switch ($input['payment_method']) {

            case 'Stripe':
                if (!in_array($currency->name, $usd_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->stripeSubmit($input);
                break;

            case 'Paypal':
                if (!in_array($currency->name, $paypal_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->paypalSubmit($input);
                break;


            case 'Mollie':
                if (!in_array($currency->name, $usd_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->MollieSubmit($input);
                break;

            case 'Paystack':
                if (!in_array($currency->name, $paystack_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment = $this->PaystackSubmit($input);

                break;

            case 'Bank':
                $checkout = true;
                $payment = $this->BankSubmit($input);
                break;

            case 'Paytabs':
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->PayTabsSubmit($input);
                break;

            case 'Cash On Delivery':
                $checkout = true;
                $payment = $this->cashOnDeliverySubmit($input);
                break;
        }



        if ($checkout) {
            if ($payment_redirect) {

                if ($payment['status']) {
                    return redirect()->away($payment['link']);
                } else {
                    Session::put('message', $payment['message']);
                    return redirect()->route('front.checkout.cancle');
                }
            } else {
                if ($payment['status']) {
                    return redirect()->route('front.checkout.success');
                } else {
                    Session::put('message', $payment['message']);
                    return redirect()->route('front.checkout.cancle');
                }
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function paymentRedirect(Request $request)
    {
        $responseData = $request->all();

        if (isset($responseData['session_id'])) {
            $payment = $this->stripeNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } elseif (Session::has('order_payment_id')) {
            $payment = $this->paypalNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function mollieRedirect(Request $request)
    {

        $responseData = $request->all();

        $payment = Mollie::api()->payments()->get(Session::get('payment_id'));
        $responseData['payment_id'] = $payment->id;
        if ($payment->status == 'paid') {
            $payment = $this->mollieNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function paymentSuccess(Request $request)
    {
        $order_id = null;

        // Debug: Log all request parameters
        \Log::info('PaymentSuccess called with request data:', $request->all());
        \Log::info('Session has order_id: ' . (Session::has('order_id') ? 'true' : 'false'));
        \Log::info('Request has id parameter: ' . ($request->has('id') ? 'true' : 'false'));

        // Check if order ID is in session (normal checkout flow)
        if (Session::has('order_id')) {
            $order_id = Session::get('order_id');
            \Log::info('Using session order_id: ' . $order_id);
        }
        // Check if order ID is in URL parameter (direct order flow)
        elseif ($request->has('id')) {
            $order_id = (int) $request->get('id');
            \Log::info('Using request id parameter: ' . $order_id);
        }

        if ($order_id) {
            // Debug: Log the order ID being searched
            \Log::info('Looking for order ID: ' . $order_id);

            $order = Order::find($order_id);

            // Check if order exists
            if (!$order) {
                \Log::error('Order not found with ID: ' . $order_id);
                return redirect()->route('front.index')->with('error', 'Order not found.');
            }

            \Log::info('Order found: ' . $order->id);

            $cart = json_decode($order->cart, true);

            // Server-side tracking: Facebook CAPI and GA4 purchase
            try {
                $totalAmount = \App\Helpers\PriceHelper::OrderTotal($order, 'trns');

                // Facebook CAPI
                $fb = new \App\Services\FacebookCapiService();
                if ($fb->isEnabled()) {
                    $billing = json_decode($order->billing_info, true) ?: [];
                    $phone = $billing['bill_phone'] ?? '';
                    $email = $billing['bill_email'] ?? '';
                    $name = trim(($billing['bill_first_name'] ?? '') . ' ' . ($billing['bill_last_name'] ?? ''));

                    $userData = [];
                    if (!empty($email)) {
                        $userData['em'] = [hash('sha256', strtolower(trim($email)))];
                    }
                    if (!empty($phone)) {
                        $userData['ph'] = [hash('sha256', preg_replace('/[^0-9]/', '', $phone))];
                    }
                    if (!empty($name)) {
                        $userData['fn'] = [hash('sha256', strtolower(trim($billing['bill_first_name'] ?? '')))];
                        $userData['ln'] = [hash('sha256', strtolower(trim($billing['bill_last_name'] ?? '')))];
                    }

                    $items = [];
                    foreach ($cart as $row) {
                        $items[] = [
                            'id' => (string)($row['item']['id'] ?? ''),
                            'quantity' => (int)($row['qty'] ?? 1),
                            'item_price' => (float)($row['item']['discount_price'] ?? $row['item']['price'] ?? 0),
                        ];
                    }

                    $fb->sendPurchaseEvent([
                        'user_data' => $userData,
                        'custom_data' => [
                            'currency' => env('CURRENCY_ISO', 'BDT'),
                            'value' => (float)$totalAmount,
                            'contents' => $items,
                            'content_type' => 'product',
                            'order_id' => (string)$order->id,
                            'transaction_id' => (string)$order->transaction_number,
                        ],
                    ]);
                }

                // GA4 Measurement API
                $ga4 = new \App\Services\Ga4MeasurementService();
                if ($ga4->isEnabled()) {
                    $items = [];
                    foreach ($cart as $row) {
                        $items[] = [
                            'item_id' => (string)($row['item']['id'] ?? ''),
                            'item_name' => (string)($row['item']['name'] ?? ''),
                            'quantity' => (int)($row['qty'] ?? 1),
                            'price' => (float)($row['item']['discount_price'] ?? $row['item']['price'] ?? 0),
                        ];
                    }

                    $ga4->sendPurchaseEvent([
                        'transaction_id' => (string)$order->transaction_number,
                        'value' => (float)$totalAmount,
                        'currency' => env('CURRENCY_ISO', 'BDT'),
                        'items' => $items,
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::error('Server-side tracking failed', ['error' => $e->getMessage()]);
            }

            // Twilio/SMS notification on success removed in simplified flow
            return view('front.checkout.success', compact('order', 'cart'));
        }

        return redirect()->route('front.index');
    }



    public function paymentCancle()
    {
        $message = '';
        if (Session::has('message')) {
            $message = Session::get('message');
            Session::forget('message');
        } else {
            $message = __('Payment Failed!');
        }
        Session::flash('error', $message);
        return redirect()->route('front.checkout.billing');
    }

    public function stateSetUp(Request $request)
    {
        $state_id = $request->state_id;
        $shipping_id = $request->shipping_id;


        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $cart = Session::get('cart');
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;
        foreach ($cart as $key => $item) {

            $total += ($item['main_price'] + $item['attribute_price']) * $item['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            // Tax disabled
        }

        $shipping = [];
        if ($shipping_id) {
            $shipping = ShippingService::findOrFail($shipping_id);
        }
        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);

        $state_price = 0;
        if ($state_id) {
            $state = State::findOrFail($state_id);
            if ($state->type == 'fixed') {
                $state_price = $state->price;
            } else {
                $state_price = ($cart_total * $state->price) / 100;
            }
        } else {
            if (Auth::check() && Auth::user()->state_id) {
                $state = Auth::user()->state;
                if ($state->type == 'fixed') {
                    $state_price = $state->price;
                } else {
                    $state_price = ($cart_total * $state->price) / 100;
                }
            } else {
                $state_price = 0;
            }
        }

        $total_amount = $grand_total + $state_price;

        $data['state_price'] = PriceHelper::setCurrencyPrice($state_price);
        $data['grand_total'] = PriceHelper::setCurrencyPrice($total_amount);

        return response()->json($data);
    }

    public function shippingSetUp(Request $request)
    {
        $state_id = $request->state_id;
        $shipping_id = $request->shipping_id;



        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $cart = Session::get('cart');
        $total_tax = 0;
        $cart_total = 0;
        $total = 0;
        foreach ($cart as $key => $item) {

            $total += ($item['main_price'] + $item['attribute_price']) * $item['qty'];
            $cart_total = $total;
            $item = Item::findOrFail($key);
            // Tax disabled
        }

        $shipping = ShippingService::findOrFail($shipping_id);

        $discount = [];
        if (Session::has('coupon')) {
            $discount = Session::get('coupon');
        }

        $grand_total = ($cart_total + ($shipping ? $shipping->price : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);

        $state_price = 0;
        if ($state_id && $state_id != 'undefined') {
            $state = State::findOrFail($state_id);
            if ($state->type == 'fixed') {
                $state_price = $state->price;
            } else {
                $state_price = ($cart_total * $state->price) / 100;
            }
        } else {
            if (Auth::check() && Auth::user()->state_id) {
                $state = Auth::user()->state;
                if ($state->type == 'fixed') {
                    $state_price = $state->price;
                } else {
                    $state_price = ($cart_total * $state->price) / 100;
                }
            } else {
                $state_price = 0;
            }
        }

        $total_amount = $grand_total + $state_price;

        $data['state_price'] = PriceHelper::setCurrencyPrice($state_price);
        $data['shipping_price'] = PriceHelper::setCurrencyPrice($shipping->price);
        $data['grand_total'] = PriceHelper::setCurrencyPrice($total_amount);

        return response()->json($data);
    }

    public function placeDirectOrder(Request $request)
    {
        // Validate required fields (no email required)
        $request->validate([
            'bill_first_name' => 'required|string|max:255',
            'bill_phone' => 'required|string|max:20',
            'bill_address1' => 'required|string|max:500',
            'item_id' => 'required|integer|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            // Get the product
            $item = Item::findOrFail($request->item_id);
            $quantity = $request->quantity;

            // Check stock
            if (!$item->is_stock() || $item->stock < $quantity) {
                return response()->json(['error' => 'Product is out of stock'], 400);
            }

            // Calculate pricing (will be updated after attributes are processed)
            $product_price = $item->discount_price;

            // Get currency info
            if (Session::has('currency')) {
                $currency = Currency::findOrFail(Session::get('currency'));
            } else {
                $currency = Currency::where('is_default', 1)->first();
            }

            // Get selected attribute options
            $attributeOptionNames = [];
            $attributeNames = [];
            $attributeOptionPrices = [];
            $optionsIds = [];
            $totalAttributePrice = 0;

            // Process selected attributes if any
            if ($request->has('selected_attributes')) {
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

            // Calculate final pricing with attributes
            $cart_total = ($product_price + $totalAttributePrice) * $quantity;
            $tax = $item->tax ? $item::taxCalculate($item) * $quantity : 0;
            $shipping_price = 0; // Free shipping
            $discount = 0; // No discount
            $state_price = 0; // No state tax
            $grand_total = $cart_total + $tax + $shipping_price - $discount + $state_price;

            // Create cart structure for order (matching normal cart structure)
            $cart = [
                $item->id => [
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'photo' => $item->photo,
                    'main_price' => $product_price,
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

            // Create billing address
            $billing_address = [
                'bill_first_name' => $request->bill_first_name,
                'bill_last_name' => '',
                'bill_email' => '', // No email required
                'bill_phone' => $request->bill_phone,
                'bill_company' => '',
                'bill_address1' => $request->bill_address1,
                'bill_address2' => '',
                'bill_zip' => '',
                'bill_city' => 'Dhaka',
                'bill_country' => 'Bangladesh',
            ];

            // Create shipping address (same as billing)
            $shipping_address = [
                'ship_first_name' => $request->bill_first_name,
                'ship_last_name' => '',
                'ship_email' => '', // No email required
                'ship_phone' => $request->bill_phone,
                'ship_company' => '',
                'ship_address1' => $request->bill_address1,
                'ship_address2' => '',
                'ship_zip' => '',
                'ship_city' => 'Dhaka',
                'ship_country' => 'Bangladesh',
            ];

            // Create order data
            $orderData = [
                'state' => null,
                'cart' => json_encode($cart),
                'discount' => json_encode([]),
                'shipping' => json_encode(['title' => 'Free Shipping', 'price' => 0]),
                'tax' => $tax,
                'state_price' => $state_price,
                'shipping_info' => json_encode($shipping_address),
                'billing_info' => json_encode($billing_address),
                'payment_method' => 'Cash On Delivery',
                'user_id' => Auth::check() ? Auth::id() : 0,
                'transaction_number' => Str::random(10),
                'currency_sign' => $currency->sign,
                'currency_value' => $currency->value,
                'payment_status' => 'Unpaid',
                'order_status' => 'Pending',
            ];

            // Create the order
            $order = Order::create($orderData);

            // Debug: Log the created order
            \Log::info('Order created with ID: ' . $order->id);

            // Generate proper transaction number
            $new_txn = 'ORD-' . str_pad(Carbon::now()->format('Ymd'), 4, '0000', STR_PAD_LEFT) . '-' . $order->id;
            $order->transaction_number = $new_txn;
            $order->save();

            // Debug: Log the final order details
            \Log::info('Order saved with ID: ' . $order->id . ', Transaction: ' . $new_txn);

            // Create order tracking
            TrackOrder::create([
                'title' => 'Pending',
                'order_id' => $order->id,
            ]);

            // Create notification
            Notification::create([
                'order_id' => $order->id
            ]);

            // Reduce stock
            $item->stock = $item->stock - $quantity;
            $item->save();

            // TODO: SMS notification will be added later
            // For now, no email or SMS notifications

            // Clear any existing sessions (including order_id to prevent conflicts)
            Session::forget(['cart', 'billing_address', 'shipping_address', 'shipping_id', 'state_id', 'order_id']);

            $redirectUrl = route('front.checkout.success', ['id' => $order->id]);
            \Log::info('Order ID for redirect: ' . $order->id);
            \Log::info('Redirect URL generated: ' . $redirectUrl);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id,
                'transaction_number' => $order->transaction_number,
                'redirect_url' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }
}

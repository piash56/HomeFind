<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\Order,
    Http\Controllers\Controller
};
use App\Helpers\PriceHelper;
use App\Helpers\EmailHelper;
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
            $this->middleware('auth')->except(['placeDirectOrder', 'paymentSuccess', 'ship_address', 'billingStore']);
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
            // Use main_price directly as it already includes the final calculated price
            $total += $item['main_price'] * $item['qty'];
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
        $data['setting'] = Setting::first();

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

            // Use main_price directly as it already includes the final calculated price
            $total += $item['main_price'] * $item['qty'];
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
        $data['setting'] = Setting::first();

        return view('front.checkout.billing', $data);
    }



    public function billingStore(Request $request)
    {
        // Validate required fields
        $request->validate([
            'bill_first_name' => 'required|string|max:255',
            'bill_phone' => 'required|string|max:20',
            'bill_address1' => 'required|string|max:500',
            'delivery_area' => 'required|in:inside_dhaka,outside_dhaka,free_delivery',
        ]);

        // Check if cart exists
        if (!Session::has('cart') || empty(Session::get('cart'))) {
            return redirect()->route('front.cart')->with('error', 'Your cart is empty');
        }

        try {
            $cart = Session::get('cart');

            // Get currency info
            if (Session::has('currency')) {
                $currency = Currency::findOrFail(Session::get('currency'));
            } else {
                $currency = Currency::where('is_default', 1)->first();
            }

            // Calculate cart total
            $cart_total = 0;
            foreach ($cart as $key => $item) {
                // Use main_price directly as it already includes the final calculated price
                $cart_total += $item['main_price'] * $item['qty'];
            }

            // Apply discount if coupon exists
            $discount = 0;
            if (Session::has('coupon')) {
                $discount = Session::get('coupon')['discount'];
            }

            // Calculate delivery fee based on selected area
            $shipping_price = 0;
            $delivery_area_title = '';

            if ($request->delivery_area === 'free_delivery') {
                $shipping_price = 0;
                $delivery_area_title = 'Free Delivery';
            } elseif ($request->delivery_area === 'inside_dhaka') {
                $shipping_price = 70;
                $delivery_area_title = 'Inside Dhaka Delivery';
            } else {
                $shipping_price = 130;
                $delivery_area_title = 'Outside Dhaka Delivery';
            }

            $tax = 0;
            $state_price = 0;
            $grand_total = $cart_total + $tax + $shipping_price - $discount + $state_price;

            // Create billing address (include order notes in billing info)
            $billing_address = [
                'bill_first_name' => $request->bill_first_name,
                'bill_last_name' => '',
                'bill_email' => $request->bill_email ?? 'customer@example.com',
                'bill_phone' => $request->bill_phone,
                'bill_company' => '',
                'bill_address1' => $request->bill_address1,
                'bill_address2' => '',
                'bill_zip' => '',
                'bill_city' => 'Dhaka',
                'bill_country' => 'Bangladesh',
                'order_notes' => $request->order_notes ?? '', // Store notes here
            ];

            // Create shipping address (same as billing)
            $shipping_address = [
                'ship_first_name' => $request->bill_first_name,
                'ship_last_name' => '',
                'ship_email' => $request->bill_email ?? 'customer@example.com',
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
                'discount' => json_encode(Session::has('coupon') ? Session::get('coupon') : []),
                'shipping' => json_encode(['title' => $delivery_area_title, 'price' => $shipping_price]),
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

            // Generate proper transaction number
            $new_txn = 'ORD-' . str_pad(Carbon::now()->format('Ymd'), 4, '0000', STR_PAD_LEFT) . '-' . $order->id;
            $order->transaction_number = $new_txn;
            $order->save();

            // Create order tracking
            TrackOrder::create([
                'title' => 'Pending',
                'order_id' => $order->id,
            ]);

            // Create notification
            Notification::create([
                'order_id' => $order->id
            ]);

            // Reduce stock for each item in cart
            foreach ($cart as $key => $item) {
                $itemId = explode('-', $key, 2)[0];
                $product = Item::find($itemId);
                if ($product && $product->item_type == 'normal') {
                    $product->stock = $product->stock - $item['qty'];
                    $product->save();
                }
            }

            // Send admin email notification if enabled
            try {
                $setting = Setting::first();
                if ($setting->order_mail == 1) {
                    $this->sendCartOrderNotification($order);
                }
            } catch (\Exception $emailError) {
                \Log::error('Email notification failed: ' . $emailError->getMessage());
            }

            // Clear sessions
            Session::forget(['cart', 'billing_address', 'shipping_address', 'coupon', 'shipping_id', 'state_id']);

            // Redirect to success page
            return redirect()->route('front.checkout.success', ['id' => $order->id]);
        } catch (\Exception $e) {
            \Log::error('Checkout failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    /**
     * Send admin email notification for cart order
     */
    private function sendCartOrderNotification($order)
    {
        try {
            $setting = Setting::first();
            $shippingInfo = json_decode($order->shipping_info, true) ?: [];
            $cart = json_decode($order->cart, true) ?: [];
            $discountData = json_decode($order->discount, true) ?: [];
            $shippingData = json_decode($order->shipping, true) ?: [];

            $customerName = $shippingInfo['ship_first_name'] ?? '';
            $customerPhone = $shippingInfo['ship_phone'] ?? '';
            $customerAddress = $shippingInfo['ship_address1'] ?? '';

            // Build items table with all products and attributes
            $itemsHtml = '<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; margin: 10px 0;">';
            $itemsHtml .= '<tr style="background-color:#f8f9fa;">
                <th style="border:1px solid #ddd; padding:8px; text-align:left;">Product</th>
                <th style="border:1px solid #ddd; padding:8px; text-align:left;">Attributes</th>
                <th style="border:1px solid #ddd; padding:8px; text-align:center;">Qty</th>
                <th style="border:1px solid #ddd; padding:8px; text-align:right;">Unit Price</th>
                <th style="border:1px solid #ddd; padding:8px; text-align:right;">Line Total</th>
            </tr>';

            $subTotal = 0;

            foreach ($cart as $itemKey => $cartItem) {
                $name = $cartItem['name'] ?? '';
                $qty = $cartItem['qty'] ?? 1;
                $mainPrice = $cartItem['main_price'] ?? 0;
                // Use main_price directly as it already includes the final calculated price
                $unitRaw = $mainPrice;
                $lineRaw = $unitRaw * $qty;
                $subTotal += $lineRaw;

                // Attributes text
                $attributesText = '';
                if (!empty($cartItem['attribute']['option_name']) && is_array($cartItem['attribute']['option_name'])) {
                    $names = $cartItem['attribute']['names'] ?? [];
                    foreach ($cartItem['attribute']['option_name'] as $idx => $optName) {
                        $attrLabel = $names[$idx] ?? '';
                        $attributesText .= ($attributesText ? '<br>' : '');
                        $attributesText .= '<strong>' . e($attrLabel) . ':</strong> ' . e($optName);
                    }
                }

                $itemsHtml .= '<tr>
                    <td style="border:1px solid #ddd; padding:8px;">' . e($name) . '</td>
                    <td style="border:1px solid #ddd; padding:8px;">' . ($attributesText ?: '-') . '</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:center;">' . (int)$qty . '</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:right;">' . PriceHelper::setCurrencyPrice($unitRaw) . '</td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:right;">' . PriceHelper::setCurrencyPrice($lineRaw) . '</td>
                </tr>';
            }

            // Summary rows
            $shippingPrice = $shippingData['price'] ?? 0;
            $tax          = $order->tax ?? 0;
            $statePrice   = $order->state_price ?? 0;
            $discountAmount = $discountData['discount'] ?? 0;

            // Normalize coupon code (may be stored as array of attributes or plain string)
            $couponCode = '';
            if (isset($discountData['code'])) {
                if (is_array($discountData['code'])) {
                    // From PromoCode model attributes stored in JSON
                    $couponCode = $discountData['code']['code_name'] ?? '';
                } elseif (is_string($discountData['code'])) {
                    $couponCode = $discountData['code'];
                }
            }

            $grandTotal = $subTotal + $shippingPrice + $tax + $statePrice - $discountAmount;

            $itemsHtml .= '<tr style="background-color:#f8f9fa;">
                <td colspan="4" style="border:1px solid #ddd; padding:8px; text-align:right;"><strong>Subtotal</strong></td>
                <td style="border:1px solid #ddd; padding:8px; text-align:right;">' . PriceHelper::setCurrencyPrice($subTotal) . '</td>
            </tr>';

            if ($shippingPrice > 0) {
                $itemsHtml .= '<tr>
                    <td colspan="4" style="border:1px solid #ddd; padding:8px; text-align:right;"><strong>Shipping</strong></td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:right;">' . PriceHelper::setCurrencyPrice($shippingPrice) . '</td>
                </tr>';
            }

            if ($tax > 0) {
                $itemsHtml .= '<tr>
                    <td colspan="4" style="border:1px solid #ddd; padding:8px; text-align:right;"><strong>Tax</strong></td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:right;">' . PriceHelper::setCurrencyPrice($tax) . '</td>
                </tr>';
            }

            if ($statePrice > 0) {
                $itemsHtml .= '<tr>
                    <td colspan="4" style="border:1px solid #ddd; padding:8px; text-align:right;"><strong>State Charge</strong></td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:right;">' . PriceHelper::setCurrencyPrice($statePrice) . '</td>
                </tr>';
            }

            if ($discountAmount > 0) {
                $label = 'Coupon Discount';
                if ($couponCode) {
                    $label .= ' (' . e($couponCode) . ')';
                }
                $itemsHtml .= '<tr>
                    <td colspan="4" style="border:1px solid #ddd; padding:8px; text-align:right;"><strong>' . $label . '</strong></td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:right;">-' . PriceHelper::setCurrencyPrice($discountAmount) . '</td>
                </tr>';
            }

            $itemsHtml .= '<tr style="background-color:#e9ffe9;">
                <td colspan="4" style="border:1px solid #ddd; padding:8px; text-align:right;"><strong>Grand Total</strong></td>
                <td style="border:1px solid #ddd; padding:8px; text-align:right; font-weight:bold;">' . PriceHelper::setCurrencyPrice($grandTotal) . '</td>
            </tr>';

            $itemsHtml .= '</table>';

            $emailData = [
                'transaction_number' => $order->transaction_number,
                'customer_name'      => $customerName,
                'customer_phone'     => $customerPhone,
                'customer_address'   => $customerAddress,
                'total_price'        => PriceHelper::setCurrencyPrice($grandTotal),
                'payment_method'     => $order->payment_method,
                'order_status'       => $order->order_status,
                'bulk_pricing_info'  => $itemsHtml,
            ];

            $emailHelper = new EmailHelper();
            $emailHelper->adminMail($emailData);
        } catch (\Exception $e) {
            \Log::error('Cart order notification failed: ' . $e->getMessage());
        }
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

            // Use main_price directly as it already includes the final calculated price
            $total += $item['main_price'] * $item['qty'];
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
        $data['setting'] = Setting::first();
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

            // Use main_price directly as it already includes the final calculated price
            $total += $item['main_price'] * $item['qty'];
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
        $data['setting'] = Setting::first();
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

            // ⚠️ PREVENT DUPLICATE SERVER EVENTS
            // Check if we've already sent tracking for this order in this session
            $trackingKey = 'server_tracking_sent_' . $order->id;
            $alreadySent = Session::get($trackingKey, false);

            // Server-side tracking: Facebook CAPI and GA4 purchase
            // Only send if not already sent in this session
            if (!$alreadySent) {
                try {
                    $totalAmount = \App\Helpers\PriceHelper::OrderTotal($order, 'trns');

                    // Facebook CAPI - Server-side purchase tracking
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
                        foreach ($cart as $item_id => $row) {
                            $items[] = [
                                'id' => (string)$item_id,
                                'quantity' => (int)($row['qty'] ?? 1),
                                'item_price' => (float)($row['main_price'] ?? 0),
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
                        foreach ($cart as $item_id => $row) {
                            $items[] = [
                                'item_id' => (string)$item_id,
                                'item_name' => (string)($row['name'] ?? ''),
                                'quantity' => (int)($row['qty'] ?? 1),
                                'price' => (float)($row['main_price'] ?? 0),
                            ];
                        }

                        $ga4->sendPurchaseEvent([
                            'transaction_id' => (string)$order->transaction_number,
                            'value' => (float)$totalAmount,
                            'currency' => env('CURRENCY_ISO', 'BDT'),
                            'items' => $items,
                        ]);
                    }

                    // Mark tracking as sent for this session to prevent duplicates
                    Session::put($trackingKey, true);
                    \Log::info('Server-side tracking completed and marked as sent for order: ' . $order->id);
                } catch (\Throwable $e) {
                    \Log::error('Server-side tracking failed', ['error' => $e->getMessage()]);
                }
            } else {
                \Log::info('Server-side tracking SKIPPED - already sent for order: ' . $order->id);
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

            // Use main_price directly as it already includes the final calculated price
            $total += $item['main_price'] * $item['qty'];
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

            // Use main_price directly as it already includes the final calculated price
            $total += $item['main_price'] * $item['qty'];
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

            // Check if bulk pricing is being used
            $isBulkPricing = $request->has('bulk_pricing') && $request->bulk_pricing;
            $bulkTotalPrice = 0;

            if ($isBulkPricing && $request->has('bulk_total_price')) {
                $bulkTotalPrice = (float) $request->bulk_total_price;
                $quantity = (int) $request->bulk_quantity;
            }

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

            // Process selected attributes if any (not applicable for bulk pricing)
            if (!$isBulkPricing && $request->has('selected_attributes')) {
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

            // Calculate final pricing
            if ($isBulkPricing) {
                // Use bulk pricing total directly
                $cart_total = $bulkTotalPrice;
            } else {
                // Calculate with attributes
                $cart_total = ($product_price + $totalAttributePrice) * $quantity;
            }

            $tax = $item->tax ? $item::taxCalculate($item) * $quantity : 0;

            // Get delivery fee if separate delivery is enabled
            $shipping_price = 0; // Default free shipping
            $delivery_area_title = 'Free Shipping';

            if ($request->has('delivery_area') && $request->has('delivery_fee')) {
                $shipping_price = (float) $request->delivery_fee;
                if ($request->delivery_area === 'inside_dhaka') {
                    $delivery_area_title = 'Inside Dhaka Delivery';
                } else if ($request->delivery_area === 'outside_dhaka') {
                    $delivery_area_title = 'Outside Dhaka Delivery';
                }
            }

            // Handle coupon discount if provided
            $discount = 0;
            $discountData = [];

            if ($request->has('coupon_code') && !empty($request->coupon_code)) {
                $promoCode = \App\Models\PromoCode::where('code_name', $request->coupon_code)
                    ->where('status', 1)
                    ->first();

                if ($promoCode && $promoCode->no_of_times > 0 && $promoCode->isValidDate()) {
                    // Check if coupon is product-specific
                    if ($promoCode->product_id && $promoCode->product_id != $item->id) {
                        // Coupon not valid for this product, skip discount
                    } else {
                        // Calculate discount
                        if ($promoCode->type == 'percentage') {
                            $discount = ($cart_total * $promoCode->discount) / 100;
                        } else {
                            $discount = $promoCode->discount;
                        }

                        // Ensure discount doesn't exceed cart total
                        if ($discount > $cart_total) {
                            $discount = $cart_total;
                        }

                        // Store discount data for order
                        $discountData = [
                            'discount' => $discount,
                            'code' => [
                                'id' => $promoCode->id,
                                'code_name' => $promoCode->code_name,
                                'title' => $promoCode->title,
                                'type' => $promoCode->type,
                                'discount' => $promoCode->discount
                            ]
                        ];

                        // Reduce coupon usage count
                        $promoCode->decrement('no_of_times');
                    }
                }
            }

            $state_price = 0; // No state tax
            $grand_total = $cart_total + $tax + $shipping_price - $discount + $state_price;

            // Calculate per-item price for cart
            // If bulk pricing, divide the total by quantity to get per-item price
            if ($isBulkPricing) {
                $perItemPrice = $bulkTotalPrice / $quantity;
                $cartMainPrice = $perItemPrice;
                $cartAttributePrice = 0; // Bulk pricing doesn't use attributes
            } else {
                $cartMainPrice = $product_price;
                $cartAttributePrice = $totalAttributePrice;
                // Ensure per-item price is defined for non-bulk orders
                $perItemPrice = $product_price + $totalAttributePrice;
            }

            // Create cart structure for order (matching normal cart structure)
            $cart = [
                $item->id => [
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'photo' => $item->photo,
                    'main_price' => $cartMainPrice,
                    'attribute_price' => $cartAttributePrice,
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
                'order_notes' => $request->order_notes ?? '', // Store order notes
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
                'discount' => json_encode($discountData),
                'shipping' => json_encode(['title' => $delivery_area_title, 'price' => $shipping_price]),
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

            // Send admin email notification
            try {
                $setting = Setting::first();
                if ($setting->order_mail == 1) {
                    // Calculate perItemPrice for email notification
                    $perItemPrice = $isBulkPricing ? ($bulkTotalPrice / $quantity) : $product_price;
                    $this->sendAdminOrderNotification($order, $item, $isBulkPricing, $bulkTotalPrice, $quantity, $cart_total, $perItemPrice);
                }
            } catch (\Exception $emailError) {
                \Log::error('Email notification failed: ' . $emailError->getMessage());
                // Continue with order creation even if email fails
            }

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

    /**
     * Send admin email notification for new order
     */
    private function sendAdminOrderNotification($order, $item, $isBulkPricing, $bulkTotalPrice, $quantity, $cart_total, $perItemPrice)
    {
        try {
            $setting = Setting::first();

            // Get customer information from shipping address
            $shippingInfo = json_decode($order->shipping_info, true);
            $customerName = $shippingInfo['ship_first_name'] . ' ' . $shippingInfo['ship_last_name'];
            $customerPhone = $shippingInfo['ship_phone'];
            $customerAddress = $shippingInfo['ship_address1'];

            // Prepare bulk pricing information
            $bulkPricingInfo = 'No bulk pricing used';
            if ($isBulkPricing) {
                $bulkPricingInfo = "Bulk pricing applied: {$quantity} units at {$setting->currency_sign}{$perItemPrice} each = {$setting->currency_sign}{$cart_total} total";
            }

            // Prepare email data
            $emailData = [
                'transaction_number' => $order->transaction_number,
                'customer_name' => trim($customerName),
                'customer_phone' => $customerPhone,
                'customer_address' => $customerAddress,
                'product_name' => $item->name,
                'quantity' => $quantity,
                'unit_price' => $setting->currency_sign . number_format($perItemPrice, 2),
                'total_price' => $setting->currency_sign . number_format($cart_total, 2),
                'payment_method' => $order->payment_method,
                'order_status' => $order->order_status,
                'bulk_pricing_info' => $bulkPricingInfo,
            ];

            // Send email using EmailHelper
            $emailHelper = new \App\Helpers\EmailHelper();
            $emailHelper->adminMail($emailData);
        } catch (\Exception $e) {
            \Log::error('Failed to send admin order notification: ' . $e->getMessage());
            // Don't throw error to prevent order creation failure
        }
    }
}

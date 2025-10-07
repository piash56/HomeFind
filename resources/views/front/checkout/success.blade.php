@extends('master.front')

@section('title')
    {{ __('Order Success') }}
@endsection

@section('content')
    @php
        $currentRoute = Route::currentRouteName();
        $hideBreadcrumbs = in_array($currentRoute, ['front.product', 'front.checkout.billing', 'front.checkout.success', 'front.order.track']);

        // Build lookup tables for category/brand from DB when not present in cart rows
        $categoryByItemId = [];
        $brandByItemId = [];
        try {
            if (isset($cart) && is_array($cart)) {
                $productIds = collect($cart)->keys()->filter(function ($id) {
                    return is_numeric($id);
                })->values();

                if ($productIds->count() > 0) {
                    // Lazy import to avoid issues if model namespaces differ
                    if (class_exists('App\\Models\\Item')) {
                        $items = App\Models\Item::with(['category:id,name', 'brand:id,name'])
                            ->whereIn('id', $productIds)->get();
                        foreach ($items as $it) {
                            $categoryByItemId[$it->id] = optional($it->category)->name ?: '';
                            $brandByItemId[$it->id] = optional($it->brand)->name ?: '';
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            // Fail silently; we'll fall back to Unknown in JS
        }
    @endphp

    {{-- Clean Purchase Tracking --}}
    <script>
        // Initialize dataLayer
        window.dataLayer = window.dataLayer || [];
        
        // Clean GA4 purchase event
        try {
            // Clear any existing ecommerce data first
            window.dataLayer.push({
                'ecommerce': null
            });
            
            // Prepare cart items
            var cartItems = [];
            @if(isset($cart) && is_array($cart) && count($cart) > 0)
                @foreach($cart as $key => $row)
                cartItems.push({
                    'item_id': @json($key ?? $row['id'] ?? $row['item_id'] ?? ''),
                    'item_name': @json($row['name'] ?? $row['item_name'] ?? 'Unknown Product'),
                    'item_category': @json($row['category'] ?? $row['item_category'] ?? $row['cat'] ?? $row['category_name'] ?? ($categoryByItemId[$key] ?? 'Unknown')),
                    'item_brand': @json($row['brand'] ?? $row['item_brand'] ?? $row['brand_name'] ?? ($brandByItemId[$key] ?? '')),
                    'item_variant': @json($row['variant'] ?? $row['item_variant'] ?? $row['size'] ?? $row['color'] ?? ''),
                    'quantity': @json($row['qty'] ?? $row['quantity'] ?? 1),
                    'price': @json(floatval($row['price'] ?? $row['item_price'] ?? $row['main_price'] ?? $row['discount_price'] ?? 0))
                });
                @endforeach
            @endif
            
            // Calculate total from cart
            var calculatedTotal = 0;
            @if(isset($cart) && is_array($cart) && count($cart) > 0)
                @foreach($cart as $key => $row)
                calculatedTotal += parseFloat(@json($row['price'] ?? $row['main_price'] ?? 0)) * parseInt(@json($row['qty'] ?? 1));
                @endforeach
            @endif
            
            // Use order total if available, otherwise use calculated total
            var orderValue = parseFloat(@json($order->total ?? $order->totalAmount ?? $order->pay_amount ?? $order->final_amount ?? 0)) || calculatedTotal;
            
            // Create transaction ID
            var transactionId = @json($order->transaction_number ?? '');
            
            // GA4 purchase event
            var purchaseEvent = {
                'event': 'purchase',
                'ecommerce': {
                    'transaction_id': transactionId,
                    'value': orderValue,
                    'currency': @json(env('CURRENCY_ISO', 'BDT')),
                    'tax': parseFloat(@json($order->tax ?? 0)),
                    'shipping': parseFloat(@json($order->shipping_cost ?? $order->shipping ?? 0)),
                    'coupon': @json($order->coupon ?? ''),
                    'items': cartItems
                },
                'eventID': transactionId
            };
            
            // Push to dataLayer
            window.dataLayer.push(purchaseEvent);
            
            // Facebook Pixel Purchase Event
            if (typeof fbq !== 'undefined') {
                fbq('track', 'Purchase', {
                    'currency': @json(env('CURRENCY_ISO', 'BDT')),
                    'value': orderValue,
                    'content_type': 'product',
                    'content_ids': cartItems.map(item => item.item_id),
                    'contents': cartItems,
                    'order_id': transactionId
                });
            }
            
        } catch (error) {
            // Fail silently - no console logs
        }
    </script>
    
    @if (!$hideBreadcrumbs)
    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="column">
                <ul class="breadcrumbs">
                    <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a> </li>
                    <li class="separator"></li>
                    <li>{{ __('Success') }}</li>
                </ul>
            </div>
        </div>
    </div>
    @endif
    <!-- Page Content-->
    <div class="container padding-bottom-3x padding-top-2x mb-1">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="card-title text-success">{{ __('ধন্যবাদ আপনাকে ওর্ডার করার জন্য') }}!</h3>
                <p class="card-text">{{ __('আপনার অর্ডার দেওয়া হয়ে গেছে এবং যত তাড়াতাড়ি সম্ভব প্রক্রিয়া শুরু হবে।') }}</p>
                <p class="card-text">{{ __('আপনার অর্ডার নম্বরটি নোট করে রাখুন, যা হল') }} <span
                        class="text-medium">{{ $order->transaction_number }}</span></p>
                <p class="card-text">{{ __('আপনার অর্ডার নিশ্চিতকরণের জন্য শীঘ্রই আপনি একটি মেসেজ পাবেন।') }}

                </p>
                <div class="padding-top-1x padding-bottom-1x">

                    <a class="btn btn-primary m-4" href="{{ route('front.index') }}"><span><i
                                class="icon-package pr-2"></i> {{ __('আমাদের আরো প্রোডাক্ট দেখুন') }}</span></a>

                </div>
            </div>
        </div>
    </div>
@endsection
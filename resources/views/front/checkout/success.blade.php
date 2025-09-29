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

    {{-- Simple Purchase Tracking Test --}}
    <script>
        // Test 1: Basic logging
        console.log('🚀 SUCCESS PAGE LOADED - BASIC TEST');
        
        // Test 2: Check if we have data
        console.log('Order ID:', @json($order->transaction_number ?? 'NO_ORDER'));
        console.log('Order Total:', @json($order->total ?? 0));
        console.log('Cart Items Count:', @json(count($cart ?? [])));
        console.log('Raw Order Data:', @json($order ?? null));
        console.log('Raw Cart Data:', @json($cart ?? null));
        
        // Test 3: Initialize dataLayer
        window.dataLayer = window.dataLayer || [];
        console.log('✅ DataLayer initialized');
        
        // Test 4: Complete GA4 purchase event with ecommerce structure
        try {
            // Clear any existing ecommerce data first
            window.dataLayer.push({
                'ecommerce': null
            });
            
            // Prepare cart items based on actual cart structure
            var cartItems = [];
            @if(isset($cart) && is_array($cart) && count($cart) > 0)
                @foreach($cart as $key => $row)
                // Debug each cart item
                console.log('Cart item @json($key):', @json($row));
                
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
            
            console.log('🛍️ Cart items prepared:', cartItems);
            
            // Debug order values - check different possible field names
            console.log('🔍 Debugging Order Total:');
            console.log('Order object keys:', @json($order ? array_keys($order->toArray()) : []));
            console.log('Order total raw:', @json($order->total ?? 'NOT_FOUND'));
            console.log('Order totalAmount:', @json($order->totalAmount ?? 'NOT_FOUND'));
            console.log('Order pay_amount:', @json($order->pay_amount ?? 'NOT_FOUND'));
            console.log('Order final_amount:', @json($order->final_amount ?? 'NOT_FOUND'));
            
            // Calculate total from cart if order total is missing
            var calculatedTotal = 0;
            @if(isset($cart) && is_array($cart) && count($cart) > 0)
                @foreach($cart as $key => $row)
                calculatedTotal += parseFloat(@json($row['price'] ?? $row['main_price'] ?? 0)) * parseInt(@json($row['qty'] ?? 1));
                @endforeach
            @endif
            console.log('Calculated total from cart:', calculatedTotal);
            
            // Determine the best value for order total
            var orderValue = parseFloat(@json($order->total ?? $order->totalAmount ?? $order->pay_amount ?? $order->final_amount ?? 0)) || calculatedTotal;
            
            console.log('🏷️ Final order value used:', orderValue);
            
            // Complete GA4 purchase event
            var purchaseEvent = {
                'event': 'purchase',
                'ecommerce': {
                    'transaction_id': @json($order->transaction_number ?? ''),
                    'value': orderValue,
                    'currency': @json(env('CURRENCY_ISO', 'BDT')),
                    'tax': parseFloat(@json($order->tax ?? 0)),
                    'shipping': parseFloat(@json($order->shipping_cost ?? $order->shipping ?? 0)),
                    'coupon': @json($order->coupon ?? ''),
                    'items': cartItems
                }
            };
            
            console.log('📦 Complete purchase event data:', purchaseEvent);
            
            // Push to dataLayer
            window.dataLayer.push(purchaseEvent);
            
            console.log('✅ GA4 purchase event pushed to dataLayer successfully!');
            
            // Facebook Pixel Purchase Event
            if (typeof fbq !== 'undefined') {
                console.log('📘 Firing Facebook Pixel Purchase Event');
                
                var contentIds = [];
                var contents = [];
                
                @if(isset($cart) && is_array($cart) && count($cart) > 0)
                    @foreach($cart as $key => $row)
                    contentIds.push(@json($key ?? $row['id'] ?? $row['item_id'] ?? ''));
                    contents.push({
                        'id': @json($key ?? $row['id'] ?? $row['item_id'] ?? ''),
                        'quantity': @json($row['qty'] ?? $row['quantity'] ?? 1),
                        'item_price': parseFloat(@json($row['price'] ?? $row['item_price'] ?? $row['main_price'] ?? $row['discount_price'] ?? 0))
                    });
            @endforeach
            @endif
            
                fbq('track', 'Purchase', {
                    value: orderValue,
                    currency: @json(env('CURRENCY_ISO', 'BDT')),
                    content_type: 'product',
                    content_ids: contentIds,
                    contents: contents
                }, {
                    eventID: @json($order->transaction_number ?? $order->id ?? uniqid('order_'))
                });
                
                console.log('✅ Facebook Pixel Purchase event sent');
            } else {
                console.log('⚠️ Facebook Pixel (fbq) not found');
            }
            
            console.log('📊 Final dataLayer:', window.dataLayer);
            
        } catch (error) {
            console.error('❌ Error in purchase event:', error);
        }
        
        console.log('🎯 Script completed - check if Custom Event trigger fires now!');
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
                <h3 class="card-title text-success">{{ __('Thank you for your order') }}!</h3>
                <p class="card-text">{{ __('Your order has been placed and will be processed as soon as possible.') }}</p>
                <p class="card-text">{{ __('Make sure you make note of your order number, which is') }} <span
                        class="text-medium">{{ $order->transaction_number }}</span></p>
                <p class="card-text">{{ __('You will be receiving an email shortly with confirmation of your order.') }}

                </p>
                <div class="padding-top-1x padding-bottom-1x">

                    <a class="btn btn-primary m-4" href="{{ route('front.index') }}"><span><i
                                class="icon-package pr-2"></i> {{ __('View our products again') }}</span></a>

                </div>
            </div>
        </div>
    </div>
@endsection
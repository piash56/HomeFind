@extends('master.front')

@section('title')
    {{ __('Order Success') }}
@endsection

@section('content')
    @php
        $currentRoute = Route::currentRouteName();
        $hideBreadcrumbs = in_array($currentRoute, ['front.product', 'front.checkout.billing', 'front.checkout.success', 'front.order.track']);
    @endphp

    {{-- Comprehensive Ecommerce dataLayer for GTM Purchase Event --}}
    <script>
        window.dataLayer = window.dataLayer || [];
        
        @php
            $user = Auth::user();
            $billing = json_decode($order->billing_info ?? '{}', true) ?: [];
            $shipping = json_decode($order->shipping_info ?? '{}', true) ?: [];
            
            // Calculate totals
            $subtotal = 0;
            $tax = 0;
            $shippingCost = $order->shipping_cost ?? 0;
            $discount = $order->discount ?? 0;
            
            foreach($cart as $row) {
                $subtotal += ($row['item']['discount_price'] ?? $row['item']['price']) * $row['qty'];
            }
            
            $customerData = [];
            if($user) {
                $customerData = [
                    'customerTotalOrders' => $user->orders()->count(),
                    'customerTotalOrderValue' => $user->orders()->sum('total'),
                    'customerFirstName' => $user->first_name ?? '',
                    'customerLastName' => $user->last_name ?? '',
                    'customerBillingFirstName' => $billing['bill_first_name'] ?? $user->first_name ?? '',
                    'customerBillingLastName' => $billing['bill_last_name'] ?? $user->last_name ?? '',
                    'customerBillingCompany' => $billing['bill_company'] ?? '',
                    'customerBillingAddress1' => $billing['bill_address1'] ?? '',
                    'customerBillingAddress2' => $billing['bill_address2'] ?? '',
                    'customerBillingCity' => $billing['bill_city'] ?? '',
                    'customerBillingState' => $billing['bill_state'] ?? '',
                    'customerBillingPostcode' => $billing['bill_zip'] ?? '',
                    'customerBillingCountry' => $billing['bill_country'] ?? '',
                    'customerBillingEmail' => $billing['bill_email'] ?? $user->email ?? '',
                    'customerBillingEmailHash' => $billing['bill_email'] ? hash('sha256', strtolower(trim($billing['bill_email']))) : '',
                    'customerBillingPhone' => $billing['bill_phone'] ?? '',
                    'customerShippingFirstName' => $shipping['ship_first_name'] ?? '',
                    'customerShippingLastName' => $shipping['ship_last_name'] ?? '',
                    'customerShippingCompany' => $shipping['ship_company'] ?? '',
                    'customerShippingAddress1' => $shipping['ship_address1'] ?? '',
                    'customerShippingAddress2' => $shipping['ship_address2'] ?? '',
                    'customerShippingCity' => $shipping['ship_city'] ?? '',
                    'customerShippingState' => $shipping['ship_state'] ?? '',
                    'customerShippingPostcode' => $shipping['ship_zip'] ?? '',
                    'customerShippingCountry' => $shipping['ship_country'] ?? ''
                ];
            }
        @endphp
        
        window.dataLayer.push({
            // Page information
            'pagePostType': 'purchase',
            'pagePostType2': 'checkout-success',
            'pagePostAuthor': 'admin',
            
            // Customer data
            @if($user)
            @foreach($customerData as $key => $value)
            '{{ $key }}': {!! is_numeric($value) ? $value : "'" . addslashes($value) . "'" !!},
            @endforeach
            @endif
            
            // Order information
            'orderId': '{{ $order->transaction_number }}',
            'orderDate': '{{ $order->created_at->format('Y-m-d H:i:s') }}',
            'orderStatus': '{{ $order->order_status }}',
            'paymentMethod': '{{ $order->payment_method }}',
            'shippingMethod': '{{ $order->shipping_method }}',
            
            // Cart information
            'cartContent': {
                'totals': {
                    'applied_coupons': [],
                    'discount_total': {{ $discount }},
                    'subtotal': {{ $subtotal }},
                    'tax': {{ $tax }},
                    'shipping': {{ $shippingCost }},
                    'total': {{ $order->total }}
                },
                'items': {!! json_encode(array_map(function($row) {
                    return [
                        'id' => $row['item']['id'],
                        'name' => $row['item']['name'],
                        'category' => $row['item']['category']['name'] ?? '',
                        'quantity' => $row['qty'],
                        'price' => $row['item']['discount_price'] ?? $row['item']['price']
                    ];
                }, $cart)) !!}
            },
            
            // Enhanced ecommerce tracking
            'ecommerce': {
                'purchase': {
                    'actionField': {
                        'id': '{{ $order->transaction_number }}',
                        'revenue': {{ $order->total }},
                        'currency': '{{ env('CURRENCY_ISO', 'BDT') }}',
                        'tax': {{ $tax }},
                        'shipping': {{ $shippingCost }},
                        'coupon': ''
                    },
                    'products': [
                        @foreach($cart as $row)
                        {
                            'id': '{{ $row['item']['id'] }}',
                            'name': '{{ addslashes($row['item']['name']) }}',
                            'category': '{{ addslashes($row['item']['category']['name'] ?? '') }}',
                            'brand': '{{ addslashes($row['item']['brand']['name'] ?? '') }}',
                            'variant': '{{ addslashes($row['item']['is_type'] ?? '') }}',
                            'quantity': {{ $row['qty'] }},
                            'price': {{ $row['item']['discount_price'] ?? $row['item']['price'] }}
                        }{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ]
                }
            },
            'event': 'purchase'
        });
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

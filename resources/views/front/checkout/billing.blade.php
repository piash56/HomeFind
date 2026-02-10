@extends('master.front')
@section('title')
    {{ __('Checkout') }}
@endsection
@section('content')
<style>
    .product-list-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
        gap: 15px;
    }
    .product-list-item:last-child {
        border-bottom: none;
    }
    .product-list-thumb {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
    }
    .product-list-details {
        flex: 1;
    }
    .product-list-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }
    .product-list-attributes {
        font-size: 14px;
        color: #333;
        margin-bottom: 8px;
        margin-top: 5px;
    }
    .product-list-attributes span {
        display: inline-block;
        margin-right: 10px;
        padding: 3px 8px;
        background: #f5f5f5;
        border-radius: 4px;
        font-size: 13px;
    }
    .product-list-attributes em {
        font-style: normal;
        font-weight: 600;
        color: #555;
    }
    .product-list-price {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .product-list-old-price {
        text-decoration: line-through;
        color: #999;
        font-size: 14px;
    }
    .product-list-current-price {
        color: #4E65FF;
        font-weight: 600;
        font-size: 16px;
    }
    .product-list-qty {
        color: #666;
        font-size: 14px;
    }
    .delivery-option-item {
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fff;
    }
    .delivery-option-item:hover {
        border-color: #4E65FF;
        background: #f8f9ff;
    }
    .delivery-option-item input[type="radio"]:checked + span {
        color: #4E65FF;
        font-weight: 600;
    }
    .delivery-option-item:has(input[type="radio"]:checked) {
        border-color: #4E65FF;
        background: #f8f9ff;
    }
    .checkout-btn {
        background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%);
        border: 2px solid #DD2476;
        padding: 12px 20px;
        font-size: 18px;
        font-weight: bold;
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3);
        transition: all 0.3s ease;
    }
    .checkout-btn:hover,
    .checkout-btn:active,
    .checkout-btn:focus {
        background: transparent !important;
        background-image: none !important;
        border: 2px solid #DD2476 !important;
        color: #DD2476 !important;
        box-shadow: none !important;
        transform: none;
    }
    .checkout-btn:hover i,
    .checkout-btn:active i,
    .checkout-btn:focus i {
        color: #DD2476 !important;
    }
    .checkout-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%) !important;
        color: white !important;
        border: 2px solid #DD2476 !important;
    }
    .checkout-btn:disabled i {
        color: white !important;
    }
</style>

<div class="container padding-bottom-3x padding-top-2x mb-1">
    <h1 class="mb-4">{{ __('Checkout') }}</h1>
    
    <form id="checkoutForm" action="{{ route('front.checkout.billing.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Left Column - Products & Billing Info -->
            <div class="col-xl-8 col-lg-8">
                <!-- Products List -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('অর্ডার পণ্য') }}</h5>
                        <div class="products-list">
                            @foreach($cart as $key => $item)
                            <div class="product-list-item">
                                <img src="{{ asset('storage/images/' . $item['photo']) }}" alt="{{ $item['name'] }}" class="product-list-thumb">
                                <div class="product-list-details">
                                    <div class="product-list-title">{{ $item['name'] }}</div>
                                    @if(isset($item['attribute']) && !empty($item['attribute']['option_name']) && is_array($item['attribute']['option_name']) && count($item['attribute']['option_name']) > 0)
                                        <div class="product-list-attributes">
                                            @foreach($item['attribute']['option_name'] as $optionkey => $option_name)
                                                @if(!empty($option_name))
                                                <span>
                                                    <em>{{ isset($item['attribute']['names'][$optionkey]) ? $item['attribute']['names'][$optionkey] : 'Option' }}:</em> {{ $option_name }}
                                                </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="product-list-price">
                                        @if(!empty($item['previous_price']) && (float)$item['previous_price'] > 0)
                                            <span class="product-list-old-price">{{ PriceHelper::setPreviousPrice($item['previous_price']) }}</span>
                                        @endif
                                        <span class="product-list-current-price">{{ PriceHelper::setCurrencyPrice($item['main_price']) }}</span>
                                        <span class="product-list-qty">x {{ $item['qty'] }}</span>
                                        <span class="ms-auto fw-bold">{{ PriceHelper::setCurrencyPrice($item['main_price'] * $item['qty']) }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Billing Information -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('ডেলিভারি তথ্য') }}</h5>
                        
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="bill_first_name">{{ __('নাম') }} <span class="text-danger">*</span></label>
                                    <input class="form-control" name="bill_first_name" type="text" required
                                        id="bill_first_name" value="{{ isset($user) && $user ? $user->first_name . ' ' . $user->last_name : '' }}" placeholder="আপনার পুরো নাম লিখুন">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="bill_phone">{{ __('ফোন নাম্বার') }} <span class="text-danger">*</span></label>
                                    <input class="form-control" name="bill_phone" type="text" required
                                        id="bill_phone" value="{{ isset($user) && $user ? $user->phone : '' }}" placeholder="আপনার ফোন নম্বর লিখুন">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="bill_address1">{{ __('ঠিকানা') }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="bill_address1" required
                                        id="bill_address1" rows="3" placeholder="আপনার ডেলিভারি ঠিকানা লিখুন">{{ isset($user) && $user ? $user->bill_address1 : '' }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Delivery Area Options -->
                            @php
                                $hasFreeDelivery = false;
                                if (Session::has('coupon')) {
                                    $couponData = Session::get('coupon');
                                    if (isset($couponData['code']) && is_object($couponData['code'])) {
                                        $hasFreeDelivery = $couponData['code']->is_free_delivery ?? false;
                                    }
                                }
                            @endphp
                            
                            @if($hasFreeDelivery)
                                <!-- Free Delivery Applied Message -->
                                <div class="col-sm-12">
                                    <div class="alert alert-success mb-3">
                                        <i class="fas fa-shipping-fast"></i> 
                                        <strong>{{ __('Free Delivery Applied!') }}</strong>
                                        <p class="mb-0 mt-1">{{ __('You have qualified for free delivery on this order.') }}</p>
                                    </div>
                                    <input type="hidden" name="delivery_area" value="free_delivery">
                                </div>
                            @else
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label class="mb-2">{{ __('আপনার ডেলিভারি এরিয়া') }} <span class="text-danger">*</span></label>
                                    <div class="delivery-options-container" style="display: flex; flex-direction: column; gap: 10px;">
                                        <div class="delivery-option-item">
                                            <label style="display: flex; align-items: center; cursor: pointer; margin: 0; font-weight: 500;">
                                                <input type="radio" name="delivery_area" value="inside_dhaka" id="delivery_inside_dhaka" class="delivery-area-radio me-2" style="width: 18px; height: 18px; cursor: pointer;" required>
                                                <span>{{ __('Inside Dhaka') }} - {{ PriceHelper::setCurrencyPrice(70) }}</span>
                                            </label>
                                        </div>
                                        <div class="delivery-option-item">
                                            <label style="display: flex; align-items: center; cursor: pointer; margin: 0; font-weight: 500;">
                                                <input type="radio" name="delivery_area" value="outside_dhaka" id="delivery_outside_dhaka" class="delivery-area-radio me-2" style="width: 18px; height: 18px; cursor: pointer;">
                                                <span>{{ __('Outside Dhaka') }} - {{ PriceHelper::setCurrencyPrice(130) }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <small class="form-text text-danger d-none mt-2" id="delivery-area-error">{{ __('অনুগ্রহ করে ডেলিভারি এরিয়া নির্বাচন করুন') }}</small>
                                </div>
                            </div>
                            @endif

                            <!-- Notes (Optional) -->
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="order_notes">{{ __('অর্ডার নোট (ঐচ্ছিক)') }}</label>
                                    <textarea class="form-control" name="order_notes" id="order_notes" rows="3" placeholder="বিশেষ প্যাকিং বা অতিরিক্ত কিছুর প্রয়োজন হলে এখানে লিখুন..."></textarea>
                                    <small class="text-muted">{{ __('যেমন: উপহার হিসেবে মোড়ানো, বিশেষ সময়ে ডেলিভারি ইত্যাদি।') }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden fields -->
                        <input type="hidden" name="bill_email" value="{{ isset($user) && $user ? $user->email : 'customer@example.com' }}">
                        <input type="hidden" name="bill_last_name" value="">
                        <input type="hidden" name="bill_company" value="">
                        <input type="hidden" name="bill_address2" value="">
                        <input type="hidden" name="bill_zip" value="">
                        <input type="hidden" name="bill_city" value="Dhaka">
                        <input type="hidden" name="bill_country" value="Bangladesh">
                        <input type="hidden" name="payment_method" value="Cash On Delivery">
                        <input type="hidden" id="inside-dhaka-fee" value="70">
                        <input type="hidden" id="outside-dhaka-fee" value="130">
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="col-xl-4 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('অর্ডার সংক্ষেপ') }}</h5>
                        
                        <table class="table">
                            <tr>
                                <td>{{ __('পণ্যের মূল্য') }}:</td>
                                <td class="text-end fw-bold" id="cart-subtotal">{{ PriceHelper::setCurrencyPrice($cart_total) }}</td>
                            </tr>
                            @if($discount)
                            <tr>
                                <td>{{ __('ডিসকাউন্ট') }}:</td>
                                <td class="text-end fw-bold">
                                    @php
                                        $isFreeDeliveryCoupon = false;
                                        if (Session::has('coupon')) {
                                            $couponData = Session::get('coupon');
                                            if (isset($couponData['code']) && is_object($couponData['code'])) {
                                                $isFreeDeliveryCoupon = $couponData['code']->is_free_delivery ?? false;
                                            }
                                        }
                                    @endphp
                                    
                                    @if($isFreeDeliveryCoupon)
                                        <span class="text-success">
                                            <i class="fas fa-shipping-fast"></i> {{ __('Free Delivery') }}
                                        </span>
                                    @else
                                        <span class="text-danger">-{{ PriceHelper::setCurrencyPrice($discount['discount']) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td>{{ __('ডেলিভারি ফি') }}:</td>
                                <td class="text-end fw-bold" id="delivery-fee-display">{{ PriceHelper::setCurrencyPrice(0) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td class="h5 mb-0">{{ __('মোট টাকা') }}:</td>
                                <td class="text-end h5 mb-0 text-primary fw-bold" id="order-total">{{ PriceHelper::setCurrencyPrice($grand_total) }}</td>
                            </tr>
                        </table>

                        <button type="submit" id="checkout-btn" class="w-100 checkout-btn mt-3">
                            <i class="fas fa-shopping-cart me-2"></i>
                            <span>{{ __('অর্ডার সম্পন্ন করুন') }}</span>
                        </button>

                        <div class="alert alert-danger mt-3 mb-0" style="font-size: 13px;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>{{ __('সাবধান!') }}</strong>
                            {{ __('ফেক অর্ডার করলে বা ফোনে অর্ডার কনফার্ম করার পরেও পার্সেল রিসিভ না করলে আইনিগত ব্যবস্থা নেওয়া হবে') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('checkoutForm');
        const deliveryRadios = document.querySelectorAll('input[name="delivery_area"]');
        const insideDhakaFee = parseFloat(document.getElementById('inside-dhaka-fee').value) || 70;
        const outsideDhakaFee = parseFloat(document.getElementById('outside-dhaka-fee').value) || 130;
        const cartSubtotal = {{ $cart_total - ($discount ? $discount['discount'] : 0) }};
        const currencySign = '{{ PriceHelper::setCurrencySign() }}';
        const currencyDirection = {{ isset($setting) ? $setting->currency_direction : 0 }};

        // Update delivery fee and total when delivery area changes
        deliveryRadios.forEach(radio => {
            radio.addEventListener('change', updateTotal);
        });

        function updateTotal() {
            let deliveryFee = 0;
            const selectedDelivery = document.querySelector('input[name="delivery_area"]:checked');
            
            if (selectedDelivery) {
                if (selectedDelivery.value === 'free_delivery') {
                    deliveryFee = 0;
                } else if (selectedDelivery.value === 'inside_dhaka') {
                    deliveryFee = insideDhakaFee;
                } else {
                    deliveryFee = outsideDhakaFee;
                }
                
                const errorElement = document.getElementById('delivery-area-error');
                if (errorElement) {
                    errorElement.classList.add('d-none');
                }
            }

            const total = cartSubtotal + deliveryFee;
            
            // Format prices
            const formattedDeliveryFee = formatPrice(deliveryFee);
            const formattedTotal = formatPrice(total);
            
            document.getElementById('delivery-fee-display').textContent = formattedDeliveryFee;
            document.getElementById('order-total').textContent = formattedTotal;
        }

        function formatPrice(price) {
            const formatted = price.toFixed(2);
            return currencyDirection == 1 ? currencySign + formatted : formatted + currencySign;
        }

        // Form validation
        form.addEventListener('submit', function(e) {
            const selectedDelivery = document.querySelector('input[name="delivery_area"]:checked');
            const hasFreeDelivery = selectedDelivery && selectedDelivery.value === 'free_delivery';
            
            if (!selectedDelivery && !hasFreeDelivery) {
                const deliveryContainer = document.querySelector('.delivery-options-container');
                if (deliveryContainer) {
                    e.preventDefault();
                    document.getElementById('delivery-area-error').classList.remove('d-none');
                    deliveryContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
            }

            // Disable button and show loading
            const btn = document.getElementById('checkout-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><span>প্রক্রিয়াকরণ চলছে...</span>';
        });

        // Initialize with default selection if needed
        if (deliveryRadios.length > 0 && !document.querySelector('input[name="delivery_area"]:checked')) {
            // Don't auto-select, let user choose
        }
        
        // If free delivery is active, set initial fee to 0
        const freeDeliveryInput = document.querySelector('input[name="delivery_area"][value="free_delivery"]');
        if (freeDeliveryInput) {
            document.getElementById('delivery-fee-display').textContent = formatPrice(0);
            const total = cartSubtotal + 0;
            document.getElementById('order-total').textContent = formatPrice(total);
        }
    });
</script>

@endsection

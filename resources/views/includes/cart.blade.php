@php
    $cart = Session::has('cart') ? Session::get('cart') : [];
    $total = 0;
    $option_price = 0;
    $cartTotal = 0;
    
@endphp

<style>
/* Mobile responsive cart styles */
@media (max-width: 768px) {
    .shopping-cart table {
        border: 0;
    }
    
    .shopping-cart table thead {
        display: none;
    }
    
    .shopping-cart table tbody tr {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        background: #fff;
        overflow: hidden;
        padding: 15px;
    }
    
    .shopping-cart table tbody td {
        border: none;
        padding: 0;
    }
    
    .shopping-cart table tbody td:before {
        display: none;
    }
    
    /* Product section - Image (left) + Name (right) */
    .shopping-cart table tbody td:nth-child(1) {
        width: 100%;
        order: 1;
        margin-bottom: 12px;
    }
    
    .shopping-cart .product-item {
        display: flex !important;
        align-items: flex-start;
        gap: 12px;
    }
    
    .shopping-cart .product-thumb {
        width: 60px !important;
        height: 60px !important;
        flex-shrink: 0;
        display: block !important;
    }
    
    .shopping-cart .product-thumb img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
        border-radius: 8px;
        display: block !important;
    }
    
    .shopping-cart .product-info {
        flex: 1;
        min-width: 0;
    }
    
    .shopping-cart .product-title {
        font-size: 14px;
        margin-bottom: 5px;
        line-height: 1.3;
        font-weight: 500;
    }
    
    .shopping-cart .product-title a {
        color: #333;
        text-decoration: none;
    }
    
    .shopping-cart .cart-item-attributes {
        font-size: 11px;
        line-height: 1.4;
        color: #666;
    }
    
    /* Price section */
    .shopping-cart table tbody td:nth-child(2) {
        width: 32%;
        order: 2;
        padding-right: 8px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    
    .shopping-cart table tbody td:nth-child(2):before {
        content: 'PRICE';
        display: block;
        font-size: 10px;
        font-weight: 600;
        color: #666;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .shopping-cart td:nth-child(2) .text-dark {
        display: block;
        font-size: 15px;
        font-weight: 700;
        color: #000;
    }
    
    .shopping-cart td:nth-child(2) small {
        font-size: 11px;
        display: block;
        margin-bottom: 2px;
    }
    
    /* Quantity section */
    .shopping-cart table tbody td:nth-child(3) {
        width: 48%;
        order: 3;
        padding: 0 5px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .shopping-cart table tbody td:nth-child(3):before {
        content: 'QUANTITY';
        display: block;
        font-size: 10px;
        font-weight: 600;
        color: #666;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: center;
    }
    
    .shopping-cart .qtySelector {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }
    
    .shopping-cart .qtySelector span {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #4E65FF;
        color: white;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .shopping-cart .qtySelector span:active {
        transform: scale(0.95);
    }
    
    .shopping-cart .qtySelector input {
        width: 45px;
        height: 32px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 15px;
        font-weight: 600;
    }
    
    /* Remove button - Right of quantity */
    .shopping-cart table tbody td:nth-child(5) {
        width: 20%;
        order: 4;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: flex-start;
        padding-left: 5px;
    }
    
    .shopping-cart table tbody td:nth-child(5):before {
        content: '';
        display: block;
        height: 10px;
        margin-bottom: 6px;
    }
    
    .shopping-cart .remove-from-cart {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        background: #dc3545;
        color: white !important;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    
    .shopping-cart .remove-from-cart:hover {
        background: #c82333;
    }
    
    .shopping-cart .remove-from-cart:active {
        transform: scale(0.95);
    }
    
    .shopping-cart .remove-from-cart i {
        margin: 0;
        font-size: 18px;
    }
    
    .shopping-cart .remove-from-cart span {
        display: none;
    }
    
    /* Subtotal section - Below everything */
    .shopping-cart table tbody td:nth-child(4) {
        width: 100%;
        order: 5;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e0e0e0;
        text-align: left;
    }
    
    .shopping-cart table tbody td:nth-child(4):before {
        content: 'SUBTOTAL';
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        color: #666;
        margin-right: 10px;
        text-transform: uppercase;
    }
    
    .shopping-cart td:nth-child(4) {
        font-size: 16px;
        font-weight: 700;
        color: #2193b0;
    }
    
    /* Clear cart button on mobile */
    .clear-cart-btn {
        width: 100%;
        margin-top: 10px;
        padding: 10px;
    }
}

/* Desktop adjustments */
@media (min-width: 769px) {
    .shopping-cart .product-thumb {
        width: 100px;
        height: 100px;
    }
    
    .shopping-cart .product-thumb img {
        border-radius: 8px;
    }
}

/* Mobile cart footer */
@media (max-width: 768px) {
    .shopping-cart-footer {
        flex-direction: column !important;
        gap: 15px;
    }
    
    .shopping-cart-footer .column {
        width: 100% !important;
        margin: 0 !important;
    }
    
    /* Coupon form mobile styling */
    .shopping-cart-footer .coupon-form {
        display: flex !important;
        flex-direction: row !important;
        gap: 10px;
        width: 100%;
        align-items: stretch;
    }
    
    .shopping-cart-footer .coupon-form input[type="text"],
    .shopping-cart-footer .coupon-form input.form-control,
    .shopping-cart-footer .coupon-form input[name="code"] {
        flex: 1 !important;
        min-width: 0 !important;
        width: auto !important;
        height: 48px !important;
        font-size: 15px !important;
        padding: 12px 15px !important;
        border: 2px solid #e0e0e0 !important;
        border-radius: 8px !important;
        background: #fff !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .shopping-cart-footer .coupon-form input[type="text"]:focus,
    .shopping-cart-footer .coupon-form input.form-control:focus,
    .shopping-cart-footer .coupon-form input[name="code"]:focus {
        border-color: #4E65FF !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(78, 101, 255, 0.1) !important;
    }
    
    .shopping-cart-footer .coupon-form button,
    .shopping-cart-footer .coupon-form .btn {
        white-space: nowrap !important;
        flex-shrink: 0 !important;
        height: 48px !important;
        padding: 12px 20px !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
        background: linear-gradient(135deg, #4E65FF 0%, #2193b0 100%) !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(78, 101, 255, 0.3) !important;
        transition: all 0.3s ease !important;
        width: auto !important;
    }
    
    .shopping-cart-footer .coupon-form button:active,
    .shopping-cart-footer .coupon-form .btn:active {
        transform: scale(0.98);
    }
    
    /* Discount display on mobile */
    .shopping-cart-footer .text-lg {
        font-size: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 10px;
        border: 2px solid #e0e0e0;
    }
    
    .shopping-cart-footer .text-lg .text-muted {
        display: block;
        margin-bottom: 5px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .shopping-cart-footer .text-lg .text-gray-dark {
        font-size: 18px;
        font-weight: 700;
        color: #2193b0;
    }
    
    .shopping-cart-footer .text-right {
        text-align: center !important;
    }
    
    /* Remove coupon button */
    .shopping-cart-footer .remove-from-cart.btn-danger {
        margin-left: 10px;
        padding: 8px 12px;
        border-radius: 6px;
    }
    
    /* Action buttons */
    .shopping-cart-footer .btn:not(.coupon-form button):not(.btn-danger) {
        width: 100%;
        justify-content: center;
        display: flex;
        align-items: center;
        height: 50px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 10px;
    }
    
    .shopping-cart-footer .checkout-btn {
        width: 100%;
        text-align: center;
        display: block;
        height: 50px;
        font-size: 16px;
    }
}
</style>

<div class="card border-0">
    <div class="card-body">
        <div class="table-responsive shopping-cart">
            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>{{ __('Product Name') }}</th>
                        <th>{{ __('Product Price') }}</th>
                        <th class="text-center">{{ __('Quantity') }}</th>
                        <th class="text-center">{{ __('Subtotal') }}</th>
                        <th class="text-center"><a class="btn btn-sm btn-primary clear-cart-btn"
                                href="{{ route('front.cart.clear') }}"><span>{{ __('Clear Cart') }}</span></a></th>
                    </tr>
                </thead>

                <tbody id="cart_view_load" data-target="{{ route('cart.get.load') }}">

                    @foreach ($cart as $key => $item)
                        @php
                            // Use main_price directly as it already includes the final calculated price
                            $cartTotal += $item['main_price'] * $item['qty'];
                        @endphp
                        <tr>
                            <td data-label="{{ __('Product') }}">
                                <div class="product-item"><a class="product-thumb" style="padding: 0 !important;"
                                        href="{{ route('front.product', $item['slug']) }}"><img
                                            src="{{ asset('storage/images/' . $item['photo']) }}" alt="Product"></a>
                                    <div class="product-info">
                                        <h4 class="product-title"><a href="{{ route('front.product', $item['slug']) }}">
                                                {{ Str::limit($item['name'], 45) }}
                                            </a></h4>
                                        @if(!empty($item['attribute']['option_name']) && is_array($item['attribute']['option_name']))
                                            <div class="cart-item-attributes text-muted small mt-1">
                                                @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                                                    <span class="d-block">
                                                        <em>{{ $item['attribute']['names'][$optionkey] ?? '' }}:</em>
                                                        {{ $option_name }}
                                                        {{-- @if(!empty($item['attribute']['option_price'][$optionkey]))
                                                            <span class="text-muted">({{ PriceHelper::setCurrencyPrice($item['attribute']['option_price'][$optionkey]) }})</span>
                                                        @endif --}}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center text-lg" data-label="{{ __('Price') }}">
                                @if(!empty($item['previous_price']) && (float)$item['previous_price'] > 0)
                                    <small class="d-block text-muted"><del>{{ PriceHelper::setPreviousPrice($item['previous_price']) }}</del></small>
                                @endif
                                <span class="text-dark">{{ PriceHelper::setCurrencyPrice($item['main_price']) }}</span>
                            </td>

                            <td class="text-center" data-label="{{ __('Quantity') }}">
                                @if ($item['item_type'] == 'normal')
                                    <div class="qtySelector product-quantity">
                                        <span class="decreaseQtycart cartsubclick" data-id="{{ $key }}"
                                            data-target="{{ PriceHelper::GetItemId($key) }}"><i
                                                class="fas fa-minus"></i></span>
                                        <input type="text" disabled class="qtyValue cartcart-amount"
                                            value="{{ $item['qty'] }}">
                                        <span class="increaseQtycart cartaddclick" data-id="{{ $key }}"
                                            data-target="{{ PriceHelper::GetItemId($key) }}"
                                            data-item="{{ implode(',', $item['options_id']) }}"><i
                                                class="fas fa-plus"></i></span>
                                        <input type="hidden" value="3333" id="current_stock">
                                    </div>
                                @endif

                            </td>
                            <td class="text-center text-lg" data-label="{{ __('Subtotal') }}">
                                {{ PriceHelper::setCurrencyPrice($item['main_price'] * $item['qty']) }}</td>

                            <td class="text-center"><a class="remove-from-cart"
                                    href="{{ route('front.cart.destroy', $key) }}" data-toggle="tooltip"
                                    title="{{ __('Remove item') }}"><i class="icon-x"></i><span class="d-none d-md-inline"> {{ __('Remove') }}</span></a></td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="card border-0 mt-4">
    <div class="card-body">
        <div class="shopping-cart-footer">
            <div class="column">
                @php
                    // Check for active free delivery coupon
                    $freeDeliveryCoupon = \App\Models\PromoCode::where('is_free_delivery', 1)
                        ->where('status', 1)
                        ->where('no_of_times', '>', 0)
                        ->first();
                    
                    $showFreeDeliveryButton = false;
                    if ($freeDeliveryCoupon && $freeDeliveryCoupon->isValidDate()) {
                        $minimumAmount = $freeDeliveryCoupon->minimum_order_amount ?? 900;
                        if ($cartTotal >= $minimumAmount) {
                            $showFreeDeliveryButton = true;
                        }
                    }
                @endphp
                
                @if($showFreeDeliveryButton && !Session::has('coupon'))
                    <div class="alert alert-success mb-3" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: 2px solid #28a745; border-radius: 10px; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);">
                        <div class="d-flex align-items-center justify-content-between">
                            <div style="flex: 1;">
                                <h6 class="mb-2" style="color: #155724; font-weight: bold;">
                                    <i class="fas fa-gift"></i> {{ __('Congratulations! Free Delivery Available!') }}
                                </h6>
                                <p class="mb-2" style="color: #155724; font-size: 14px;">
                                    {{ __('Use this coupon code:') }}
                                </p>
                                <div class="d-flex align-items-center gap-2">
                                    <code id="free_delivery_code" style="background: white; color: #28a745; padding: 8px 12px; border-radius: 6px; font-size: 16px; font-weight: bold; border: 2px dashed #28a745; cursor: pointer;" 
                                        data-code="{{ $freeDeliveryCoupon->code_name }}" 
                                        title="{{ __('Click to copy and apply') }}">
                                        {{ $freeDeliveryCoupon->code_name }}
                                    </code>
                                    <button type="button" class="btn btn-sm btn-success" id="copy_and_apply_btn" 
                                        data-code="{{ $freeDeliveryCoupon->code_name }}"
                                        style="padding: 8px 15px; border-radius: 6px; font-weight: bold;">
                                        <i class="fas fa-copy"></i> {{ __('Copy & Apply') }}
                                    </button>
                                </div>
                            </div>
                            <div style="font-size: 48px; color: #28a745; opacity: 0.3;">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                        </div>
                    </div>
                @endif
                
                <form class="coupon-form" method="post" id="coupon_form" action="{{ route('front.promo.submit') }}">
                    @csrf
                    <input class="form-control form-control-sm" name="code" type="text"
                        placeholder="{{ __('Coupon code') }}" required>
                    <button class="btn-primary btn-sm"
                        type="submit"><span>{{ __('Apply Coupon') }}</span></button>
                </form>
            </div>

            <div class="text-right text-lg column {{ Session::has('coupon') ? '' : 'd-none' }}" style="margin-bottom: 5px !important;">
                @php
                    $isFreeDeliveryCoupon = false;
                    if (Session::has('coupon')) {
                        $couponData = Session::get('coupon');
                        if (isset($couponData['code']) && is_object($couponData['code'])) {
                            $isFreeDeliveryCoupon = $couponData['code']->is_free_delivery ?? false;
                        }
                    }
                @endphp
                
                <span class="text-muted">{{ __('Discount') }}
                    ({{ Session::has('coupon') ? Session::get('coupon')['code']['title'] : '' }}) : </span>
                
                @if($isFreeDeliveryCoupon)
                    <span class="text-success" style="font-weight: bold;">
                        <i class="fas fa-shipping-fast"></i> {{ __('Free Delivery') }}
                    </span>
                @else
                    <span class="text-gray-dark">{{ PriceHelper::setCurrencyPrice(Session::has('coupon') ? Session::get('coupon')['discount'] : 0) }}</span>
                @endif
                
                <a class="remove-from-cart btn btn-danger btn-sm "
                    href="{{ route('front.promo.destroy') }}" data-toggle="tooltip"
                    title="Remove item"><i class="icon-x"></i></a>
            </div>

            <div class="text-right column text-lg"><span class="text-muted">{{ __('Subtotal') }}: </span><span
                    class="text-gray-dark">
                    @php
                        $subtotal = $cartTotal;
                        if (Session::has('coupon')) {
                            $couponData = Session::get('coupon');
                            $isFreeDeliveryCoupon = false;
                            if (isset($couponData['code']) && is_object($couponData['code'])) {
                                $isFreeDeliveryCoupon = $couponData['code']->is_free_delivery ?? false;
                            }
                            // Only subtract discount if it's not a free delivery coupon
                            if (!$isFreeDeliveryCoupon) {
                                $subtotal -= $couponData['discount'];
                            }
                        }
                    @endphp
                    {{ PriceHelper::setCurrencyPrice($subtotal) }}
                </span>
            </div>


        </div>
        <div class="shopping-cart-footer">
            <div class="column"><a class="btn btn-primary " href="{{ route('front.products') }}"><span><i
                            class="icon-arrow-left"></i> {{ __('Back to Shopping') }}</span></a></div>
            <div class="column"><a class="checkout-btn"
                    href="{{ route('front.checkout.billing') }}" style="background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: 2px solid #DD2476; padding: 12px 20px; font-size: 18px; font-weight: bold; color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3); transition: all 0.3s ease;"><span>{{ __('Checkout') }}</span></a></div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
// Free delivery coupon copy functionality
(function() {
    console.log('Free delivery script loaded');
    
    function attachCopyHandler() {
        console.log('Attaching copy handler...');
        
        // Remove any existing handlers to avoid duplicates
        jQuery('#copy_and_apply_btn, #free_delivery_code').off('click');
        
        // Attach new handler
        jQuery(document).on('click', '#copy_and_apply_btn, #free_delivery_code', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var couponCode = jQuery(this).attr('data-code');
            console.log('Button clicked! Coupon code:', couponCode);
            
            if (!couponCode) {
                console.error('No coupon code found in data-code attribute');
                return;
            }
            
            // Copy to clipboard using fallback method
            var tempInput = document.createElement('input');
            tempInput.value = couponCode;
            document.body.appendChild(tempInput);
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                var successful = document.execCommand('copy');
                console.log('Copy command result:', successful);
                if (successful) {
                    console.log('Coupon code copied to clipboard:', couponCode);
                }
            } catch (err) {
                console.error('Failed to copy:', err);
            }
            
            document.body.removeChild(tempInput);
            
            // Also try modern clipboard API
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(couponCode).then(function() {
                    console.log('Modern clipboard API succeeded');
                }).catch(function(err) {
                    console.error('Modern clipboard API failed:', err);
                });
            }
            
            // Find the coupon input field
            var couponInput = jQuery('#coupon_form input[name="code"]');
            if (couponInput.length === 0) {
                couponInput = jQuery('input[name="code"]');
            }
            if (couponInput.length === 0) {
                couponInput = jQuery('.coupon-form input[type="text"]');
            }
            
            console.log('Found input field:', couponInput.length > 0);
            
            if (couponInput.length > 0) {
                // Paste into input field
                couponInput.val(couponCode);
                console.log('Filled input with:', couponInput.val());
                
                // Add visual feedback to button
                var copyBtn = jQuery('#copy_and_apply_btn');
                if (copyBtn.length > 0) {
                    var originalHtml = copyBtn.html();
                    copyBtn.html('<i class="fas fa-check"></i> Copied!');
                    
                    setTimeout(function() {
                        copyBtn.html(originalHtml);
                    }, 2000);
                }
                
                // Highlight the input field
                couponInput.focus();
                couponInput.css({
                    'border': '2px solid #28a745',
                    'box-shadow': '0 0 10px rgba(40, 167, 69, 0.3)'
                });
                
                setTimeout(function() {
                    couponInput.css({
                        'border': '',
                        'box-shadow': ''
                    });
                }, 2000);
                
                // Scroll to coupon form
                jQuery('html, body').animate({
                    scrollTop: couponInput.offset().top - 100
                }, 500);
            } else {
                console.error('Coupon input field not found!');
                alert('Coupon code copied: ' + couponCode + '\nPlease paste it manually.');
            }
        });
        
        console.log('Handler attached successfully');
    }
    
    // Try to attach immediately
    if (typeof jQuery !== 'undefined') {
        if (jQuery(document).ready) {
            jQuery(document).ready(function() {
                attachCopyHandler();
            });
        } else {
            attachCopyHandler();
        }
    }
    
    // Also try after a short delay
    setTimeout(attachCopyHandler, 500);
})();
</script>

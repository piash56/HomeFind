<aside class="sidebar">
    <div class="padding-top-2x hidden-lg-up"></div>
    <!-- Items in Cart Widget-->

    <section class="card widget widget-featured-posts widget-order-summary p-4">
        <h3 class="widget-title">{{ __('Order Summary') }}</h3>
        @php
            $free_shipping = DB::table('shipping_services')->whereStatus(1)->whereIsCondition(1)->first();
        @endphp

        @if ($free_shipping)
            @if ($free_shipping->minimum_price >= $cart_total)
                <p class="free-shippin-aa"><em>{{ __('Free Shipping After Order') }}
                        {{ PriceHelper::setCurrencyPrice($free_shipping->minimum_price) }}</em></p>
            @endif
        @endif

        <table class="table">
            <tr>
                <td>{{ __('Cart subtotal') }}:</td>
                <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice($cart_total) }}</td>
            </tr>

            @if ($tax != 0)
                <tr>
                    <td>{{ __('Estimated tax') }}:</td>
                    <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice($tax) }}</td>
                </tr>
            @endif

            @if (DB::table('states')->count() > 0)
                <tr class="{{ Auth::check() && Auth::user()->state_id ? '' : 'd-none' }} set__state_price_tr">
                    <td>{{ __('State tax') }}:</td>
                    <td class="text-gray-dark set__state_price">
                        {{ PriceHelper::setCurrencyPrice(Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0) }}
                    </td>
                </tr>
            @endif

            @if ($discount)
                <tr>
                    <td>{{ __('Coupon discount') }}:</td>
                    <td class="text-danger">-
                        {{ PriceHelper::setCurrencyPrice($discount ? $discount['discount'] : 0) }}</td>
                </tr>
            @endif

            <tr>
                <td>{{ __('Delivery Fee') }}:</td>
                <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice(0) }}</td>
                </tr>
            <tr>
                <td class="text-lg text-primary">{{ __('Order total') }}</td>
                <td class="text-lg text-primary grand_total_set">{{ PriceHelper::setCurrencyPrice($grand_total) }}
                </td>
            </tr>
        </table>
    </section>




    <!-- Order Now Button-->
    <div class="mt-4">
        <button id="order_now_btn"
            class="btn btn-primary btn-lg w-100 order_now_btn p-0" type="submit">
            <span>{{ __('Order Now') }}</span>
        </button>
            </div>

</aside>

@section('script')
    <script>
        // Handle the "Order Now" button click
        $(document).on("click", "#order_now_btn", function(e) {
            e.preventDefault();
            
            // Validate the form first
            let form = $("#checkoutBilling");
            let name = $('input[name="bill_first_name"]').val().trim();
            let phone = $('input[name="bill_phone"]').val().trim();
            let address = $('textarea[name="bill_address1"]').val().trim();
            
            if (!name || !phone || !address) {
                alert('Please fill in all required fields: Name, Phone, and Address');
                return;
            }
            
            // Add hidden inputs for payment method and shipping
            form.append('<input type="hidden" name="payment_method" value="Cash On Delivery">');
            form.append('<input type="hidden" name="shipping_id" value="1">'); // Default free shipping
            form.append('<input type="hidden" name="state_id" value="">');
            
            // Show loading state
            $(this).prop('disabled', true).html('<span>Processing Order...</span>');
            
            // Submit the form
            form.submit();
        });

        // Order Now button is always enabled (no reCAPTCHA validation needed)
    </script>
@endsection

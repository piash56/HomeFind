@extends('master.front')

@section('title')
    {{ $item->name }}
@endsection


@section('meta')
    <meta name="tile" content="{{ $item->title }}">
    <meta name="keywords" content="{{ $item->meta_keywords }}">
    <meta name="description" content="{{ $item->meta_description }}">

    <meta name="twitter:title" content="{{ $item->title }}">
    <meta name="twitter:image" content="{{ asset('storage/images/' . $item->photo) }}">
    <meta name="twitter:description" content="{{ $item->meta_description }}">

    <meta name="og:title" content="{{ $item->title }}">
    <meta name="og:image" content="{{ asset('storage/images/' . $item->photo) }}">
    <meta name="og:description" content="{{ $item->meta_description }}">
@endsection

@section('styles')
<style>
.bulk-pricing-section {
    margin-top: 15px;
}

.bulk-price-option {
    transition: all 0.3s ease;
    cursor: pointer;
    background: #fff;
    display: flex;
    flex-direction: column;
    min-height: 140px;
}

.bulk-price-option:hover {
    border-color: #ff6600 !important;
    box-shadow: 0 4px 12px rgba(255, 102, 0, 0.15);
    transform: translateY(-2px);
}

.bulk-price-option .btn-primary {
    background-color: #ff6600;
    border-color: #ff6600;
    margin-top: auto;
    color: #fff !important;
}

.bulk-price-option .btn-primary:hover,
.bulk-price-option .btn-primary:focus,
.bulk-price-option .btn-primary:active {
    background-color: #000 !important;
    border-color: #000 !important;
    color: #fff !important;
}

.cursor-pointer {
    cursor: pointer;
}

#bulk-selection-message {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
    padding: 12px 15px;
    border-radius: 5px;
    font-weight: 500;
}

#order_now_btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.related-buy-now-btn:hover,
.related-buy-now-btn:focus,
.related-buy-now-btn:active {
    background-color: #000 !important;
    border-color: #000 !important;
    color: #fff !important;
}

@media (max-width: 767px) {
    .bulk-pricing-section .col-md-6 {
        margin-bottom: 15px;
    }
}
</style>
@endsection



@section('content')
    @php
        $currentRoute = Route::currentRouteName();
        $hideBreadcrumbs = in_array($currentRoute, ['front.product', 'front.checkout.billing', 'front.checkout.success', 'front.order.track']);
    @endphp

    {{-- Comprehensive Ecommerce dataLayer for GTM --}}
    <script>
        window.dataLayer = window.dataLayer || [];
        
        @php
            $user = Auth::user();
            $cart = session('cart', []);
            $cartTotal = 0;
            $cartItems = [];
            $cartItemCount = 0;
            
            foreach($cart as $key => $row) {
                $itemTotal = ($row['item']['discount_price'] ?? $row['item']['price']) * $row['qty'];
                $cartTotal += $itemTotal;
                $cartItemCount += $row['qty'];
                $cartItems[] = [
                    'id' => $row['item']['id'],
                    'name' => $row['item']['name'],
                    'category' => $row['item']['category']['name'] ?? '',
                    'quantity' => $row['qty'],
                    'price' => $row['item']['discount_price'] ?? $row['item']['price']
                ];
            }
            
            // Customer data
            $customerData = [];
            if($user) {
                $billing = json_decode($user->billing_info ?? '{}', true) ?: [];
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
                    'customerShippingFirstName' => $billing['ship_first_name'] ?? '',
                    'customerShippingLastName' => $billing['ship_last_name'] ?? '',
                    'customerShippingCompany' => $billing['ship_company'] ?? '',
                    'customerShippingAddress1' => $billing['ship_address1'] ?? '',
                    'customerShippingAddress2' => $billing['ship_address2'] ?? '',
                    'customerShippingCity' => $billing['ship_city'] ?? '',
                    'customerShippingState' => $billing['ship_state'] ?? '',
                    'customerShippingPostcode' => $billing['ship_zip'] ?? '',
                    'customerShippingCountry' => $billing['ship_country'] ?? ''
                ];
            }
            
            // Product ratings
            $reviewsQuery = $item->reviews();
            $avgRating = $reviewsQuery->avg('rating') ?? 0;
            $reviewCount = $reviewsQuery->count();
        @endphp
        
        window.dataLayer.push({
            // Page information
            'pagePostType': 'product',
            'pagePostType2': 'single-product',
            'pagePostAuthor': 'admin',
            
            // Customer data
            @if($user)
            @foreach($customerData as $key => $value)
            '{{ $key }}': {!! is_numeric($value) ? $value : "'" . addslashes($value) . "'" !!},
            @endforeach
            @endif
            
            // Cart information
            'cartContent': {
                'totals': {
                    'applied_coupons': [],
                    'discount_total': 0,
                    'subtotal': {{ $cartTotal }},
                    'total': {{ $cartTotal }}
                },
                'items': {!! json_encode($cartItems) !!}
            },
            
            // Product ratings and reviews
            'productRatingCounts': [],
            'productAverageRating': {{ $avgRating }},
            'productReviewCount': {{ $reviewCount }},
            'productType': '{{ $item->is_type ?? 'simple' }}',
            'productisVeriable': {{ $item->attributes->count() > 0 ? 1 : 0 }},
            
            // Ecommerce tracking
            'ecommerce': {
                'detail': {
                    'products': [{
                        'id': '{{ $item->id }}',
                        'name': '{{ addslashes($item->name) }}',
                        'category': '{{ addslashes($item->category->name ?? '') }}',
                        'price': {{ $item->discount_price ?? $item->price }},
                        'currency': '{{ env('CURRENCY_ISO', 'BDT') }}',
                        'brand': '{{ addslashes($item->brand->name ?? '') }}',
                        'variant': '{{ addslashes($item->is_type ?? '') }}',
                        'quantity': 1
                    }]
                }
            },
            'event': 'view_item'
        });
    </script>
    
    @if (!$hideBreadcrumbs)
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a>
                        </li>
                        <li class="separator"></li>
                        <li><a href="{{ route('front.index') }}">{{ __('Shop') }}</a>
                        </li>
                        <li class="separator"></li>
                        <li>{{ $item->name }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Page Content-->
    <div class="container padding-bottom-1x mb-1">
        <div class="row padding-top-2x">
            <!-- Poduct Gallery-->
            <div class="col-xxl-5 col-lg-6 col-md-6">
                <div class="product-gallery">
                    @if ($item->video)
                        <div class="gallery-wrapper">
                            <div class="gallery-item video-btn text-center">
                                <a href="{{ $item->video }}" title="Watch video"></a>
                            </div>
                        </div>
                    @endif
                    @if ($item->is_stock())
                        <span
                            class="product-badge
          @if ($item->is_type == 'feature') bg-warning
          @elseif($item->is_type == 'new')
          bg-success
          @elseif($item->is_type == 'top')
          bg-info
          @elseif($item->is_type == 'best')
          bg-dark
          @elseif($item->is_type == 'flash_deal')
            bg-success @endif
          ">{{ $item->is_type != 'undefine' ? ucfirst(str_replace('_', ' ', $item->is_type)) : '' }}</span>
                    @else
                        <span class="product-badge bg-secondary border-default text-body">{{ __('out of stock') }}</span>
                    @endif

                    @if ($item->previous_price && $item->previous_price != 0)
                        <div class="product-badge bg-goldenrod  ppp-t"> -{{ PriceHelper::DiscountPercentage($item) }}</div>
                    @endif

                    <div class="product-thumbnails insize">
                        <div class="product-details-slider owl-carousel">
                            <div class="item"><img src="{{ asset('storage/images/' . $item->photo) }}"
                                    alt="zoom" />
                            </div>
                            @foreach ($galleries as $key => $gallery)
                                <div class="item"><img src="{{ asset('storage/images/' . $gallery->photo) }}"
                                        alt="zoom" /></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product Info-->
            <div class="col-xxl-7 col-lg-6 col-md-6">
                <div class="details-page-top-right-content d-flex align-items-center">
                    <div class="div w-100">
                        <input type="hidden" id="item_id" value="{{ $item->id }}">
                        <input type="hidden" id="demo_price"
                            value="{{ PriceHelper::setConvertPrice($item->discount_price) }}">
                        <input type="hidden" value="{{ PriceHelper::setCurrencySign() }}" id="set_currency">
                        <input type="hidden" value="{{ PriceHelper::setCurrencyValue() }}" id="set_currency_val">
                        <input type="hidden" value="{{ $setting->currency_direction }}" id="currency_direction">
                        <h4 class="mb-2 p-title-main">{{ $item->name }}</h4>
                        {{-- <div class="mb-3">
                            <div class="rating-stars d-inline-block gmr-3">
                                {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                            </div>
                            @if ($item->is_stock())
                                <span class="text-success  d-inline-block">{{ __('In Stock') }} <b>({{ $item->stock }}
                                        @lang('items'))</b></span>
                            @else
                                <span class="text-danger  d-inline-block">{{ __('Out of stock') }}</span>
                            @endif
                        </div> --}}


                        @if ($item->is_type == 'flash_deal')
                            @if (date('d-m-y') != \Carbon\Carbon::parse($item->date)->format('d-m-y'))
                                <div class="countdown countdown-alt mb-3" data-date-time="{{ $item->date }}">
                                </div>
                            @endif
                        @endif

                        @php
                            $bulkPricingDataPreview = $item->getBulkPricingData();
                            $hasBulkPricingPreview = $item->enable_bulk_pricing && !empty($bulkPricingDataPreview);
                        @endphp
                        
                        <span class="h3 d-block price-area">
                            @if (!$hasBulkPricingPreview)
                                @if ($item->previous_price != 0)
                                    <small
                                        class="d-inline-block"><del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del></small>
                                @endif
                                <span id="main_price" class="main-price">{{ PriceHelper::grandCurrencyPrice($item) }}</span>
                            @else
                                <span class="text-muted" style="font-size: 1.2rem;">{{ __('দাম শুরু') }}</span>
                                <span id="main_price" class="main-price">{{ PriceHelper::grandCurrencyPrice($item) }}</span>
                                <span class="text-muted" style="font-size: 1.2rem;">{{ __('থেকে') }}</span>
                            @endif
                        </span>
                        <div class="row margin-top-1x">
                            @foreach ($attributes as $attribute)
                                @if ($attribute->options->count() != 0)
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="{{ $attribute->name }}">{{ $attribute->name }}</label>
                                            <select class="form-control attribute_option" id="{{ $attribute->name }}">
                                                @foreach ($attribute->options->where('stock', '!=', '0') as $option)
                                                    <option value="{{ $option->name }}" data-type="{{ $attribute->id }}"
                                                        data-href="{{ $option->id }}"
                                                        data-target="{{ $option->price }}">
                                                        {{ $option->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <p class="text-muted cta-text">{{ $setting->cta_text ?? 'For order call us or chat on WhatsApp' }}</p>
                        {{-- Contact Section --}}
                        @if($setting->cta_enabled ?? true)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="contact-section d-flex align-items-center gap-3 flex-wrap">
                                    @php
                                        $phoneNumber = $setting->cta_phone ?? '01872200587';
                                        $whatsappNumber = $setting->cta_whatsapp ?? $phoneNumber;
                                        // Clean phone number for WhatsApp and add Bangladesh country code
                                        $whatsappCleanNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);
                                        // Remove leading 0 and add +880 for Bangladesh
                                        if (strpos($whatsappCleanNumber, '880') === 0) {
                                            // Already has country code
                                            $whatsappFormattedNumber = $whatsappCleanNumber;
                                        } else {
                                            // Remove leading 0 and add 880
                                            $whatsappCleanNumber = ltrim($whatsappCleanNumber, '0');
                                            $whatsappFormattedNumber = '880' . $whatsappCleanNumber;
                                        }
                                    @endphp
                                    
                                    {{-- Phone Number with Blinking Effect --}}
                                    <div class="phone-number-section">
                                        <a href="tel:{{ $phoneNumber }}" class="phone-number-link">
                                            <span class="phone-icon">📞</span>
                                            <span class="phone-number-blinking">{{ $phoneNumber }}</span>
                                        </a>
                                    </div>
                                    
                                    {{-- WhatsApp Image --}}
                                    <div class="whatsapp-section">
                                        <a href="https://wa.me/{{ $whatsappFormattedNumber }}?text=Hello, I'm interested in {{ $item->name }}" 
                                           target="_blank" class="whatsapp-image-link">
                                            <img src="{{ asset('assets/images/whatsapp-click-to-chat.png') }}" 
                                                 alt="WhatsApp Chat" 
                                                 class="whatsapp-image">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row align-items-end pb-4">
                            <div class="col-sm-12">
                                @php
                                    $bulkPricingData = $item->getBulkPricingData();
                                    $hasBulkPricing = $item->enable_bulk_pricing && !empty($bulkPricingData);
                                @endphp
                                
                                @if ($item->item_type == 'normal')
                                    @if (!$hasBulkPricing)
                                        {{-- Regular quantity selector --}}
                                        <div class="qtySelector product-quantity">
                                            <span class="decreaseQty subclick"><i class="fas fa-minus "></i></span>
                                            <input type="text" class="qtyValue cart-amount" value="1">
                                            <span class="increaseQty addclick"><i class="fas fa-plus"></i></span>
                                            <input type="hidden" value="3333" id="current_stock">
                                        </div>
                                    @endif
                                @endif
                                
                                @if (!$hasBulkPricing)
                                    {{-- Regular Buy Now button --}}
                                    <div class="p-action-button">
                                        @if ($item->item_type != 'affiliate')
                                            @if ($item->is_stock())
                                                <button class="btn btn-primary m-0" id="but_to_cart"><i
                                                        class="icon-bag"></i><span>{{ __('Buy Now') }}</span></button>
                                            @else
                                                <button class="btn btn-primary m-0"><i
                                                        class="icon-bag"></i><span>{{ __('Out of stock') }}</span></button>
                                            @endif
                                        @else
                                            <a href="{{ $item->affiliate_link }}" target="_blank"
                                                class="btn btn-primary m-0"><span><i
                                                        class="icon-bag"></i>{{ __('Buy Now') }}</span></a>
                                        @endif
                                    </div>
                                @else
                                    {{-- Bulk pricing options in 2 columns --}}
                                    <div class="bulk-pricing-section">
                                        <h6 class="mb-3">{{ __('সরাসরি ওর্ডার করতে পরিমাণ নির্বাচন করুন') }}</h6>
                                        
                                        <div class="row">
                                            @php
                                                $allOptions = [['quantity' => 1, 'price' => $item->discount_price, 'is_single' => true]];
                                                foreach($bulkPricingData as $tier) {
                                                    $allOptions[] = ['quantity' => $tier['quantity'], 'price' => $tier['price'], 'is_single' => false];
                                                }
                                            @endphp
                                            
                                            @foreach ($allOptions as $index => $option)
                                                <div class="col-md-6 mb-3">
                                                    <div class="bulk-price-option border rounded p-3 h-100 cursor-pointer" data-quantity="{{ $option['quantity'] }}" data-price="{{ $option['price'] }}">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div>
                                                                <strong style="font-size: 1.1rem;">{{ __('Buy') }} {{ $option['quantity'] }}</strong>
                                                                @if ($option['is_single'])
                                                                    <div class="text-muted small">{{ __('Single Price') }}</div>
                                                                @else
                                                                    @php
                                                                        $savings = ($item->discount_price * $option['quantity']) - $option['price'];
                                                                        $savingsPercent = ($savings / ($item->discount_price * $option['quantity'])) * 100;
                                                                    @endphp
                                                                    @if ($savings > 0)
                                                                        <div class="text-success small">{{ __('Save') }} {{ PriceHelper::setCurrencyPrice($savings) }} ({{ number_format($savingsPercent, 0) }}%)</div>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                            <div class="text-right">
                                                                <div class="h5 mb-0 text-primary">{{ PriceHelper::setCurrencyPrice($option['price']) }}</div>
                                                                @if (!$option['is_single'])
                                                                    <small class="text-muted">{{ PriceHelper::setCurrencyPrice($option['price'] / $option['quantity']) }} {{ __('each') }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-primary btn-block bulk-buy-now mt-2" data-quantity="{{ $option['quantity'] }}" data-price="{{ $option['price'] }}">
                                                            <i class="icon-bag"></i> {{ __('Buy Now') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>

                        <div class="div">
                            {{-- <div class="t-c-b-area">
                                @if ($item->brand_id)
                                    <div class="pt-1 mb-1"><span class="text-medium">{{ __('Brand') }}:</span>
                                        <a
                                            href="{{ route('front.index') . '?brand=' . $item->brand->slug }}">{{ $item->brand->name }}</a>
                                    </div>
                                @endif
                                <div class="pt-1 mb-1"><span class="text-medium">{{ __('Tags') }}:</span>
                                    @if ($item->tags)
                                        @foreach (explode(',', $item->tags) as $tag)
                                            @if ($loop->last)
                                                <a
                                                    href="{{ route('front.index') . '?tag=' . $tag }}">{{ $tag }}</a>
                                            @else
                                                <a
                                                    href="{{ route('front.index') . '?tag=' . $tag }}">{{ $tag }}</a>,
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                @if ($item->item_type == 'normal')
                                    <div class="pt-1 mb-4"><span class="text-medium">{{ __('SKU') }}:</span>
                                        #{{ $item->sku }}</div>
                                @endif
                            </div> --}}

                            <div class="mt-4 p-d-f-area">
                                

                                <div class="d-flex align-items-center">
                                    <span class="text-muted mr-1">{{ __('Share') }}: </span>
                                    <div class="d-inline-block a2a_kit">
                                        <a class="facebook  a2a_button_facebook" href="">
                                            <span><i class="fab fa-facebook-f"></i></span>
                                        </a>
                                        <a class="twitter  a2a_button_twitter" href="">
                                            <span><i class="fab fa-twitter"></i></span>
                                        </a>
                                        <a class="linkedin  a2a_button_linkedin" href="">
                                            <span><i class="fab fa-linkedin-in"></i></span>
                                        </a>
                                        <a class="pinterest   a2a_button_pinterest" href="">
                                            <span><i class="fab fa-pinterest"></i></span>
                                        </a>
                                    </div>
                                    <script async src="https://static.addtoany.com/menu/page.js"></script>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class=" padding-top-3x mb-3" id="details">
                <div class="col-lg-12">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                data-bs-target="#description" type="button" role="tab" aria-controls="description"
                                aria-selected="true">{{ __('Descriptions') }}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="specification-tab" data-bs-toggle="tab"
                                data-bs-target="#specification" type="button" role="tab"
                                aria-controls="specification" aria-selected="false">{{ __('Specifications') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content card">
                        <div class="tab-pane fade show active" id="description" role="tabpanel"
                            aria-labelledby="description-tab"">
                            {!! $item->details !!}
                        </div>
                        <div class="tab-pane fade show" id="specification" role="tabpanel"
                            aria-labelledby="specification-tab">
                            <div class="comparison-table">
                                <table class="table table-bordered">
                                    <thead class="bg-secondary">
                                    </thead>
                                    <tbody>
                                        <tr class="bg-secondary">
                                            <th class="text-uppercase">{{ __('Specifications') }}</th>
                                            <td><span class="text-medium">{{ __('Descriptions') }}</span></td>
                                        </tr>
                                        @if ($sec_name)
                                            @foreach (array_combine($sec_name, $sec_details) as $sname => $sdetail)
                                                <tr>
                                                    <th>{{ $sname }}</th>
                                                    <td>{{ $sdetail }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="text-center">
                                                <td colspan="2">{{ __('No Specifications') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Reviews-->
    <div class="container  review-area">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2 class="h3">{{ __('Latest Reviews') }}</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div id="reviews-container">
                    @php
                        $approvedReviews = \App\Models\Review::where('item_id', $item->id)->where('status', 'approved')->orderBy('created_at', 'desc')->limit(3)->get();
                        $totalReviews = \App\Models\Review::where('item_id', $item->id)->where('status', 'approved')->count();
                    @endphp
                    @forelse ($approvedReviews as $review)
                        <div class="single-review mb-4">
                            <div class="row">
                                <!-- Left Side: Avatar, Name, Date, Review Text -->
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar-circle me-3">
                                            {{ strtoupper(substr($review->customer_name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $review->customer_name }}</h5>
                                            <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                                            @if($review->review_text)
                                                <p class="comment-text mt-2 mb-0">{{ $review->review_text }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Side: Stars and Images -->
                                <div class="col-md-4">
                                    <div class="text-right">
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        @php
                                            $reviewImages = $review->getReviewImages();
                                        @endphp
                                        @if(!empty($reviewImages))
                                            <div class="review-images-right">
                                                @foreach($reviewImages as $index => $image)
                                                    <img src="{{ asset($image) }}" class="img-fluid rounded review-image-thumb d-inline-block" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #ddd; margin: 2px; cursor: pointer;" 
                                                         alt="Review Image {{ $index + 1 }}"
                                                         onclick="openImageSlider({{ $review->id }}, {{ $index }}, {{ json_encode($reviewImages) }})">
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if($review->hasAdminReply())
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="admin-reply-compact">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-user-shield text-primary me-2 mt-1" style="font-size: 12px;"></i>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <strong class="text-primary" style="font-size: 13px;">{{ __('Reply by HomeFindBD.com') }}</strong>
                                                        <small class="text-muted ms-auto" style="font-size: 11px;">
                                                            @if($review->admin_reply_date)
                                                                {{ \Carbon\Carbon::parse($review->admin_reply_date)->format('M d, Y') }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                    <p class="mb-0 text-dark" style="font-size: 13px; line-height: 1.3;">{{ $review->admin_reply }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="card p-5">
                            {{ __('No Review') }}
                        </div>
                    @endforelse
                    
                    @if($totalReviews > 3)
                        <div class="text-center mt-4 mb-4">
                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#all-reviews-modal">
                                {{ __('More Reviews') }} ({{ $totalReviews - 3 }} {{ __('more') }})
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="d-inline align-baseline display-3 mr-1">
                                {{ round(\App\Models\Review::getAverageRating($item->id), 1) }}
                            </div>
                            <div class="d-inline align-baseline text-sm text-warning mr-1">
                                <div class="rating-stars">
                                    @php
                                        $avgRating = \App\Models\Review::getAverageRating($item->id);
                                    @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $avgRating)
                                            <i class="fas fa-star text-warning"></i>
                                        @elseif($i - 0.5 <= $avgRating)
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <div class="text-muted small">
                                {{ \App\Models\Review::getReviewCount($item->id) }} {{ __('reviews') }}
                            </div>
                        </div>
                        <div class="pt-3">
                            @for($star = 5; $star >= 1; $star--)
                                @php
                                    $starCount = \App\Models\Review::where('item_id', $item->id)
                                                                  ->where('status', 'approved')
                                                                  ->where('rating', $star)
                                                                  ->count();
                                    $totalReviews = \App\Models\Review::getReviewCount($item->id);
                                    $percentage = $totalReviews > 0 ? ($starCount / $totalReviews) * 100 : 0;
                                @endphp
                                <label class="text-medium text-sm">{{ $star }} {{ __('stars') }} 
                                    <span class="text-muted">- {{ $starCount }}</span>
                                </label>
                                <div class="progress margin-bottom-1x">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ $percentage }}%; height: 2px;"
                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            @endfor
                        </div>
                        <div class="pb-2">
                            <button class="btn btn-primary btn-block" type="button" data-toggle="modal" data-target="#review-modal" id="review-login-btn">
                                <span>{{ __('Login') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (count($related_items) > 0)
        <div class="relatedproduct-section container padding-bottom-3x mb-1 s-pt-30">
            <!-- Related Products Carousel-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2 class="h3">{{ __('You May Also Like') }}</h2>
                    </div>
                </div>
            </div>
            <!-- Carousel-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="relatedproductslider owl-carousel">
                        @foreach ($related_items as $related)
                            <div class="slider-item">
                                <div class="product-card">

                                    @if ($related->is_stock())
                                        @if ($related->is_type == 'new')
                                        @else
                                            <div
                                                class="product-badge
                                    @if ($related->is_type == 'feature') bg-warning

                                    @elseif($related->is_type == 'top')
                                    bg-info
                                    @elseif($related->is_type == 'best')
                                    bg-dark
                                    @elseif($related->is_type == 'flash_deal')
                                    bg-success @endif
                                    ">
                                                {{ $related->is_type != 'undefine' ? ucfirst(str_replace('_', ' ', $related->is_type)) : '' }}
                                            </div>
                                        @endif
                                    @else
                                        <div
                                            class="product-badge bg-secondary border-default text-body
                                    ">
                                            {{ __('out of stock') }}</div>
                                    @endif
                                    @if ($related->previous_price && $related->previous_price != 0)
                                        <div class="product-badge product-badge2 bg-info">
                                            -{{ PriceHelper::DiscountPercentage($related) }}</div>
                                    @endif

                                    @if ($related->previous_price && $related->previous_price != 0)
                                        <div class="product-badge product-badge2 bg-info">
                                            -{{ PriceHelper::DiscountPercentage($related) }}</div>
                                    @endif
                                    <div class="product-thumb">
                                        <a href="{{ route('front.product', $related->slug) }}">
                                            <img src="{{ asset('storage/images/' . $related->photo) }}" alt="{{ $related->name }}">
                                        </a>
                                    </div>
                                    <div class="product-card-body">
                                        <div class="product-category"><a
                                                href="{{ route('front.index') . '?category=' . $related->category->slug }}">{{ $related->category->name }}</a>
                                        </div>
                                        <h3 class="product-title"><a
                                                href="{{ route('front.product', $related->slug) }}">
                                                {{ Str::limit($related->name, 35) }}
                                            </a></h3>
                                        <h4 class="product-price">
                                            @if ($related->previous_price != 0)
                                                <del>{{ PriceHelper::setPreviousPrice($related->previous_price) }}</del>
                                            @endif
                                            {{ PriceHelper::grandCurrencyPrice($related) }}
                                        </h4>
                                        <div class="product-buttons">
                                            <a href="{{ route('front.product', $related->slug) }}" class="btn btn-primary btn-sm related-buy-now-btn" style="color: white !important; text-decoration: none !important;">
                                                {{ __('Buy Now') }}
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Checkout Section -->
    <div class="container padding-bottom-3x mb-1" id="checkout-section">
        <div class="row">
            <!-- Delivery Information-->
            <div class="col-xl-8 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h6>{{ __('ডেলিভারি তথ্য') }}</h6>

                        <form id="checkoutBilling" action="{{ route('front.order.direct') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="checkout-name">{{ __('নাম') }}</label>
                                        <input class="form-control" name="bill_first_name" type="text" required
                                            id="checkout-name" value="{{ isset($user) ? $user->first_name . ' ' . $user->last_name : '' }}" placeholder="আপনার পুরো নাম লিখুন">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="checkout-phone">{{ __('ফোন নাম্বার') }}</label>
                                        <input class="form-control" name="bill_phone" type="text"
                                            id="checkout-phone" required
                                            value="{{ isset($user) ? $user->phone : '' }}" placeholder="আপনার ফোন নম্বর লিখুন">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="checkout-address1">{{ __('ঠিকানা') }}</label>
                                        <textarea class="form-control" name="bill_address1" required
                                            id="checkout-address1" rows="3" placeholder="আপনার ডেলিভারি ঠিকানা লিখুন">{{ isset($user) ? $user->bill_address1 : '' }}</textarea>
                                    </div>
                                </div>
                                <!-- Hidden fields for required data -->
                                <input type="hidden" name="bill_email" value="{{ isset($user) ? $user->email : 'customer@example.com' }}">
                                <input type="hidden" name="bill_last_name" value="">
                                <input type="hidden" name="bill_company" value="">
                                <input type="hidden" name="bill_address2" value="">
                                <input type="hidden" name="bill_zip" value="">
                                <input type="hidden" name="bill_city" value="Dhaka">
                                <input type="hidden" name="bill_country" value="Bangladesh">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Order Summary Sidebar -->
            <div class="col-xl-4 col-lg-4">
                <aside class="sidebar">
                    <div class="padding-top-2x hidden-lg-up"></div>
                    <!-- Order Summary Widget-->
                    <section class="card widget-featured-posts widget-order-summary p-4">
                        <h3 class="widget-title">{{ __('অর্ডার সংক্ষেপ') }}</h3>
                        @php
                            $base_price = $item->discount_price;
                            $hasBulkPricingCheckout = $item->enable_bulk_pricing && !empty($item->getBulkPricingData());
                            // Default to base price even if bulk pricing is enabled; will update if a bulk option is chosen
                            $cart_total = $base_price;
                            $tax = 0;
                            $grand_total = $cart_total + $tax;
                        @endphp

                        <table class="table">
                            <tr>
                                <td>{{ __('প্রোডাক্ট বিল') }}:</td>
                                <td class="text-gray-dark" id="cart-subtotal">{{ PriceHelper::setCurrencyPrice($cart_total) }}</td>
                            </tr>

                            <tr>
                                <td>{{ __('ডেলিভারি ফি') }}:</td>
                                <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice(0) }}</td>
                            </tr>
                            <tr>
                                <td class="text-lg text-primary">{{ __('মোট টাকা') }}</td>
                                <td class="text-lg text-primary grand_total_set" id="order-total">{{ PriceHelper::setCurrencyPrice($grand_total) }}
                                </td>
                            </tr>
                        </table>
                        
                        <!-- Hidden price data for JavaScript -->
                        <input type="hidden" id="base-price" value="{{ $item->discount_price }}">
                        <input type="hidden" id="currency-sign" value="{{ PriceHelper::setCurrencySign() }}">
                        <input type="hidden" id="currency-direction" value="{{ $setting->currency_direction }}">
                        <input type="hidden" id="has-bulk-pricing" value="{{ $hasBulkPricingCheckout ? '1' : '0' }}">
                        
                    </section>

                    <!-- Quantity Selection Message -->
                    @if ($hasBulkPricingCheckout)
                        <div id="bulk-selection-message" class="alert alert-info mt-3" style="display: block;">
                            <i class="fas fa-shopping-cart"></i> <span id="bulk-message-text">{{ __('আপনি একটা কিনার জন্য ক্লিক করছেন') }}</span>
                        </div>
                    @endif

                    <!-- Order Now Button-->
                    <div class="mt-4">
                        <button id="order_now_btn"
                            class="btn btn-primary btn-lg w-100 order_now_btn p-0" type="submit">
                            <span>{{ __('অর্ডার করুন') }}</span>
                        </button>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    @auth
        <form class="modal fade ratingForm" action="{{ route('front.review.submit') }}" method="post" id="leaveReview"
            tabindex="-1">
            @csrf
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Leave a Review') }}</h4>
                        <button class="close modal_close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $user = Auth::user();
                        @endphp
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-name">{{ __('Your Name') }}</label>
                                    <input class="form-control" type="text" id="review-name"
                                        value="{{ $user->first_name }}" required>
                                </div>
                            </div>
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-email">{{ __('Your Email') }}</label>
                                    <input class="form-control" type="email" id="review-email"
                                        value="{{ $user->email }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-subject">{{ __('Subject') }}</label>
                                    <input class="form-control" type="text" name="subject" id="review-subject" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-rating">{{ __('Rating') }}</label>
                                    <select name="rating" class="form-control" id="review-rating">
                                        <option value="5">5 {{ __('Stars') }}</option>
                                        <option value="4">4 {{ __('Stars') }}</option>
                                        <option value="3">3 {{ __('Stars') }}</option>
                                        <option value="2">2 {{ __('Stars') }}</option>
                                        <option value="1">1 {{ __('Star') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="review-message">{{ __('Review') }}</label>
                            <textarea class="form-control" name="review" id="review-message" rows="8" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit"><span>{{ __('Submit Review') }}</span></button>
                    </div>
                </div>
            </div>
        </form>
    @endauth

@endsection

@section('script')
<script>
$(document).ready(function() {
    // Function to get total attribute price (raw prices, no currency conversion)
    function getTotalAttributePrice() {
        var totalAttributePrice = 0;
        $('.attribute_option').each(function() {
            var selectedOption = $(this).find(':selected');
            var optionPrice = parseFloat(selectedOption.attr('data-target')) || 0;
            totalAttributePrice += optionPrice;
        });
        return totalAttributePrice;
    }
    
    // Function to update order summary
    function updateOrderSummary() {
        var quantity = parseInt($('.cart-amount').val()) || 1;
        var basePrice = parseFloat($('#base-price').val());
        var attributePrice = getTotalAttributePrice(); // Get raw attribute prices
        var currencySign = $('#currency-sign').val();
        var currencyDirection = parseInt($('#currency-direction').val());
        
        // Check if bulk pricing is active
        var subtotal;
        if (window.bulkPricingSelection && window.bulkPricingSelection.totalPrice) {
            subtotal = window.bulkPricingSelection.totalPrice;
            quantity = window.bulkPricingSelection.quantity;
        } else {
            subtotal = (basePrice + attributePrice) * quantity;
        }
        
        var total = subtotal; // Delivery fee is 0
        
        // Format price based on currency direction with proper decimal handling
        var formattedSubtotal, formattedTotal;
        var decimalPlaces = (subtotal % 1 === 0) ? 0 : 2; // Use 0 decimals for whole numbers
        
        if (currencyDirection == 1) {
            formattedSubtotal = currencySign + subtotal.toFixed(decimalPlaces);
            formattedTotal = currencySign + total.toFixed(decimalPlaces);
        } else {
            formattedSubtotal = subtotal.toFixed(decimalPlaces) + currencySign;
            formattedTotal = total.toFixed(decimalPlaces) + currencySign;
        }
        
        $('#cart-subtotal').text(formattedSubtotal);
        $('#order-total').text(formattedTotal);
        
        // Also update the main product price display
        $('#main_price').text(formattedSubtotal);
        
    }
    
    // Handle quantity changes - prevent default behavior and stop propagation
    $('.increaseQty').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var currentVal = parseInt($('.cart-amount').val()) || 1;
        $('.cart-amount').val(currentVal + 1);
        updateOrderSummary();
        return false;
    });
    
    $('.decreaseQty').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var currentVal = parseInt($('.cart-amount').val()) || 1;
        if (currentVal > 1) {
            $('.cart-amount').val(currentVal - 1);
            updateOrderSummary();
        }
        return false;
    });
    
    // Handle manual quantity input
    $('.cart-amount').on('change keyup', function() {
        var val = parseInt($(this).val()) || 1;
        if (val < 1) val = 1;
        $(this).val(val);
        updateOrderSummary();
    });
    
    // Handle attribute option changes
    $('.attribute_option').on('change', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        updateOrderSummary();
        return false;
    });
    
    // Function to remove validation errors
    function removeValidationErrors() {
        $('input[name="bill_first_name"], input[name="bill_phone"], textarea[name="bill_address1"]').removeClass('is-invalid').css('border-color', '');
    }
    
    // Function to add validation errors
    function addValidationError(element) {
        $(element).addClass('is-invalid').css('border-color', 'red');
    }
    
    // Remove error styling when user starts typing
    $('input[name="bill_first_name"], input[name="bill_phone"], textarea[name="bill_address1"]').on('input', function() {
        $(this).removeClass('is-invalid').css('border-color', '');
    });
    
    // Handle Bulk Buy Now button click
    $(document).on("click", ".bulk-buy-now", function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        let quantity = $(this).data('quantity');
        let price = $(this).data('price');
        
        // Set the quantity in hidden field or update cart
        $('.cart-amount').val(quantity);
        
        // Store bulk pricing info for checkout
        window.bulkPricingSelection = {
            quantity: quantity,
            totalPrice: price
        };
        
        // Update Order Summary with bulk pricing
        updateOrderSummaryWithBulkPrice(price, quantity);
        
        // Show and update bulk selection message
        $('#bulk-message-text').text('আপনি ' + quantity + ' টা কেনার জন্য ক্লিক করেছেন');
        $('#bulk-selection-message').show();
        
        // Enable Order Now button
        $('#order_now_btn').prop('disabled', false).removeClass('disabled');
        
        // Scroll to checkout section
        $('html, body').animate({
            scrollTop: $('#checkout-section').offset().top - 100
        }, 1000);
        
        return false;
    });
    
    // Function to update Order Summary with bulk price
    function updateOrderSummaryWithBulkPrice(price, quantity) {
        var currencySign = $('#currency-sign').val();
        var currencyDirection = parseInt($('#currency-direction').val());
        
        // Format price
        var formattedPrice;
        var decimalPlaces = (price % 1 === 0) ? 0 : 2;
        
        if (currencyDirection == 0) {
            formattedPrice = currencySign + parseFloat(price).toFixed(decimalPlaces);
        } else {
            formattedPrice = parseFloat(price).toFixed(decimalPlaces) + currencySign;
        }
        
        // Update cart subtotal and order total
        $('#cart-subtotal').text(formattedPrice);
        $('#order-total').text(formattedPrice);
    }
    
    // Handle Buy Now button click (scroll to checkout section)
    $(document).on("click", "#but_to_cart", function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        // Clear bulk pricing selection and reset message to default
        window.bulkPricingSelection = null;
        $('#bulk-message-text').text('You are clicking for 1 piece only');
        $('#bulk-selection-message').show();
        updateOrderSummary();
        
        // Scroll to checkout section
        $('html, body').animate({
            scrollTop: $('#checkout-section').offset().top - 100
        }, 1000);
        
        return false;
    });

    // Track form field changes for GTM
    $(document).on('input change', '#checkoutBilling input, #checkoutBilling textarea', function() {
        let formData = {
            'event': 'form_field_change',
            'form_id': 'checkoutBilling',
            'field_name': $(this).attr('name'),
            'field_value': $(this).val(),
            'form_data': {
                'bill_first_name': $('input[name="bill_first_name"]').val(),
                'bill_phone': $('input[name="bill_phone"]').val(),
                'bill_address1': $('textarea[name="bill_address1"]').val(),
                'bill_email': $('input[name="bill_email"]').val()
            }
        };
        
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push(formData);
    });

    // Handle Order Now button click (place order directly)
    $(document).on("click", "#order_now_btn", function(e) {
        e.preventDefault();
        
        // Remove previous validation errors
        removeValidationErrors();
        
        // Validate the form first
        let form = $("#checkoutBilling");
        let name = $('input[name="bill_first_name"]').val().trim();
        let phone = $('input[name="bill_phone"]').val().trim();
        let address = $('textarea[name="bill_address1"]').val().trim();
        
        let hasErrors = false;
        
        if (!name) {
            addValidationError('input[name="bill_first_name"]');
            hasErrors = true;
        }
        
        if (!phone) {
            addValidationError('input[name="bill_phone"]');
            hasErrors = true;
        }
        
        if (!address) {
            addValidationError('textarea[name="bill_address1"]');
            hasErrors = true;
        }
        
        if (hasErrors) {
            // Scroll to first error field
            var firstError = $('.is-invalid').first();
            $('html, body').animate({
                scrollTop: firstError.offset().top - 150
            }, 500);
            return;
        }
        
        // Push form submission data to GTM
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'event': 'form_submit',
            'form_id': 'checkoutBilling',
            'form_data': {
                'bill_first_name': name,
                'bill_phone': phone,
                'bill_address1': address,
                'bill_email': $('input[name="bill_email"]').val()
            },
            'ecommerce': {
                'add_to_cart': {
                    'products': [{
                        'id': $('#item_id').val(),
                        'name': '{{ addslashes($item->name) }}',
                        'category': '{{ addslashes($item->category->name ?? '') }}',
                        'price': {{ $item->discount_price ?? $item->price }},
                        'currency': '{{ env('CURRENCY_ISO', 'BDT') }}',
                        'quantity': parseInt($('.cart-amount').val()) || 1
                    }]
                }
            }
        });
        
        // Get product details
        var itemId = $('#item_id').val();
        var quantity = $('.cart-amount').val() || 1;
        
        // Collect selected attributes
        var selectedAttributes = {};
        $('.attribute_option').each(function() {
            var attributeId = $(this).find(':selected').attr('data-type');
            var optionId = $(this).find(':selected').attr('data-href');
            if (attributeId && optionId) {
                selectedAttributes[attributeId] = optionId;
            }
        });
        
        // Show loading state immediately
        $('#order_now_btn').prop('disabled', true).html('<span><i class="fas fa-spinner fa-spin"></i> Processing Order...</span>');
        
        // Prepare order data
        var orderData = {
            _token: "{{ csrf_token() }}",
            item_id: itemId,
            quantity: quantity,
            bill_first_name: $('input[name="bill_first_name"]').val(),
            bill_phone: $('input[name="bill_phone"]').val(),
            bill_address1: $('textarea[name="bill_address1"]').val(),
            selected_attributes: JSON.stringify(selectedAttributes)
        };
        
        // Add bulk pricing info if available
        if (window.bulkPricingSelection) {
            orderData.bulk_pricing = true;
            orderData.bulk_quantity = window.bulkPricingSelection.quantity;
            orderData.bulk_total_price = window.bulkPricingSelection.totalPrice;
        }
        
        // Place order directly (no cart needed)
        $.ajax({
            url: "{{ route('front.order.direct') }}",
            type: "POST",
            data: orderData,
            success: function(response) {
                console.log('Order placed successfully:', response);
                console.log('Redirect URL from response:', response.redirect_url);
                
                if (response.success) {
                    // Redirect to success page
                    console.log('Redirecting to:', response.redirect_url);
                    window.location.replace(response.redirect_url);
                } else {
                    // Reset button state on error
                    $('#order_now_btn').prop('disabled', false).html('<span>{{ __('Order Now') }}</span>');
                    alert('Failed to place order. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                console.log('Error placing order:', error);
                
                // Reset button state on error
                $('#order_now_btn').prop('disabled', false).html('<span>{{ __('Order Now') }}</span>');
                
                // Show error message
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert('Error: ' + xhr.responseJSON.error);
                } else {
                    alert('There was an error processing your order. Please try again.');
                }
            }
        });
    });
    
    // Initialize order summary on page load
    updateOrderSummary();
});
</script>

<style>
.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
}

.star-rating {
    font-size: 24px;
    color: #ddd;
    cursor: pointer;
    display: inline-block;
    margin-right: 5px;
}

.star-rating:hover,
.star-rating.active {
    color: #ffc107;
}

.star-rating-container {
    display: flex;
    align-items: center;
}

#review-submit-form {
    display: none;
}

/* Modal z-index fix for frontend */
.modal {
    z-index: 9999 !important;
}

/* Image preview styles */
.image-preview-item {
    position: relative;
    display: inline-block;
}

.image-preview-item img {
    border: 2px solid #ddd;
    border-radius: 4px;
}

.remove-image {
    border-radius: 50%;
    width: 20px;
    height: 20px;
    padding: 0;
    font-size: 12px;
    line-height: 1;
}

/* Review image thumbnails */
.review-image-thumb {
    transition: transform 0.2s ease;
    cursor: pointer;
}

.review-image-thumb:hover {
    transform: scale(1.1);
    border-color: #007bff !important;
}

/* Review layout improvements */
.single-review {
    padding: 15px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background-color: #fff;
    margin-bottom: 15px;
}

.comment-text {
    font-size: 14px;
    line-height: 1.4;
    color: #555;
}

/* Admin reply section */
.admin-reply-compact {
    background-color: #f8f9fa;
    border-left: 3px solid #007bff;
    padding: 8px 12px;
    border-radius: 4px;
    margin-top: 8px;
}

.admin-reply-section .card {
    border: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin: 0;
}

/* All Reviews Modal Styles */
#all-reviews-modal .modal-body {
    padding: 20px;
}

#all-reviews-modal .single-review {
    box-shadow: none;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 15px;
    padding: 15px;
}

#all-reviews-modal .single-review:last-child {
    border-bottom: 1px solid #e9ecef;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
}

/* Filter Section Styles */
.reviews-filter-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.reviews-filter-section .form-label {
    font-size: 14px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
}

.reviews-filter-section .form-control-sm {
    font-size: 13px;
}

/* Image Slider Modal Styles */
.image-slider-modal .modal-dialog {
    max-width: 95vw;
    max-height: 95vh;
    margin: 2.5vh auto;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-slider-modal .modal-content {
    background: transparent;
    border: none;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 90vh;
}

.image-slider-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    border-radius: 8px;
}

.slider-image {
    max-width: 90vw;
    max-height: 85vh;
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.slider-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
}

.slider-nav:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-50%) scale(1.1);
}

.slider-nav.prev {
    left: 20px;
}

.slider-nav.next {
    right: 20px;
}

.slider-nav:disabled {
    opacity: 0.3;
    cursor: not-allowed;
    transform: translateY(-50%);
}

.slider-counter {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 500;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
}

.slider-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
    z-index: 1000;
}

.slider-close:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.1);
}

/* Mobile Responsive for Filters */
@media (max-width: 768px) {
    .reviews-filter-section .row {
        margin: 0;
    }
    
    .reviews-filter-section .col-md-6 {
        padding: 0 5px;
        margin-bottom: 10px;
    }
    
    .image-slider-modal .modal-dialog {
        max-width: 98vw;
        max-height: 98vh;
        margin: 1vh auto;
    }
    
    .slider-image {
        max-width: 95vw;
        max-height: 90vh;
    }
    
    .slider-nav {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .slider-nav.prev {
        left: 10px;
    }
    
    .slider-nav.next {
        right: 10px;
    }
    
    .slider-close {
        width: 40px;
        height: 40px;
        font-size: 18px;
        top: 10px;
        right: 10px;
    }
    
    .slider-counter {
        bottom: 10px;
        padding: 6px 12px;
        font-size: 12px;
    }
}

@media (max-width: 576px) {
    .slider-image {
        max-width: 98vw;
        max-height: 85vh;
    }
    
    .slider-nav {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
    
    .slider-close {
        width: 35px;
        height: 35px;
        font-size: 16px;
    }
}

.admin-reply-section .card-body {
    padding: 12px 15px;
}

.admin-reply-section .border-start {
    border-left-width: 4px !important;
}

/* Contact Section Styling */
.contact-section {
    margin-bottom: 15px;
    padding: 0;
}

.phone-number-section {
    display: flex;
    align-items: center;
}

.phone-number-link {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
}

.phone-number-link:hover {
    text-decoration: none;
    color: #007bff;
    transform: scale(1.05);
}

.phone-icon {
    font-size: 24px;
    animation: bounce 2s infinite;
}

.phone-number-blinking {
    font-size: 20px;
    font-weight: bold;
    animation: blinkGreenRed 2s infinite;
}

.whatsapp-section {
    display: flex;
    align-items: center;
}

.whatsapp-image-link {
    display: block;
    transition: all 0.3s ease;
}

.whatsapp-image-link:hover {
    transform: scale(1.05);
}

.whatsapp-image {
    max-height: 50px;
    width: auto;
    border: none;
    box-shadow: none;
    background: none;
}

/* Animations */
@keyframes blinkGreenRed {
    0%, 25% {
        color: #28a745; /* Green */
        text-shadow: 0 0 8px rgba(40, 167, 69, 0.5);
    }
    26%, 50% {
        color: #dc3545; /* Red */
        text-shadow: 0 0 8px rgba(220, 53, 69, 0.5);
    }
    51%, 75% {
        color: #28a745; /* Green */
        text-shadow: 0 0 8px rgba(40, 167, 69, 0.5);
    }
    76%, 100% {
        color: #dc3545; /* Red */
        text-shadow: 0 0 8px rgba(220, 53, 69, 0.5);
    }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-5px);
    }
    60% {
        transform: translateY(-3px);
    }
}

@media (max-width: 576px) {
    .contact-section {
        flex-direction: column;
        gap: 15px !important;
    }
    
    .phone-number-blinking {
        font-size: 18px;
    }
    
    .whatsapp-image {
        max-height: 45px;
    }
}
</style>

<!-- Review Modal -->
<div class="modal fade" id="review-modal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">{{ __('Write a Review') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Step 1: Order Verification -->
            <div id="review-verification-form">
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h6>{{ __('Please verify your order to write a review') }}</h6>
                        <p class="text-muted">{{ __('Enter your Order ID and Phone number to continue') }}</p>
                    </div>
                    
                    <form id="verify-order-form">
                        <div class="form-group">
                            <label for="order_id">{{ __('Order ID') }} *</label>
                            <input type="text" class="form-control" id="order_id" name="order_id" 
                                   placeholder="{{ __('Enter your Order ID') }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">{{ __('Phone Number') }} *</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   placeholder="{{ __('Enter your phone number') }}" required>
                        </div>
                        
                        <input type="hidden" id="item_id" value="{{ $item->id }}">
                    </form>
                    
                    <div id="verification-message" class="mt-3" style="display: none;"></div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="verify-order-btn">
                        <span class="btn-text">{{ __('Verify Order') }}</span>
                        <span class="spinner-border spinner-border-sm" style="display: none;"></span>
                    </button>
                </div>
            </div>
            
            <!-- Step 2: Review Form -->
            <div id="review-submit-form">
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h6>{{ __('Write Your Review') }}</h6>
                        <p class="text-muted">{{ __('Share your experience with this product') }}</p>
                    </div>
                    
                    <form id="submit-review-form" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Customer Name') }}</label>
                                    <input type="text" class="form-control" id="customer_name_display" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Phone Number') }}</label>
                                    <input type="text" class="form-control" id="customer_phone_display" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>{{ __('Rating') }} *</label>
                            <div class="star-rating-container">
                                <div class="star-rating" data-rating="1">★</div>
                                <div class="star-rating" data-rating="2">★</div>
                                <div class="star-rating" data-rating="3">★</div>
                                <div class="star-rating" data-rating="4">★</div>
                                <div class="star-rating" data-rating="5">★</div>
                            </div>
                            <input type="hidden" id="rating_value" name="rating" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="review_text">{{ __('Review') }}</label>
                            <textarea class="form-control" id="review_text" name="review_text" rows="4" 
                                      placeholder="{{ __('Write your review here...') }}"></textarea>
                        </div>
                        
                                <div class="form-group">
                                    <label for="review_images">{{ __('Upload Images (Optional)') }}</label>
                                    <input type="file" class="form-control-file" id="review_images" name="review_images[]" 
                                           accept="image/*" multiple>
                                    <small class="form-text text-muted">{{ __('Supported formats: JPG, PNG, GIF (Max: 2MB each, Max 3 images)') }}</small>
                                    <div id="image-preview" class="mt-2" style="display: none;">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="image-preview-item">
                                                    <img id="preview-0" src="" alt="Preview 1" class="img-thumbnail" style="display: none; width: 80px; height: 80px; object-fit: cover;">
                                                    <button type="button" class="btn btn-sm btn-danger remove-image" data-index="0" style="display: none; position: absolute; top: 0; right: 0;">×</button>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="image-preview-item">
                                                    <img id="preview-1" src="" alt="Preview 2" class="img-thumbnail" style="display: none; width: 80px; height: 80px; object-fit: cover;">
                                                    <button type="button" class="btn btn-sm btn-danger remove-image" data-index="1" style="display: none; position: absolute; top: 0; right: 0;">×</button>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="image-preview-item">
                                                    <img id="preview-2" src="" alt="Preview 3" class="img-thumbnail" style="display: none; width: 80px; height: 80px; object-fit: cover;">
                                                    <button type="button" class="btn btn-sm btn-danger remove-image" data-index="2" style="display: none; position: absolute; top: 0; right: 0;">×</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        
                        <input type="hidden" id="verified_order_id" name="order_id">
                        <input type="hidden" id="verified_phone" name="phone">
                        <input type="hidden" id="verified_item_id" name="item_id">
                    </form>
                    
                    <div id="submit-message" class="mt-3" style="display: none;"></div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="back-to-verification">{{ __('Back') }}</button>
                    <button type="button" class="btn btn-primary" id="submit-review-btn">
                        <span class="btn-text">{{ __('Submit Review') }}</span>
                        <span class="spinner-border spinner-border-sm" style="display: none;"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Modal trigger with debugging
    $('#review-login-btn').click(function(e) {
        e.preventDefault();
        console.log('Login button clicked, showing modal');
        $('#review-modal').modal('show');
    });
    
    // Debug modal events
    $('#review-modal').on('show.bs.modal', function (e) {
        console.log('Modal is showing');
    });
    
    $('#review-modal').on('hide.bs.modal', function (e) {
        console.log('Modal is hiding');
    });
    
    $('#review-modal').on('hidden.bs.modal', function (e) {
        console.log('Modal is hidden');
        // Reset modal forms when hidden
        resetModalForms();
    });
    
    // Function to reset modal forms
    function resetModalForms() {
        $('#review-verification-form').show();
        $('#review-submit-form').hide();
        $('#verify-order-form')[0].reset();
        $('#submit-review-form')[0].reset();
        $('#verification-message').hide();
        $('#submit-message').hide();
        $('#rating_value').val('');
        $('.star-rating').removeClass('active');
    }
    
    // Test if modal exists
    if ($('#review-modal').length) {
        console.log('Review modal element found');
    } else {
        console.log('Review modal element NOT found');
    }
    
    // Star rating functionality
    $('.star-rating').click(function() {
        var rating = $(this).data('rating');
        $('#rating_value').val(rating);
        
        $('.star-rating').removeClass('active');
        $('.star-rating').each(function() {
            if ($(this).data('rating') <= rating) {
                $(this).addClass('active');
            }
        });
    });
    
    // Hover effect for stars
    $('.star-rating').hover(
        function() {
            var rating = $(this).data('rating');
            $('.star-rating').removeClass('active');
            $('.star-rating').each(function() {
                if ($(this).data('rating') <= rating) {
                    $(this).addClass('active');
                }
            });
        },
        function() {
            var currentRating = $('#rating_value').val();
            $('.star-rating').removeClass('active');
            if (currentRating) {
                $('.star-rating').each(function() {
                    if ($(this).data('rating') <= currentRating) {
                        $(this).addClass('active');
                    }
                });
            }
        }
    );
    
    // Verify order
    $('#verify-order-btn').click(function() {
        var btn = $(this);
        var btnText = btn.find('.btn-text');
        var spinner = btn.find('.spinner-border');
        var message = $('#verification-message');
        
        var orderId = $('#order_id').val().trim();
        var phone = $('#phone').val().trim();
        var itemId = $('#item_id').val();
        
        if (!orderId || !phone) {
            showMessage(message, 'Please fill in all required fields.', 'danger');
            return;
        }
        
        btn.prop('disabled', true);
        btnText.hide();
        spinner.show();
        message.hide();
        
        $.ajax({
            url: '{{ route("front.review.verify") }}',
            method: 'POST',
            data: {
                order_id: orderId,
                phone: phone,
                item_id: itemId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Fill customer details
                    $('#customer_name_display').val(response.customer_name);
                    $('#customer_phone_display').val(response.phone);
                    $('#verified_order_id').val(orderId);
                    $('#verified_phone').val(phone);
                    $('#verified_item_id').val(itemId);
                    
                    // Show review form
                    $('#review-verification-form').hide();
                    $('#review-submit-form').show();
                } else {
                    showMessage(message, response.message, 'danger');
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                var errorMessage = response && response.message ? response.message : 'An error occurred. Please try again.';
                showMessage(message, errorMessage, 'danger');
            },
            complete: function() {
                btn.prop('disabled', false);
                btnText.show();
                spinner.hide();
            }
        });
    });
    
    // Submit review
    $('#submit-review-btn').click(function() {
        var btn = $(this);
        var btnText = btn.find('.btn-text');
        var spinner = btn.find('.spinner-border');
        var message = $('#submit-message');
        
        var rating = $('#rating_value').val();
        
        if (!rating) {
            showMessage(message, 'Please select a rating.', 'danger');
            return;
        }
        
        btn.prop('disabled', true);
        btnText.hide();
        spinner.show();
        message.hide();
        
        var formData = new FormData($('#submit-review-form')[0]);
        formData.append('_token', '{{ csrf_token() }}');
        
        $.ajax({
            url: '{{ route("front.review.submit") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showMessage(message, response.message, 'success');
                    setTimeout(function() {
                        $('#review-modal').modal('hide');
                        location.reload(); // Reload to show new review
                    }, 2000);
                } else {
                    showMessage(message, response.message, 'danger');
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                var errorMessage = response && response.message ? response.message : 'An error occurred. Please try again.';
                showMessage(message, errorMessage, 'danger');
            },
            complete: function() {
                btn.prop('disabled', false);
                btnText.show();
                spinner.hide();
            }
        });
    });
    
    // Back to verification
    $('#back-to-verification').click(function() {
        $('#review-submit-form').hide();
        $('#review-verification-form').show();
        $('#verification-message').hide();
        $('#submit-message').hide();
    });
    
    // Reset modal when closed
    $('#review-modal').on('hidden.bs.modal', function() {
        $('#review-verification-form').show();
        $('#review-submit-form').hide();
        $('#verify-order-form')[0].reset();
        $('#submit-review-form')[0].reset();
        $('#rating_value').val('');
        $('.star-rating').removeClass('active');
        $('#verification-message').hide();
        $('#submit-message').hide();
    });
    
    function showMessage(element, text, type) {
        element.removeClass('alert-success alert-danger alert-warning alert-info')
               .addClass('alert alert-' + type)
               .text(text)
               .show();
    }
    
    // Manual close handlers to ensure modal closes properly
    $(document).on('click', '#review-modal .close', function(e) {
        console.log('Close button clicked');
        $('#review-modal').modal('hide');
    });
    
    $(document).on('click', '#review-modal .btn-secondary', function(e) {
        console.log('Cancel button clicked');
        $('#review-modal').modal('hide');
    });
    
    // Back to verification button
    $(document).on('click', '#back-to-verification', function(e) {
        e.preventDefault();
        $('#review-verification-form').show();
        $('#review-submit-form').hide();
    });

    // Multiple image upload handling
    let selectedImages = [];
    
    $('#review_images').on('change', function(e) {
        const files = Array.from(e.target.files);
        
        // Validate file count
        if (files.length > 3) {
            alert('You can upload maximum 3 images');
            return;
        }
        
        // Validate file sizes
        for (let file of files) {
            if (file.size > 2 * 1024 * 1024) { // 2MB
                alert('File size should not exceed 2MB');
                return;
            }
        }
        
        selectedImages = files;
        displayImagePreviews(files);
    });
    
    function displayImagePreviews(files) {
        // Hide all previews first
        for (let i = 0; i < 3; i++) {
            $('#preview-' + i).hide();
            $('.remove-image[data-index="' + i + '"]').hide();
        }
        
        // Show previews for selected files
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-' + index).attr('src', e.target.result).show();
                $('.remove-image[data-index="' + index + '"]').show();
            };
            reader.readAsDataURL(file);
        });
        
        $('#image-preview').show();
    }
    
    // Remove image functionality
    $(document).on('click', '.remove-image', function() {
        const index = $(this).data('index');
        selectedImages.splice(index, 1);
        
        // Update file input
        const dt = new DataTransfer();
        selectedImages.forEach(file => dt.items.add(file));
        $('#review_images')[0].files = dt.files;
        
        // Update previews
        displayImagePreviews(selectedImages);
        
        // Hide preview container if no images
        if (selectedImages.length === 0) {
            $('#image-preview').hide();
        }
    });
    
    // Review filtering functionality (only for modal)
    function filterReviews() {
        const ratingFilter = $('#rating-filter').val();
        const imageFilter = $('#image-filter').val();
        
        $('#all-reviews-container .single-review').each(function() {
            const $review = $(this);
            const reviewRating = parseInt($review.data('rating'));
            const hasImagesStr = $review.data('has-images');
            const hasImages = hasImagesStr === 'true' || hasImagesStr === true;
            
            let showReview = true;
            
            // Filter by rating
            if (ratingFilter !== 'all' && reviewRating != parseInt(ratingFilter)) {
                showReview = false;
            }
            
            // Filter by images
            if (imageFilter === 'with-images' && !hasImages) {
                showReview = false;
            } else if (imageFilter === 'without-images' && hasImages) {
                showReview = false;
            }
            
            // Show/hide review
            if (showReview) {
                $review.show();
            } else {
                $review.hide();
            }
        });
        
        // Update "no reviews" message
        const visibleReviews = $('#all-reviews-container .single-review:visible').length;
        if (visibleReviews === 0) {
            if ($('#no-reviews-message').length === 0) {
                $('#all-reviews-container').append(`
                    <div id="no-reviews-message" class="text-center p-5">
                        <p class="text-muted">{{ __('No reviews match the selected filters.') }}</p>
                    </div>
                `);
            }
        } else {
            $('#no-reviews-message').remove();
        }
    }
    
    // Initialize filters (only for modal)
    $('#rating-filter, #image-filter').on('change', filterReviews);
    
    // Reset filters when modal is closed
    $('#all-reviews-modal').on('hidden.bs.modal', function() {
        $('#rating-filter').val('all');
        $('#image-filter').val('all');
        $('#all-reviews-container .single-review').show();
        $('#no-reviews-message').remove();
    });
    
    // Image slider functionality
    let currentSliderImages = [];
    let currentSliderIndex = 0;
    
    // Global function to open image slider
    window.openImageSlider = function(reviewId, imageIndex, images) {
        currentSliderImages = images;
        currentSliderIndex = imageIndex;
        
        // Convert relative paths to absolute URLs
        let imageSrc = images[imageIndex];
        if (imageSrc.startsWith('assets/')) {
            // Convert to absolute URL using Laravel asset() function equivalent
            imageSrc = window.location.origin + '/' + imageSrc;
        } else if (!imageSrc.startsWith('http') && !imageSrc.startsWith('/')) {
            imageSrc = window.location.origin + '/' + imageSrc;
        }
        
        $('#slider-main-image').attr('src', imageSrc);
        updateSliderCounter();
        updateSliderButtons();
        
        $('#image-slider-modal').modal('show');
    };
    
    function updateSliderCounter() {
        $('#slider-counter').text(`${currentSliderIndex + 1} / ${currentSliderImages.length}`);
    }
    
    function updateSliderButtons() {
        $('#slider-prev').prop('disabled', currentSliderIndex === 0);
        $('#slider-next').prop('disabled', currentSliderIndex === currentSliderImages.length - 1);
    }
    
    // Slider navigation
    $('#slider-prev').on('click', function() {
        if (currentSliderIndex > 0) {
            currentSliderIndex--;
            let imageSrc = currentSliderImages[currentSliderIndex];
            if (imageSrc.startsWith('assets/')) {
                imageSrc = window.location.origin + '/' + imageSrc;
            } else if (!imageSrc.startsWith('http') && !imageSrc.startsWith('/')) {
                imageSrc = window.location.origin + '/' + imageSrc;
            }
            $('#slider-main-image').attr('src', imageSrc);
            updateSliderCounter();
            updateSliderButtons();
        }
    });
    
    $('#slider-next').on('click', function() {
        if (currentSliderIndex < currentSliderImages.length - 1) {
            currentSliderIndex++;
            let imageSrc = currentSliderImages[currentSliderIndex];
            if (imageSrc.startsWith('assets/')) {
                imageSrc = window.location.origin + '/' + imageSrc;
            } else if (!imageSrc.startsWith('http') && !imageSrc.startsWith('/')) {
                imageSrc = window.location.origin + '/' + imageSrc;
            }
            $('#slider-main-image').attr('src', imageSrc);
            updateSliderCounter();
            updateSliderButtons();
        }
    });
    
    // Keyboard navigation for slider
    $(document).on('keydown', function(e) {
        if ($('#image-slider-modal').hasClass('show')) {
            if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                $('#slider-prev').click();
            } else if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                $('#slider-next').click();
            } else if (e.key === 'Escape') {
                $('#image-slider-modal').modal('hide');
            }
        }
    });
    
    // Ensure close button works properly
    $(document).on('click', '.slider-close', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#image-slider-modal').modal('hide');
    });
    
    // Also handle clicks on the modal backdrop
    $('#image-slider-modal').on('click', function(e) {
        if (e.target === this) {
            $('#image-slider-modal').modal('hide');
        }
    });
});
</script>

<!-- All Reviews Modal -->
<div class="modal fade" id="all-reviews-modal" tabindex="-1" role="dialog" aria-labelledby="allReviewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allReviewsModalLabel">{{ __('All Reviews') }} ({{ $totalReviews }})</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <!-- Filter Controls -->
                <div class="reviews-filter-section mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="rating-filter" class="form-label">{{ __('Filter by Rating') }}</label>
                                <select class="form-control form-control-sm" id="rating-filter">
                                    <option value="all">{{ __('All Ratings') }}</option>
                                    <option value="5">5 {{ __('Stars') }}</option>
                                    <option value="4">4 {{ __('Stars') }}</option>
                                    <option value="3">3 {{ __('Stars') }}</option>
                                    <option value="2">2 {{ __('Stars') }}</option>
                                    <option value="1">1 {{ __('Star') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="image-filter" class="form-label">{{ __('Filter by Images') }}</label>
                                <select class="form-control form-control-sm" id="image-filter">
                                    <option value="all">{{ __('All Reviews') }}</option>
                                    <option value="with-images">{{ __('With Images') }}</option>
                                    <option value="without-images">{{ __('Without Images') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="all-reviews-container">
                    @php
                        $allApprovedReviews = \App\Models\Review::where('item_id', $item->id)->where('status', 'approved')->orderBy('created_at', 'desc')->get();
                    @endphp
                    @forelse ($allApprovedReviews as $review)
                        @php
                            $reviewImages = $review->getReviewImages();
                            $hasImages = !empty($reviewImages);
                        @endphp
                        <div class="single-review mb-4 border-bottom pb-3" 
                             data-rating="{{ $review->rating }}" 
                             data-has-images="{{ $hasImages ? 'true' : 'false' }}">
                            <div class="row">
                                <!-- Left Side: Avatar, Name, Date, Review Text -->
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar-circle me-3">
                                            {{ strtoupper(substr($review->customer_name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $review->customer_name }}</h5>
                                            <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                                            @if($review->review_text)
                                                <p class="comment-text mt-2 mb-0">{{ $review->review_text }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Side: Stars and Images -->
                                <div class="col-md-4">
                                    <div class="text-right">
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        @php
                                            $reviewImages = $review->getReviewImages();
                                        @endphp
                                        @if(!empty($reviewImages))
                                            <div class="review-images-right">
                                                @foreach($reviewImages as $index => $image)
                                                    <img src="{{ asset($image) }}" class="img-fluid rounded review-image-thumb d-inline-block" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #ddd; margin: 2px; cursor: pointer;" 
                                                         alt="Review Image {{ $index + 1 }}"
                                                         onclick="openImageSlider({{ $review->id }}, {{ $index }}, {{ json_encode($reviewImages) }})">
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if($review->hasAdminReply())
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="admin-reply-compact">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-user-shield text-primary me-2 mt-1" style="font-size: 12px;"></i>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <strong class="text-primary" style="font-size: 13px;">{{ __('Reply by HomeFindBD.com') }}</strong>
                                                        <small class="text-muted ms-auto" style="font-size: 11px;">
                                                            @if($review->admin_reply_date)
                                                                {{ \Carbon\Carbon::parse($review->admin_reply_date)->format('M d, Y') }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                    <p class="mb-0 text-dark" style="font-size: 13px; line-height: 1.3;">{{ $review->admin_reply }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center p-5">
                            <p class="text-muted">{{ __('No reviews found.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Slider Modal -->
<div class="modal fade image-slider-modal" id="image-slider-modal" tabindex="-1" role="dialog" aria-labelledby="imageSliderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="image-slider-container">
                <button type="button" class="slider-close" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
                
                <img id="slider-main-image" class="slider-image" src="" alt="Review Image">
                
                <button type="button" class="slider-nav prev" id="slider-prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <button type="button" class="slider-nav next" id="slider-next">
                    <i class="fas fa-chevron-right"></i>
                </button>
                
                <div class="slider-counter" id="slider-counter">
                    1 / 1
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

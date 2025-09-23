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
                        <div class="mb-3">
                            <div class="rating-stars d-inline-block gmr-3">
                                {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                            </div>
                            @if ($item->is_stock())
                                <span class="text-success  d-inline-block">{{ __('In Stock') }} <b>({{ $item->stock }}
                                        @lang('items'))</b></span>
                            @else
                                <span class="text-danger  d-inline-block">{{ __('Out of stock') }}</span>
                            @endif
                        </div>


                        @if ($item->is_type == 'flash_deal')
                            @if (date('d-m-y') != \Carbon\Carbon::parse($item->date)->format('d-m-y'))
                                <div class="countdown countdown-alt mb-3" data-date-time="{{ $item->date }}">
                                </div>
                            @endif
                        @endif

                        <span class="h3 d-block price-area">
                            @if ($item->previous_price != 0)
                                <small
                                    class="d-inline-block"><del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del></small>
                            @endif
                            <span id="main_price" class="main-price">{{ PriceHelper::grandCurrencyPrice($item) }}</span>
                        </span>

                        <p class="text-muted">{{ $item->sort_details }} <a href="#details"
                                class="scroll-to">{{ __('Read more') }}</a></p>

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
                        <div class="row align-items-end pb-4">
                            <div class="col-sm-12">
                                @if ($item->item_type == 'normal')
                                    <div class="qtySelector product-quantity">
                                        <span class="decreaseQty subclick"><i class="fas fa-minus "></i></span>
                                        <input type="text" class="qtyValue cart-amount" value="1">
                                        <span class="increaseQty addclick"><i class="fas fa-plus"></i></span>
                                        <input type="hidden" value="3333" id="current_stock">
                                    </div>
                                @endif
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

                            </div>
                        </div>

                        <div class="div">
                            <div class="t-c-b-area">
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
                            </div>

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
                @forelse ($reviews as $review)
                    <div class="single-review">
                        <div class="comment">
                            <div class="comment-author-ava"><img class="lazy"
                                    data-src="{{ asset('storage/images/' . $review->user->photo) }}"
                                    alt="Comment author">
                            </div>
                            <div class="comment-body">
                                <div class="comment-header d-flex flex-wrap justify-content-between">
                                    <div>
                                        <h4 class="comment-title mb-1">{{ $review->subject }}</h4>
                                        <span>{{ $review->user->first_name }}</span>
                                        <span class="ml-3">{{ $review->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <div class="rating-stars">
                                            @php
                                                for ($i = 0; $i < $review->rating; $i++) {
                                                    echo "<i class = 'far fa-star filled'></i>";
                                                }
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                                <p class="comment-text  mt-2">{{ $review->review }}</p>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card p-5">
                        {{ __('No Review') }}
                    </div>
                @endforelse
                <div class="row mt-15">
                    <div class="col-lg-12 text-center">
                        {{ $reviews->links() }}
                    </div>
                </div>

            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="d-inline align-baseline display-3 mr-1">
                                {{ round($item->reviews->avg('rating'), 2) }}</div>
                            <div class="d-inline align-baseline text-sm text-warning mr-1">
                                <div class="rating-stars">
                                    {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="pt-3">
                            <label class="text-medium text-sm">5 {{ __('stars') }} <span class="text-muted">-
                                    {{ $item->reviews->where('status', 1)->where('rating', 5)->count() }}</span></label>
                            <div class="progress margin-bottom-1x">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $item->reviews->where('status', 1)->where('rating', 5)->sum('rating') * 20 }}%; height: 2px;"
                                    aria-valuenow="100"
                                    aria-valuemin="{{ $item->reviews->where('rating', 5)->sum('rating') * 20 }}"
                                    aria-valuemax="100"></div>
                            </div>
                            <label class="text-medium text-sm">4 {{ __('stars') }} <span class="text-muted">-
                                    {{ $item->reviews->where('status', 1)->where('rating', 4)->count() }}</span></label>
                            <div class="progress margin-bottom-1x">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $item->reviews->where('status', 1)->where('rating', 4)->sum('rating') * 20 }}%; height: 2px;"
                                    aria-valuenow="{{ $item->reviews->where('rating', 4)->sum('rating') * 20 }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <label class="text-medium text-sm">3 {{ __('stars') }} <span class="text-muted">-
                                    {{ $item->reviews->where('status', 1)->where('rating', 3)->count() }}</span></label>
                            <div class="progress margin-bottom-1x">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $item->reviews->where('rating', 3)->sum('rating') * 20 }}%; height: 2px;"
                                    aria-valuenow="{{ $item->reviews->where('rating', 3)->sum('rating') * 20 }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <label class="text-medium text-sm">2 {{ __('stars') }} <span class="text-muted">-
                                    {{ $item->reviews->where('status', 1)->where('rating', 2)->count() }}</span></label>
                            <div class="progress margin-bottom-1x">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $item->reviews->where('status', 1)->where('rating', 2)->sum('rating') * 20 }}%; height: 2px;"
                                    aria-valuenow="{{ $item->reviews->where('rating', 2)->sum('rating') * 20 }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <label class="text-medium text-sm">1 {{ __('star') }} <span class="text-muted">-
                                    {{ $item->reviews->where('status', 1)->where('rating', 1)->count() }}</span></label>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $item->reviews->where('status', 1)->where('rating', 1)->sum('rating') * 20 }}; height: 2px;"
                                    aria-valuenow="0"
                                    aria-valuemin="{{ $item->reviews->where('rating', 1)->sum('rating') * 20 }}"
                                    aria-valuemax="100"></div>
                            </div>
                        </div>
                        @if (Auth::user())
                            <div class="pb-2"><a class="btn btn-primary btn-block" href="#"
                                    data-bs-toggle="modal"
                                    data-bs-target="#leaveReview"><span>{{ __('Leave a Review') }}</span></a></div>
                        @else
                            <div class="pb-2"><a class="btn btn-primary btn-block"
                                    href="{{ route('user.login') }}"><span>{{ __('Login') }}</span></a></div>
                        @endif
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
                                            <a href="{{ route('front.product', $related->slug) }}" class="btn btn-primary btn-sm" style="color: white !important; text-decoration: none !important;">
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
                        <h6>{{ __('Delivery Information') }}</h6>

                        <form id="checkoutBilling" action="{{ route('front.order.direct') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="checkout-name">{{ __('Name') }}</label>
                                        <input class="form-control" name="bill_first_name" type="text" required
                                            id="checkout-name" value="{{ isset($user) ? $user->first_name . ' ' . $user->last_name : '' }}" placeholder="Enter your full name">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="checkout-phone">{{ __('Phone Number') }}</label>
                                        <input class="form-control" name="bill_phone" type="text"
                                            id="checkout-phone" required
                                            value="{{ isset($user) ? $user->phone : '' }}" placeholder="Enter your phone number">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="checkout-address1">{{ __('Address') }}</label>
                                        <textarea class="form-control" name="bill_address1" required
                                            id="checkout-address1" rows="3" placeholder="Enter your delivery address">{{ isset($user) ? $user->bill_address1 : '' }}</textarea>
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
                        <h3 class="widget-title">{{ __('Order Summary') }}</h3>
                        @php
                            $base_price = $item->discount_price;
                            $cart_total = $base_price; // Default quantity is 1
                            $tax = 0;
                            $grand_total = $cart_total + $tax;
                        @endphp

                        <table class="table">
                            <tr>
                                <td>{{ __('Cart subtotal') }}:</td>
                                <td class="text-gray-dark" id="cart-subtotal">{{ PriceHelper::setCurrencyPrice($cart_total) }}</td>
                            </tr>

                            <tr>
                                <td>{{ __('Delivery Fee') }}:</td>
                                <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice(0) }}</td>
                            </tr>
                            <tr>
                                <td class="text-lg text-primary">{{ __('Order total') }}</td>
                                <td class="text-lg text-primary grand_total_set" id="order-total">{{ PriceHelper::setCurrencyPrice($grand_total) }}
                                </td>
                            </tr>
                        </table>
                        
                        <!-- Hidden price data for JavaScript -->
                        <input type="hidden" id="base-price" value="{{ $item->discount_price }}">
                        <input type="hidden" id="currency-sign" value="{{ PriceHelper::setCurrencySign() }}">
                        <input type="hidden" id="currency-direction" value="{{ $setting->currency_direction }}">
                        
                    </section>

                    <!-- Order Now Button-->
                    <div class="mt-4">
                        <button id="order_now_btn"
                            class="btn btn-primary btn-lg w-100 order_now_btn p-0" type="submit">
                            <span>{{ __('Order Now') }}</span>
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
        
        var subtotal = (basePrice + attributePrice) * quantity;
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
    
    // Handle Buy Now button click (scroll to checkout section)
    $(document).on("click", "#but_to_cart", function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
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
        
        // Place order directly (no cart needed)
        $.ajax({
            url: "{{ route('front.order.direct') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                item_id: itemId,
                quantity: quantity,
                bill_first_name: $('input[name="bill_first_name"]').val(),
                bill_phone: $('input[name="bill_phone"]').val(),
                bill_address1: $('textarea[name="bill_address1"]').val(),
                selected_attributes: JSON.stringify(selectedAttributes)
            },
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
</style>
@endsection

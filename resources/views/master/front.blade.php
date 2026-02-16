@php
    // Define pages where header and footer should always be hidden
    $alwaysHideHeaderFooter = [
        'front.checkout.billing',  // Checkout page
        'front.checkout.success',  // Order success page
        'front.order.track',       // Track order page
    ];
    
    $currentRoute = Route::currentRouteName();
    
    // Start with default: show header/footer
    $shouldHideHeaderFooter = false;
    
    // Check if route is in always-hide list
    if (in_array($currentRoute, $alwaysHideHeaderFooter)) {
        $shouldHideHeaderFooter = true;
    }
    // Check product page setting
    elseif ($currentRoute == 'front.product') {
        // Hide if setting is disabled (default 0), show if enabled (1)
        $shouldHideHeaderFooter = !$setting->show_header_footer_product_page;
    }
    // Check shop page setting
    elseif ($currentRoute == 'front.products') {
        // Hide if setting is disabled (default 0), show if enabled (1)
        $shouldHideHeaderFooter = !$setting->show_header_footer_shop_page;
    }
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    @if (url()->current() == route('front.index'))
        <title>@yield('hometitle')</title>
    @else
        <title>{{ $setting->title }} -@yield('title')</title>
    @endif

    <!-- SEO Meta Tags: default site-level. Product/custom pages override these via @yield('meta') below. -->
    <meta name="author" content="GeniusDevs">
    <meta name="distribution" content="web">
    <meta name="description" content="{{ $setting->meta_description }}">
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="image" content="{{ asset('storage/images/' . $setting->meta_image) }}">
    
    @php
        // Use home_page_title (tagline) if available, otherwise use title
        $ogTitle = !empty($setting->home_page_title) ? $setting->home_page_title : $setting->title;
        // Use ONLY meta_description for social media sharing - no fallbacks to avoid showing old text
        $ogDescription = !empty($setting->meta_description) ? trim($setting->meta_description) : '';
    @endphp
    
    <!-- Open Graph / Facebook: default. Product page overrides via @yield('meta') below. -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ asset('storage/images/' . $setting->meta_image) }}">
    <meta property="og:image:secure_url" content="{{ asset('storage/images/' . $setting->meta_image) }}" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="627" />
    <meta property="og:site_name" content="{{ $setting->title }}">
    
    <!-- Twitter Card: default. Product page overrides via @yield('meta') below. -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ asset('storage/images/' . $setting->meta_image) }}">
    
    @yield('meta')

    <!-- Mobile Specific Meta Tag-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Favicon Icons-->
    <link rel="icon" type="image/png" href="{{ asset('storage/images/' . $setting->favicon) }}">
    <link rel="apple-touch-icon" href="{{ asset('storage/images/' . $setting->favicon) }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('storage/images/' . $setting->favicon) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/images/' . $setting->favicon) }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('storage/images/' . $setting->favicon) }}">

    <!-- Vendor Styles including: Bootstrap, Font Icons, Plugins, etc.-->
    <link rel="stylesheet" media="screen" href="{{ asset('assets/front/css/plugins.min.css') }}">

    <!-- Latest Font Awesome for all icons (including TikTok) -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer">
    
    @yield('styleplugins')
    
    <link id="mainStyles" rel="stylesheet" media="screen" href="{{ asset('assets/front/css/styles.min.css') }}">
    
    <link id="mainStyles" rel="stylesheet" media="screen" href="{{ asset('assets/front/css/responsive.css') }}">
    <!-- Color css -->
    <link
        href="{{ asset('assets/front/css/color.php?primary_color=') . str_replace('#', '', $setting->primary_color) }}"
        rel="stylesheet">

    <!-- Modernizr-->
    <script src="{{ asset('assets/front/js/modernizr.min.js') }}"></script>

    @if (DB::table('languages')->where('is_default', 1)->first()->rtl == 1)
        <link rel="stylesheet" href="{{ asset('assets/front/css/rtl.css') }}">
    @endif
    <style>
        {{ $setting->custom_css }}
        
        /* Checkout button consistent styling */
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
        .checkout-btn:hover span,
        .checkout-btn:active span,
        .checkout-btn:focus span,
        .checkout-btn:hover i,
        .checkout-btn:active i,
        .checkout-btn:focus i {
            color: #DD2476 !important;
        }
        
        /* Custom styles for pages without header/footer */
        @if ($shouldHideHeaderFooter)
        body {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }
        
        .page-title {
            margin-top: 20px;
        }
        
        /* Only make checkout page full width, keep product page with normal padding */
        @if ($currentRoute == 'front.checkout.billing' || $currentRoute == 'front.checkout.success' || $currentRoute == 'front.order.track')
        .container {
            max-width: 100%;
        }
        
        .checkut-page {
            padding: 20px 0;
        }
        @endif
        
        /* Product page should have normal padding */
        @if ($currentRoute == 'front.product')
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        @endif
        @endif

        /* Override button colors with gradient - Replace #FF6A00 */
        .btn-primary,
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active,
        button.btn-primary,
        a.btn-primary,
        .btn:not(.btn-secondary):not(.btn-outline-primary):not(.btn-outline-success):not(.btn-outline-danger):not(.btn-outline-info):not(.btn-outline-warning):not(.btn-outline-white) {
            background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%) !important;
            border: none !important;
            color: white !important;
        }
        
        .btn-success,
        .btn-success:hover,
        .btn-success:focus,
        .btn-success:active {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
            border: none !important;
            color: white !important;
        }
        
        .btn-danger,
        .btn-danger:hover,
        .btn-danger:focus,
        .btn-danger:active {
            background: linear-gradient(135deg, #ed213a 0%, #93291e 100%) !important;
            border: none !important;
            color: white !important;
        }
        
        .btn-info,
        .btn-info:hover,
        .btn-info:focus,
        .btn-info:active {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
            border: none !important;
            color: white !important;
        }
        
        .btn-warning,
        .btn-warning:hover,
        .btn-warning:focus,
        .btn-warning:active {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important;
            border: none !important;
            color: white !important;
        }
        
        /* Override other elements that use #FF6A00 with gradient text */
        .product-card .product-price,
        .text-primary:not(.header-menu-item a):not(.header-menu-item),
        .product-card .product-title > a:hover,
        .product-card .product-category > a:hover {
            background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }

        /* Custom styles for new header */
        .top-tagline-bar {
            font-size: 13px;
        }
        .top-tagline-bar .tagline-item {
            display: inline-block;
        }
        .main-header-area {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .main-header-sticky {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: transform 0.3s ease, opacity 0.3s ease;
            transform: translateY(-100%);
            opacity: 0;
        }
        
        .main-header-sticky.show {
            transform: translateY(0);
            opacity: 1;
        }
        .search-box-wrapper .input-group {
            display: flex;
            align-items: stretch;
        }
        .search-box-wrapper .form-control {
            flex: 1;
        }
        .search-box-wrapper button {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .search-suggestions {
            top: 100%;
            left: 0;
        }
        .product-card.p-col {
            margin: 5px 0px;
        }
        .search-suggestions .s-r-inner {
            padding: 0;
        }
        .search-suggestions .product-card {
            border-bottom: 1px solid #e9ecef;
            padding: 0px 10px;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .search-suggestions .product-card:hover {
            background: #f8f9fa;
            border-color: #4E65FF !important;
        }
        .search-suggestions .product-card .product-thumb {
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            display: block;
            margin-right: 0px !important;
        }
        .search-suggestions .product-card .product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
        }
        .search-suggestions .product-card .product-card-body {
            flex: 1;
            min-width: 0;
        }
        .search-suggestions .product-card .product-title {
            font-size: 14px;
            margin-bottom: 2px;
            line-height: 1.3;
            margin-bottom: -15px !important;
        }
        .search-suggestions .product-card .product-title a {
            text-decoration: none;
            color: #333;
        }
        .search-suggestions .product-card .rating-stars {
            margin-bottom: 2px;
            font-size: 12px;
        }
        .search-suggestions .product-card .product-price-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .search-suggestions .product-card .old-price {
            font-size: 13px;
            color: #999;
            text-decoration: line-through;
        }
        .search-suggestions .product-card .main-price {
            font-size: 15px;
            font-weight: 600;
            color: #FF512F;
        }
        .search-suggestions .bottom-area {
            padding: 8px 10px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            background: #f8f9fa;
        }
        .search-suggestions .bottom-area a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }
        .header-menu-items .header-menu-item a:hover {
            color: #667eea !important;
        }
        
        /* Phone number styling */
        .header-phone-number {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        
        .header-phone-number a {
            white-space: nowrap;
        }
        
        @media (max-width: 991px) {
            /* Hide main header area on mobile */
            .main-header-area {
                display: none !important;
            }
            
            /* Show only first top bar on mobile - hide others */
            .top-tagline-bar {
                display: block !important;
            }
            
            /* Mobile header layout */
            .mobile-header-layout {
                display: block !important;
            }
            
            .mobile-header-sticky {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                background: #fff;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transition: transform 0.3s ease, opacity 0.3s ease;
                transform: translateY(-100%);
                opacity: 0;
            }
            
            .mobile-header-sticky.show {
                transform: translateY(0);
                opacity: 1;
            }
            
            /* Ensure mobile menu items are properly styled */
            .mobile-menu .slideable-menu ul li a {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 12px 15px;
                font-size: 15px;
                width: 100%;
            }
            
            .mobile-menu .slideable-menu ul li a span {
                flex: 1;
            }
            
            .mobile-menu .slideable-menu ul li a i.fas {
                margin-right: 10px;
                width: 20px;
                text-align: center;
                flex-shrink: 0;
            }
            
            .mobile-menu .slideable-menu ul li a i.icon-chevron-right {
                flex-shrink: 0;
                margin-left: auto;
            }
            
        }
        
        @media (min-width: 992px) {
            /* Hide mobile header layout on desktop */
            .mobile-header-layout {
                display: none !important;
            }
        }
        
        @media (max-width: 768px) {
            .top-tagline-bar .tagline-item {
                font-size: 11px;
            }
            .header-menu-items {
                justify-content: center !important;
            }
            .header-phone-number {
                justify-content: center !important;
                margin-top: 10px;
            }
        }
        
        /* Shared Order Now / Buy Now button style (home + single product page) */
        .order-now-btn-home {
            font-size: 15px !important;
            border: 2px solid #DD2476 !important;
        }
        .order-now-btn-home:hover,
        .order-now-btn-home:active,
        .order-now-btn-home:focus {
            background: transparent !important;
            background-image: none !important;
            background-color: transparent !important;
            border: 2px solid #DD2476 !important;
            color: #DD2476 !important;
            box-shadow: none !important;
        }
        .order-now-btn-home:hover *,
        .order-now-btn-home:active *,
        .order-now-btn-home:focus *,
        .order-now-btn-home:hover i,
        .order-now-btn-home:active i,
        .order-now-btn-home:focus i {
            color: #DD2476 !important;
        }
        /* Shared Add to Cart button style (home + single product page) */
        .add_to_single_cart {
            font-size: 15px !important;
        }
        .add_to_single_cart:hover,
        .add_to_single_cart:focus {
            background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%) !important;
            border: 2px solid transparent !important;
            color: #fff !important;
        }
        .add_to_single_cart:hover *,
        .add_to_single_cart:focus *,
        .add_to_single_cart:hover i,
        .add_to_single_cart:focus i {
            color: #fff !important;
        }
        /* Add to cart feedback: success state and cart count animation */
        .add_to_single_cart.add-to-cart-success,
        #add_to_cart.add-to-cart-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
            border-color: transparent !important;
            color: #fff !important;
            pointer-events: none;
        }
        .add_to_single_cart.add-to-cart-success *,
        #add_to_cart.add-to-cart-success * {
            color: #fff !important;
        }
        .add_to_single_cart.add-to-cart-loading,
        #add_to_cart.add-to-cart-loading {
            opacity: 0.8;
            cursor: wait;
        }
        @keyframes cartCountBump {
            0% { transform: scale(1); }
            50% { transform: scale(1.35); }
            100% { transform: scale(1); }
        }
        .cart_count.cart-count-bump {
            animation: cartCountBump 0.5s ease;
        }
    </style>
    {{-- Google AdSense Start --}}
    @if ($setting->is_google_adsense == '1')
        {!! $setting->google_adsense !!}
    @endif
    {{-- Google AdSense End --}}

    {{-- Google AnalyTics Start --}}
    @if ($setting->is_google_analytics == '1')
        {!! $setting->google_analytics !!}
    @endif
    {{-- Google AnalyTics End --}}

    {{-- Facebook pixel  Start --}}
    @if ($setting->is_facebook_pixel == '1' || !empty($setting->facebook_pixel))
        {!! $setting->facebook_pixel !!}
    @endif
    {{-- Facebook pixel End --}}

    {{-- Google Tag Manager (Head) Start --}}
    @if ($setting->is_gtm == '1' || !empty($setting->gtm_head_code))
        {!! $setting->gtm_head_code !!}
    @endif
    {{-- Google Tag Manager (Head) End --}}

</head>
<!-- Body-->

<body
    class="
@if ($setting->theme == 'theme1') body_theme1
@elseif($setting->theme == 'theme2')
body_theme2
@elseif($setting->theme == 'theme3')
body_theme3
@elseif($setting->theme == 'theme4')
body_theme4 @endif
">

    {{-- Google Tag Manager (Body) Start --}}
    @if ($setting->is_gtm == '1' || !empty($setting->gtm_body_code))
        {!! $setting->gtm_body_code !!}
    @endif
    {{-- Google Tag Manager (Body) End --}}

    @if ($setting->is_loader == 1)
        <!-- Preloader Start -->
        @if ($setting->is_loader == 1)
            <div id="preloader">
                <img src="{{ asset('storage/images/' . $setting->loader) }}" alt="{{ __('Loading...') }}">
            </div>
        @endif

        <!-- Preloader endif -->
    @endif

    <!-- Header-->
    @if (!$shouldHideHeaderFooter)
    <header class="site-header navbar-sticky">
        <!-- Top Bar with Taglines -->
        <div class="top-tagline-bar" style="background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); padding: 10px 0;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-4 col-md-4 col-12 text-center text-md-start mb-2 mb-md-0">
                        <span class="tagline-item text-white" style="font-size: 13px; font-weight: 500;">
                            <i class="{{ $setting->tagline1_icon ?? 'fas fa-truck' }} me-1"></i>{{ $setting->tagline1_text ?? __('Free delivery over 500tk') }}
                        </span>
                    </div>
                    <div class="col-lg-4 col-md-4 col-12 text-center mb-2 mb-md-0 d-none d-md-block">
                        <span class="tagline-item text-white" style="font-size: 13px; font-weight: 500;">
                            <i class="{{ $setting->tagline2_icon ?? 'fas fa-percent' }} me-1"></i>{{ $setting->tagline2_text ?? __('5% off for website order') }}
                        </span>
                    </div>
                    <div class="col-lg-4 col-md-4 col-12 text-center text-md-end d-none d-md-block">
                        <span class="tagline-item text-white" style="font-size: 13px; font-weight: 500;">
                            <i class="{{ $setting->tagline3_icon ?? 'fas fa-gift' }} me-1"></i>{{ $setting->tagline3_text ?? __('2nd time? get your 15% voucher') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Header (Desktop Only) -->
        <div class="main-header-area d-none d-lg-block" id="main-header-original" style="background: #fff; border-bottom: 1px solid #e9ecef;">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Logo -->
                    <div class="col-lg-2 col-md-2 col-6">
                        <div class="site-branding">
                            <a class="site-logo" href="{{ route('front.index') }}" style="width: 170px">
                                <img src="{{ asset('storage/images/' . $setting->logo) }}" alt="{{ $setting->title }}" style="max-height: 60px; width: auto;">
                            </a>
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <div class="col-lg-4 col-md-5 col-12 mt-3 mt-md-0">
                        <div class="search-box-wrapper position-relative">
                            <form class="search-form" id="header_search_form" action="{{ route('front.products') }}" method="get">
                                <div class="input-group" style="max-width: 350px; height: 42px;">
                                    <input type="text" 
                                           class="form-control border-end-0" 
                                           id="__product__search" 
                                           name="search"
                                           data-target="{{ route('front.search.suggest') }}"
                                           placeholder="{{ __('Search by product name') }}"
                                           autocomplete="off"
                                           style="border-radius: 21px 0 0 21px; padding: 10px 20px; font-size: 14px; border-image-source: linear-gradient(rgba(19, 117, 215, 0.1), rgba(20, 156, 178, 0.21)); border-width: 1pt; border-image-slice: 1; background: #f8f9fa; height: 42px; line-height: 22px;">
                                    <button type="submit" class="btn d-flex align-items-center justify-content-center border-start-0" 
                                            style="border-radius: 0 21px 21px 0; 
                                                   padding: 0 20px; 
                                                   width: 42px;
                                                   height: 42px;
                                                   background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%) !important;
                                                   border: 2px solid transparent;
                                                   color: white;">
                                        <i class="icon-search" style="font-size: 18px; line-height: 1;"></i>
                                    </button>
                                </div>
                                <div class="search-suggestions position-absolute w-100 bg-white shadow-lg rounded mt-1 d-none" id="search_suggestions" style="z-index: 1000; max-height: 400px; overflow-y: auto; border: 1px solid #e9ecef; max-width: 350px;">
                                    <!-- Search suggestions will appear here -->
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Menu Items -->
                    <div class="col-lg-4 col-md-3 col-6">
                        <div class="header-menu-items d-flex justify-content-end align-items-center flex-wrap">
                            <!-- Categories -->
                            <div class="header-menu-item me-3 mb-2 mb-md-0">
                                <a href="{{ route('front.categories') }}" class="text-dark text-decoration-none d-flex align-items-center" style="font-size: 14px; font-weight: 500;">
                                    <i class="fas fa-th-large me-1"></i>{{__('Categories')}}
                                </a>
                            </div>

                            <!-- Shop -->
                            <div class="header-menu-item me-3 mb-2 mb-md-0">
                                <a href="{{ route('front.products') }}" class="text-dark text-decoration-none d-flex align-items-center" style="font-size: 14px; font-weight: 500;">
                                    <i class="fas fa-shopping-bag me-1"></i>{{__('Shop')}}
                                </a>
                            </div>

                            <!-- Reviews -->
                            <div class="header-menu-item me-3 mb-2 mb-md-0">
                                <a href="{{ route('front.index') }}#reviews-section" class="text-dark text-decoration-none d-flex align-items-center" style="font-size: 14px; font-weight: 500;">
                                    <i class="fas fa-star me-1"></i>{{__('Reviews')}}
                                </a>
                            </div>

                            <!-- Best Selling -->
                            <div class="header-menu-item mb-2 mb-md-0">
                                <a href="{{ route('front.index') }}#best-selling-section" class="text-dark text-decoration-none d-flex align-items-center" style="font-size: 14px; font-weight: 500;">
                                    <i class="fas fa-fire me-1"></i>{{__('Best Selling')}}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Cart -->
                    <div class="col-lg-2 col-md-2 col-6 d-flex justify-content-end align-items-center">
                        <a href="{{ route('front.cart') }}" class="text-decoration-none d-flex flex-column align-items-center justify-content-center fw-bold text-dark" style="font-size: 14px;">
                            <span class="cart_count badge rounded-pill mb-0" style="min-width: 20px; font-size: 11px; background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%);">{{ Session::has('cart') ? count(Session::get('cart')) : 0 }}</span>
                            <span class="d-flex align-items-center mt-1"><i class="fas fa-shopping-cart me-1"></i>{{ __('Cart') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Header Layout -->
        <div class="mobile-header-layout d-lg-none" id="mobile-header-original" style="background: #fff; border-bottom: 1px solid #e9ecef;">
            <!-- Mobile Top Bar: Menu, Logo -->
            <div class="container">
                <div class="row align-items-center py-2">
                    <!-- Menu Button -->
                    <div class="col-4">
                        <button class="mobile-menu-toggle" style="background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: none; border-radius: 8px; padding: 10px 18px; color: #fff; font-size: 16px; font-weight: 600; box-shadow: 0 2px 8px rgba(255, 81, 47, 0.3); transition: all 0.3s ease; display: inline-flex; align-items: center; cursor: pointer;">
                            <i class="icon-menu me-2"></i>{{__('Menu')}}
                        </button>
                    </div>
                    
                    <!-- Logo (Center) -->
                    <div class="col-4 text-center">
                        <a class="site-logo" href="{{ route('front.index') }}">
                            <img src="{{ asset('storage/images/' . $setting->logo) }}" alt="{{ $setting->title }}" style="max-height: 45px; width: auto;">
                        </a>
                    </div>

                    <!-- Cart (Mobile) -->
                    <div class="col-4 d-flex justify-content-end">
                        <a href="{{ route('front.cart') }}" class="text-decoration-none d-flex flex-column align-items-center text-dark" style="font-size: 13px;">
                            <span class="cart_count badge rounded-pill mb-0" style="min-width: 18px; font-size: 10px; background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%);">{{ Session::has('cart') ? count(Session::get('cart')) : 0 }}</span>
                            <span class="d-flex align-items-center mt-0"><i class="fas fa-shopping-cart me-1"></i>{{ __('Cart') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Search Bar Below -->
            <div class="container pb-2">
                <div class="search-box-wrapper position-relative">
                    <form class="search-form" id="header_search_form_mobile" action="{{ route('front.products') }}" method="get">
                        <div class="input-group" style="height: 42px;">
                            <input type="text" 
                                   class="form-control border-end-0" 
                                   id="__product__search_mobile" 
                                   name="search"
                                   data-target="{{ route('front.search.suggest') }}"
                                   placeholder="{{ __('Search by product name') }}"
                                   autocomplete="off"
                                   style="border-radius: 21px 0 0 21px; padding: 10px 20px; font-size: 14px; border-image-source: linear-gradient(rgba(19, 117, 215, 0.1), rgba(20, 156, 178, 0.21)); border-width: 1pt; border-image-slice: 1; background: #f8f9fa; height: 42px; line-height: 22px;">
                            <button type="submit" class="btn d-flex align-items-center justify-content-center border-start-0" 
                                    style="border-radius: 0 21px 21px 0; 
                                           padding: 0 20px; 
                                           width: 42px;
                                           height: 42px;
                                           background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%) !important;
                                           border: 2px solid transparent;
                                           color: white;">
                                <i class="icon-search" style="font-size: 18px; line-height: 1;"></i>
                            </button>
                        </div>
                        <div class="search-suggestions position-absolute w-100 bg-white shadow-lg rounded mt-1 d-none" id="search_suggestions_mobile" style="z-index: 1000; max-height: 400px; overflow-y: auto; border: 1px solid #e9ecef;">
                            <!-- Search suggestions will appear here -->
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Backdrop (separate element) -->
        <div class="mobile-menu-backdrop"></div>
        
        <!-- Mobile Menu - New Design -->
        <div class="mobile-menu-new">
            <div class="mobile-menu-container">
                <!-- Header -->
                <div class="mobile-menu-header">
                    <h4 class="mb-0 fw-bold text-white">{{ __('Navigation') }}</h4>
                    <button class="mobile-menu-close-btn" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Menu Items -->
                <nav class="mobile-menu-nav">
                    <ul class="mobile-menu-list">
                        <!-- Categories with Submenu -->
                        <li class="mobile-menu-item has-submenu">
                            <a href="javascript:void(0);" class="mobile-menu-link" data-toggle="submenu">
                                <div class="menu-icon-wrapper">
                                    <i class="fas fa-th-large"></i>
                                </div>
                                <span class="menu-text">{{ __('Categories') }}</span>
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </a>
                            <!-- Categories Submenu -->
                            <ul class="mobile-submenu">
                                @php
                                    $categories = App\Models\Category::whereStatus(1)->orderby('serial','asc')->get();
                                @endphp
                                @foreach ($categories as $category)
                                <li class="mobile-submenu-item">
                                    <a href="{{ route('front.products').'?category='.$category->slug }}" class="mobile-submenu-link">
                                        {{ $category->name }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        
                        <!-- Shop -->
                        <li class="mobile-menu-item">
                            <a href="{{ route('front.products') }}" class="mobile-menu-link">
                                <div class="menu-icon-wrapper">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <span class="menu-text">{{ __('Shop') }}</span>
                                <i class="fas fa-chevron-right menu-arrow"></i>
                            </a>
                        </li>
                        
                        <!-- Reviews -->
                        <li class="mobile-menu-item">
                            <a href="{{ route('front.index') }}#reviews-section" class="mobile-menu-link">
                                <div class="menu-icon-wrapper">
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="menu-text">{{ __('Reviews') }}</span>
                                <i class="fas fa-chevron-right menu-arrow"></i>
                            </a>
                        </li>
                        
                        <!-- Best Selling -->
                        <li class="mobile-menu-item">
                            <a href="{{ route('front.index') }}#best-selling-section" class="mobile-menu-link">
                                <div class="menu-icon-wrapper">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <span class="menu-text">{{ __('Best Selling') }}</span>
                                <i class="fas fa-chevron-right menu-arrow"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        
        <style>
        /* Hide mobile menu on desktop */
        @media (min-width: 992px) {
            .mobile-menu-new {
                display: none !important;
            }
            .mobile-menu-backdrop {
                display: none !important;
            }
        }
        
        /* Menu Button Styling */
        @media (max-width: 991px) {
            .mobile-menu-toggle {
                background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%) !important;
                border: none !important;
                border-radius: 8px !important;
                padding: 10px 18px !important;
                color: #fff !important;
                font-size: 16px !important;
                font-weight: 600 !important;
                box-shadow: 0 2px 8px rgba(255, 81, 47, 0.3) !important;
                transition: all 0.3s ease !important;
                display: inline-flex !important;
                align-items: center !important;
                cursor: pointer !important;
            }
            
            .mobile-menu-toggle:hover {
                box-shadow: 0 4px 15px rgba(255, 81, 47, 0.4) !important;
                transform: translateY(-1px);
            }
            
            .mobile-menu-toggle:active {
                transform: translateY(0);
            }
            
            /* Change icon color when menu is open */
            .mobile-menu-toggle.menu-open i {
                color: #DD2476 !important;
            }
            
            .mobile-menu-toggle.menu-open {
                background: #fff !important;
                border: 2px solid #DD2476 !important;
                color: #DD2476 !important;
            }
        }
        
        /* New Mobile Menu Design with Fade Transition */
        @media (max-width: 991px) {
            /* Hide old mobile menu */
            .mobile-menu {
                display: none !important;
            }
            
            /* New Mobile Menu - Container only, no backdrop here */
            .mobile-menu-new {
                position: fixed;
                top: 0;
                left: 0;
                width: 320px;
                max-width: 85vw;
                height: 100%;
                z-index: 10000;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                pointer-events: none;
            }
            
            .mobile-menu-new.active {
                transform: translateX(0);
                pointer-events: all;
            }
            
            /* Menu Container - Sharp and clickable */
            .mobile-menu-new .mobile-menu-container {
                position: relative;
                width: 100%;
                height: 100%;
                background: #fff;
                box-shadow: 2px 0 20px rgba(0, 0, 0, 0.15);
                overflow-y: auto;
                display: flex;
                flex-direction: column;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                pointer-events: all;
                will-change: transform;
            }
            
            /* Header */
            .mobile-menu-header {
                background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%);
                padding: 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            
            .mobile-menu-header h4 {
                color: #fff;
                font-size: 20px;
                margin: 0;
            }
            
            .mobile-menu-close-btn {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: #fff;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                padding: 0;
            }
            
            .mobile-menu-close-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: rotate(90deg);
            }
            
            .mobile-menu-close-btn i {
                font-size: 18px;
            }
            
            /* Menu Navigation */
            .mobile-menu-nav {
                flex: 1;
                padding: 0;
                overflow-y: auto;
            }
            
            .mobile-menu-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            
            .mobile-menu-item {
                border-bottom: 1px solid #f0f0f0;
            }
            
            .mobile-menu-item:last-child {
                border-bottom: none;
            }
            
            .mobile-menu-link {
                display: flex;
                align-items: center;
                padding: 18px 20px;
                text-decoration: none;
                color: #232323;
                font-size: 16px;
                font-weight: 500;
                transition: all 0.3s ease;
                position: relative;
            }
            
            .mobile-menu-link:hover,
            .mobile-menu-link:active {
                background: linear-gradient(90deg, rgba(78, 101, 255, 0.05) 0%, rgba(146, 239, 253, 0.05) 100%);
                color: #92EFFD;
                text-decoration: none;
            }
            
            .menu-icon-wrapper {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, rgba(78, 101, 255, 0.1) 0%, rgba(146, 239, 253, 0.1) 100%);
                border-radius: 10px;
                margin-right: 15px;
                transition: all 0.3s ease;
            }
            
            .mobile-menu-link:hover .menu-icon-wrapper,
            .mobile-menu-link:active .menu-icon-wrapper {
                background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%);
                transform: scale(1.1);
            }
            
            .menu-icon-wrapper i {
                font-size: 18px;
                color: #DD2476;
                transition: color 0.3s ease;
            }
            
            .mobile-menu-link:hover .menu-icon-wrapper i,
            .mobile-menu-link:active .menu-icon-wrapper i {
                color: #fff;
            }
            
            .menu-text {
                flex: 1;
                font-weight: 500;
            }
            
            .menu-arrow {
                font-size: 14px;
                color: #999;
                transition: all 0.3s ease;
            }
            
            .mobile-menu-link:hover .menu-arrow,
            .mobile-menu-link:active .menu-arrow {
                color: #92EFFD;
                transform: translateX(5px);
            }
            
            /* Submenu arrow rotation */
            .has-submenu.active .menu-arrow {
                transform: rotate(180deg);
                color: #92EFFD;
            }
            
            /* Submenu Styles */
            .mobile-submenu {
                list-style: none;
                padding: 0;
                margin: 0;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
                background: #f8f9fa;
            }
            
            .has-submenu.active .mobile-submenu {
                max-height: 500px;
            }
            
            .mobile-submenu-item {
                border-bottom: 1px solid #e9ecef;
            }
            
            .mobile-submenu-item:last-child {
                border-bottom: none;
            }
            
            .mobile-submenu-link {
                display: block;
                padding: 12px 20px 12px 60px;
                text-decoration: none;
                color: #555;
                font-size: 14px;
                font-weight: 400;
                transition: all 0.2s ease;
                position: relative;
            }
            
            .mobile-submenu-link:before {
                content: "•";
                position: absolute;
                left: 45px;
                color: #DD2476;
                font-size: 16px;
            }
            
            .mobile-submenu-link:hover,
            .mobile-submenu-link:active {
                background: #fff;
                color: #92EFFD;
                padding-left: 65px;
            }
            
            /* Backdrop - Separate element, behind menu, NO BLUR */
            .mobile-menu-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
                pointer-events: none;
                /* NO backdrop-filter - this causes the blurry screen issue */
            }
            
            .mobile-menu-backdrop.active {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
            }
            
            /* Ensure menu is above backdrop */
            .mobile-menu-new {
                z-index: 10000 !important;
            }
            
            /* Ensure menu content is sharp and clickable */
            .mobile-menu-container,
            .mobile-menu-header,
            .mobile-menu-nav,
            .mobile-menu-list,
            .mobile-menu-item,
            .mobile-menu-link {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                text-rendering: optimizeLegibility;
            }
            
            /* Ensure menu links are fully clickable */
            .mobile-menu-link {
                pointer-events: auto !important;
                cursor: pointer;
            }
            
            .mobile-menu-item {
                pointer-events: auto !important;
            }
            
            body.mobile-menu-open {
                overflow: hidden !important;
                /* Ensure no blur effects on body */
                filter: none !important;
                -webkit-filter: none !important;
            }
            
            /* Prevent any blur on page content when menu is open */
            body.mobile-menu-open * {
                filter: none !important;
                -webkit-filter: none !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
            }
            
            /* Exception: allow backdrop to have its own styling */
            body.mobile-menu-open .mobile-menu-backdrop {
                filter: none !important;
                -webkit-filter: none !important;
            }
            
            /* Scrollbar styling */
            .mobile-menu-nav::-webkit-scrollbar {
                width: 6px;
            }
            
            .mobile-menu-nav::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            
            .mobile-menu-nav::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%);
                border-radius: 3px;
            }
            
            .mobile-menu-nav::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, #92EFFD 0%, #4E65FF 100%);
            }
        }
        
        /* Prevent horizontal scroll on mobile for all pages */
        @media (max-width: 991px) {
            html {
                overflow-x: hidden !important;
                max-width: 100vw !important;
            }
            
            body {
                overflow-x: hidden !important;
                max-width: 100vw !important;
                position: relative;
            }
            
            .container,
            .container-fluid {
                max-width: 100% !important;
                overflow-x: hidden !important;
            }
        }
        </style>
                        @php
                            $free_shipping = DB::table('shipping_services')
                                ->whereStatus(1)
                                ->whereIsCondition(1)
                                ->first();
                        @endphp

                    </div>
                </div>
            </div>
        </div>

    </header>
    @endif
    <!-- Page Content-->
    @yield('content')


    <!-- Site Footer-->
    @if (!$shouldHideHeaderFooter)
    <footer class="site-footer" style="background: #232323; color: #fff; padding: 50px 0 20px;">
        <div class="container">
            <div class="row g-4 mb-4">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h5 class="mb-3 fw-bold" style="color: #fff; font-size: 18px;">{{ $setting->title ?? __('Home Find') }}</h5>
                        @if($setting->footer_address)
                        <p class="mb-2" style="color: #ccc; font-size: 14px; line-height: 1.6;">
                            <i class="fas fa-map-marker-alt me-2" style="color: #92EFFD;"></i>{{ $setting->footer_address }}
                        </p>
                        @endif
                        @if($setting->footer_phone)
                        <p class="mb-2" style="color: #ccc; font-size: 14px;">
                            <i class="fas fa-phone me-2" style="color: #92EFFD;"></i>
                            <a href="tel:{{ $setting->footer_phone }}" style="color: #ccc; text-decoration: none;">{{ $setting->footer_phone }}</a>
                        </p>
                        @endif
                        @if($setting->footer_email)
                        <p class="mb-3" style="color: #ccc; font-size: 14px;">
                            <i class="fas fa-envelope me-2" style="color: #92EFFD;"></i>
                            <a href="mailto:{{ $setting->footer_email }}" style="color: #ccc; text-decoration: none;">{{ $setting->footer_email }}</a>
                        </p>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h5 class="mb-3 fw-bold" style="color: #fff; font-size: 18px;">{{ __('Quick Links') }}</h5>
                        <ul class="list-unstyled" style="margin: 0; padding: 0;">
                            @php
                                $quickLinks = $setting->footer_quick_links ? json_decode($setting->footer_quick_links, true) : [];
                            @endphp
                            @if(!empty($quickLinks))
                                @foreach($quickLinks as $link)
                                <li class="mb-2">
                                    @php
                                        // Check if URL is a route name or full URL
                                        $url = $link['url'];
                                        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
                                            $linkUrl = $url;
                                        } elseif (strpos($url, '/') === 0) {
                                            $linkUrl = $url;
                                        } else {
                                            // Try to resolve as route
                                            try {
                                                $linkUrl = route($url);
                                            } catch (\Exception $e) {
                                                $linkUrl = url($url);
                                            }
                                        }
                                    @endphp
                                    <a href="{{ $linkUrl }}" style="color: #ccc; text-decoration: none; font-size: 14px; transition: color 0.3s ease;">
                                        <i class="fas fa-chevron-right me-2" style="font-size: 10px;"></i>{{ $link['label'] }}
                                    </a>
                                </li>
                                @endforeach
                            @else
                                <!-- Default links if none configured -->
                                <li class="mb-2">
                                    <a href="{{ route('front.index') }}" style="color: #ccc; text-decoration: none; font-size: 14px; transition: color 0.3s ease;">
                                        <i class="fas fa-chevron-right me-2" style="font-size: 10px;"></i>{{ __('Home') }}
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="{{ route('front.products') }}" style="color: #ccc; text-decoration: none; font-size: 14px; transition: color 0.3s ease;">
                                        <i class="fas fa-chevron-right me-2" style="font-size: 10px;"></i>{{ __('Products') }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                
                <!-- Contact & Hours -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5 class="mb-3 fw-bold" style="color: #fff; font-size: 18px;">{{ __('Contact') }}</h5>
                        @if($setting->working_days_from_to)
                        <p class="mb-2" style="color: #ccc; font-size: 14px;">
                            <i class="fas fa-clock me-2" style="color: #92EFFD;"></i>
                            <strong>{{ $setting->working_days_from_to }}:</strong><br>
                            <span class="ms-4">{{ $setting->friday_start }} - {{ $setting->friday_end }}</span>
                        </p>
                        @endif
                    </div>
                </div>
                
                <!-- Social Media -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5 class="mb-3 fw-bold" style="color: #fff; font-size: 18px;">{{ __('Follow Us') }}</h5>
                        @php
                            $links = json_decode($setting->social_link, true)['links'] ?? [];
                            $icons = json_decode($setting->social_link, true)['icons'] ?? [];
                        @endphp
                        @if(!empty($links))
                        <div class="footer-social-links d-flex gap-2 flex-wrap">
                            @foreach ($links as $link_key => $link)
                                @if(!empty($link) && isset($icons[$link_key]))
                                <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" 
                                   style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.1); border-radius: 50%; color: #fff; text-decoration: none; transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.2);">
                                    <i class="{{ $icons[$link_key] }}" style="font-size: 18px;"></i>
                                </a>
                                @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="row">
                <div class="col-12">
                    <div class="footer-copyright text-center pt-3" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                        <p class="mb-0" style="color: #999; font-size: 13px;">
                            {{ $setting->copy_right ?? '© ' . date('Y') . ' ' . ($setting->title ?? 'Home Find') . '. ' . __('All rights reserved.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <style>
    .site-footer a:hover {
        color: #92EFFD !important;
    }
    
    /* Default social icon background */
    .footer-social-links a {
        background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%) !important;
        border: none !important;
        color: #fff !important;
    }
    
    /* Remove global black overlay effect from theme */
    .footer-social-links a::before,
    .footer-social-links a:hover::before,
    .footer-social-links a:focus::before,
    .footer-social-links a:active::before {
        background: transparent !important;
        width: 0 !important;
    }
    
    /* Hover / focus / active state */
    .footer-social-links a:hover,
    .footer-social-links a:focus,
    .footer-social-links a:active {
        background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%) !important;
        border-color: #92EFFD !important;
        transform: translateY(-2px);
        color: #fff !important;
    }

        /* Ensure old prices are always shown with line-through */
        .product-card .product-price > del,
        #main_div .product-price del,
        .home-product-old-price del {
            text-decoration: line-through !important;
        }
    
    @media (max-width: 768px) {
        .site-footer {
            padding: 40px 0 20px !important;
        }
        
        .footer-widget {
            margin-bottom: 30px;
        }
    }
    </style>
    @endif

    <!-- Back To Top Button-->
    <a class="scroll-to-top-btn" href="#">
        <i class="icon-chevron-up"></i>
    </a>
    <!-- Backdrop-->
    <div class="site-backdrop"></div>

    


    @php
        $mainbs = [];
        $mainbs['is_announcement'] = $setting->is_announcement;
        $mainbs['announcement_delay'] = $setting->announcement_delay;
        $mainbs['overlay'] = $setting->overlay;
        $mainbs = json_encode($mainbs);
    @endphp

    <script>
        var mainbs = {!! $mainbs !!};
        var decimal_separator = '{!! $setting->decimal_separator !!}';
        var thousand_separator = '{!! $setting->thousand_separator !!}';
        
        // Enhanced dataLayer for user behavior tracking
        window.dataLayer = window.dataLayer || [];
        
        // Session and user behavior data
        window.dataLayer.push({
            'userLoginStatus': '{{ Auth::check() ? "logged_in" : "logged_out" }}',
            'userType': '{{ Auth::check() ? "registered" : "guest" }}',
            'sessionId': '{{ session()->getId() }}',
            'pageLoadTime': new Date().getTime(),
            'userAgent': navigator.userAgent,
            'screenResolution': screen.width + 'x' + screen.height,
            'language': navigator.language,
            'referrer': document.referrer || 'direct',
            'event': 'page_view'
        });
    </script>

    <script>
        let language = {
            Days: '{{ __('Days') }}',
            Hrs: '{{ __('Hrs') }}',
            Min: '{{ __('Min') }}',
            Sec: '{{ __('Sec') }}',
        }
    </script>



    <!-- JavaScript (jQuery) libraries, plugins and custom scripts-->
    <script type="text/javascript" src="{{ asset('assets/front/js/plugins.min.js') }}"></script>
    <!-- Ensure Bootstrap is properly loaded for modals -->
    <script type="text/javascript" src="{{ asset('assets/back/js/core/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/back/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('assets/front/js/scripts.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/lazy.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/lazy.plugin.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/myscript.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/extraindex.js') }}"></script>
    @yield('script')

    @if ($setting->is_facebook_messenger == '1')
        <!-- Messenger Chat Plugin Code -->
        <div id="fb-root"></div>

        <!-- Your Chat Plugin code -->
        <div id="fb-customer-chat" class="fb-customerchat">
        </div>

        <script>
            var chatbox = document.getElementById('fb-customer-chat');
            chatbox.setAttribute("page_id", "{{ $setting->facebook_messenger }}");
            chatbox.setAttribute("attribution", "biz_inbox");
            window.fbAsyncInit = function() {
                FB.init({
                    xfbml: true,
                    version: 'v11.0'
                });
            };

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
    @endif



    <script type="text/javascript">
        let mainurl = '{{ route('front.index') }}';

        // Search autocomplete functionality
        $(document).ready(function() {
            let searchTimeout;
            
            // Function to handle search for both desktop and mobile
            function handleSearch(inputId, suggestionsId) {
                $(document).on('keyup', inputId, function () {
                    let search = $(this).val();
                    
                    clearTimeout(searchTimeout);
                    
                    if (search.length >= 2) {
                        searchTimeout = setTimeout(function() {
                            let url = $(inputId).attr('data-target');
                            $.get(url + '?search=' + encodeURIComponent(search), function (response) {
                                $(suggestionsId).removeClass('d-none').html(response);
                            }).fail(function() {
                                $(suggestionsId).addClass('d-none');
                            });
                        }, 300);
                    } else {
                        $(suggestionsId).addClass('d-none');
                    }
                });
            }
            
            // Initialize for desktop search
            handleSearch('#__product__search', '#search_suggestions');
            
            // Initialize for mobile search
            handleSearch('#__product__search_mobile', '#search_suggestions_mobile');

            // Hide suggestions when clicking outside (desktop)
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#__product__search, #search_suggestions').length) {
                    $('#search_suggestions').addClass('d-none');
                }
                // Hide suggestions when clicking outside (mobile)
                if (!$(e.target).closest('#__product__search_mobile, #search_suggestions_mobile').length) {
                    $('#search_suggestions_mobile').addClass('d-none');
                }
            });

            // Handle view all search link (desktop and mobile)
            $(document).on('click', '#view_all_search_', function () {
                // Check if clicked from mobile or desktop search
                if ($('#__product__search_mobile').is(':visible')) {
                    $('#header_search_form_mobile').submit();
                } else {
                    $('#header_search_form').submit();
                }
            });
            
            // New Mobile Menu Toggle with Fade Transition
            // Backdrop is now in HTML, no need to create dynamically
            
            // Function to open menu
            function openMobileMenu() {
                // Close old menu if it's open
                $('.mobile-menu').removeClass('open');
                // Open new menu
                $('.mobile-menu-new').addClass('active');
                $('.mobile-menu-backdrop').addClass('active');
                $('body').addClass('mobile-menu-open');
                $('.mobile-menu-toggle').addClass('menu-open');
            }
            
            // Function to close menu
            function closeMobileMenu() {
                $('.mobile-menu-new').removeClass('active');
                $('.mobile-menu-backdrop').removeClass('active');
                $('body').removeClass('mobile-menu-open');
                $('.mobile-menu-toggle').removeClass('menu-open');
            }
            
            // Handle submenu toggle - DO NOT close main menu
            $(document).on('click', '.mobile-menu-link[data-toggle="submenu"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                var parentItem = $(this).closest('.mobile-menu-item');
                
                // Toggle active class
                parentItem.toggleClass('active');
                
                // Close other submenus
                $('.mobile-menu-item.has-submenu').not(parentItem).removeClass('active');
                
                // DO NOT close the main menu
                return false;
            });
            
            // Handle submenu item clicks - CLOSE the main menu
            $(document).on('click', '.mobile-submenu-link', function(e) {
                e.stopPropagation();
                // Allow navigation and close menu
                closeMobileMenu();
            });
            
            // Handle menu toggle button click (Menu button in header) - override old handler
            $(document).off('click', '.mobile-menu-toggle').on('click', '.mobile-menu-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // Check if this is the close button
                if ($(this).hasClass('mobile-menu-close-btn')) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }
                return false;
            });
            
            // Handle close button click (separate handler for safety)
            $(document).on('click', '.mobile-menu-close-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                closeMobileMenu();
                return false;
            });
            
            // Handle backdrop click to close menu
            $(document).on('click', '.mobile-menu-backdrop', function() {
                closeMobileMenu();
            });
            
            // Prevent menu close when clicking inside menu container - CRITICAL
            $(document).on('click', '.mobile-menu-container', function(e) {
                e.stopPropagation();
                e.stopImmediatePropagation();
                return true;
            });
            
            // Handle clicks on backdrop area (outside menu container) to close
            $(document).on('click', '.mobile-menu-backdrop.active', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeMobileMenu();
            });
            
            // Handle menu link clicks - allow navigation, then close menu
            $(document).on('click', '.mobile-menu-link', function(e) {
                // Skip if this is a submenu toggle (Categories)
                if ($(this).attr('data-toggle') === 'submenu') {
                    return; // Let the submenu handler deal with it
                }
                
                // Stop propagation to prevent backdrop click
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                const href = $(this).attr('href');
                
                if (!href || href === '#' || href === 'javascript:void(0);') {
                    e.preventDefault();
                    return false;
                }
                
                // If it's an anchor link (contains #), handle scroll
                if (href.indexOf('#') !== -1) {
                    const parts = href.split('#');
                    const baseUrl = parts[0] || window.location.pathname;
                    const anchor = parts[1];
                    
                    // If it's a full URL with anchor, navigate first
                    if (baseUrl && baseUrl !== window.location.pathname && baseUrl !== '') {
                        // It's a different page with anchor - navigate normally
                        closeMobileMenu();
                        setTimeout(function() {
                            window.location.href = href;
                        }, 200);
                        return false;
                    }
                    
                    // Same page anchor link - scroll to section
                    e.preventDefault();
                    const targetElement = anchor ? $('#' + anchor) : null;
                    
                    if (targetElement && targetElement.length) {
                        closeMobileMenu();
                        // Small delay to allow menu to close, then scroll
                        setTimeout(function() {
                            $('html, body').animate({
                                scrollTop: targetElement.offset().top - 80
                            }, 500);
                        }, 300);
                    } else {
                        // If element not found, just close menu
                        closeMobileMenu();
                    }
                    return false;
                } else {
                    // For regular links, navigate normally
                    closeMobileMenu();
                    // Allow default navigation to proceed
                    setTimeout(function() {
                        window.location.href = href;
                    }, 200);
                    return false;
                }
            });
            
            // Prevent any clicks inside menu from bubbling
            $(document).on('click', '.mobile-menu-header, .mobile-menu-nav, .mobile-menu-list, .mobile-menu-item', function(e) {
                e.stopPropagation();
            });
        });

        let view_extra_index = 0;
        // Notifications
        function SuccessNotification(title) {
            $.notify({
                title: ` <strong>${title}</strong>`,
                message: '',
                icon: 'fas fa-check-circle'
            }, {
                element: 'body',
                position: null,
                type: "success",
                allow_dismiss: true,
                newest_on_top: false,
                showProgressbar: false,
                placement: {
                    from: "top",
                    align: "right"
                },
                offset: 20,
                spacing: 10,
                z_index: 1031,
                delay: 5000,
                timer: 1000,
                url_target: '_blank',
                mouse_over: null,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                },
                onShow: null,
                onShown: null,
                onClose: null,
                onClosed: null,
                icon_type: 'class'
            });
        }

        function DangerNotification(title) {
            $.notify({
                // options
                title: ` <strong>${title}</strong>`,
                message: '',
                icon: 'fas fa-exclamation-triangle'
            }, {
                // settings
                element: 'body',
                position: null,
                type: "danger",
                allow_dismiss: true,
                newest_on_top: false,
                showProgressbar: false,
                placement: {
                    from: "top",
                    align: "right"
                },
                offset: 20,
                spacing: 10,
                z_index: 1031,
                delay: 5000,
                timer: 1000,
                url_target: '_blank',
                mouse_over: null,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                },
                onShow: null,
                onShown: null,
                onClose: null,
                onClosed: null,
                icon_type: 'class'
            });
        }
        // Notifications Ends
    </script>

    @if (Session::has('error'))
        <script>
            $(document).ready(function() {
                DangerNotification('{{ Session::get('error') }}')
            })
        </script>
    @endif
    @if (Session::has('success'))
        <script>
            $(document).ready(function() {
                SuccessNotification('{{ Session::get('success') }}');
            })
        </script>
    @endif

    {{-- Purchase Notification Popup --}}
    @if(isset($purchaseNotifications) && $purchaseNotifications && $purchaseNotifications->count() > 0)
    <div id="purchase-notification-container" style="position: fixed; bottom: 20px; left: 20px; z-index: 9999; max-width: 350px;"></div>

    <style>
        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOutLeft {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(-100%);
                opacity: 0;
            }
        }
        .purchase-notification-popup {
            transition: all 0.3s ease;
        }
    </style>

    <script>
        var purchaseNotifications = {!! isset($purchaseNotificationsJson) ? $purchaseNotificationsJson : '[]' !!};
        var currentNotificationIndex = 0;
        var popupInterval = {{ $purchasePopupInterval ?? 2000 }};
        var breakInterval = {{ $purchasePopupBreakInterval ?? 2000 }};
        var notificationTimer = null;
        var breakTimer = null;
        var isPaused = false;

        function calculateTimeAgo(createdAtTimestamp, minutesAgo) {
            // Calculate actual time difference from when notification was created
            var now = Math.floor(Date.now() / 1000); // Current timestamp in seconds
            var created = createdAtTimestamp; // Notification creation timestamp
            var actualMinutesAgo = Math.floor((now - created) / 60);
            
            // Reset after 24 hours (1440 minutes) - use original minutes_ago value
            if (actualMinutesAgo >= 1440) {
                actualMinutesAgo = minutesAgo;
            } else {
                // Use the actual calculated time, but ensure it's at least the configured minutes_ago
                actualMinutesAgo = Math.max(actualMinutesAgo, minutesAgo);
            }
            
            // Convert to hours if >= 60 minutes
            if (actualMinutesAgo >= 60) {
                var hours = Math.floor(actualMinutesAgo / 60);
                var remainingMinutes = actualMinutesAgo % 60;
                
                if (remainingMinutes === 0) {
                    // Exact hours
                    if (hours === 1) {
                        return '{{ __('1 hour ago') }}';
                    } else {
                        return hours + ' {{ __('hours ago') }}';
                    }
                } else {
                    // Hours and minutes
                    if (hours === 1) {
                        return '{{ __('1 hour') }} ' + remainingMinutes + ' {{ __('min ago') }}';
                    } else {
                        return hours + ' {{ __('hours') }} ' + remainingMinutes + ' {{ __('min ago') }}';
                    }
                }
            } else {
                // Less than 60 minutes - show in minutes
                if (actualMinutesAgo < 1) {
                    return '{{ __('Just now') }}';
                } else if (actualMinutesAgo == 1) {
                    return '{{ __('1 min ago') }}';
                } else {
                    return actualMinutesAgo + ' {{ __('min ago') }}';
                }
            }
        }

        function createNotificationPopup(notification) {
            var container = document.getElementById('purchase-notification-container');
            
            // Clear any existing popup first
            container.innerHTML = '';
            
            var popupId = 'purchase-popup-' + Date.now();
            
            // Calculate time dynamically
            var timeText = calculateTimeAgo(notification.created_at, notification.minutes_ago);
            
            var popupHTML = '<div id="' + popupId + '" class="purchase-notification-popup" style="background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border-radius: 12px; padding: 16px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3); color: #fff; animation: slideInLeft 0.5s ease; position: relative;">' +
                '<button type="button" class="btn-close-notification" style="position: absolute; top: 8px; right: 8px; background: none; border: none; color: #fff; font-size: 16px; cursor: pointer; opacity: 0.7; padding: 4px 8px; line-height: 1;" onclick="closePurchaseNotification(\'' + popupId + '\')">' +
                '<i class="fas fa-times"></i>' +
                '</button>' +
                '<div class="purchase-notification-content" style="padding-right: 30px;">' +
                '<div class="notification-line-1" style="font-size: 15px; line-height: 1.6; margin-bottom: 6px;">' +
                '<strong style="font-weight: 600;">' + notification.customer_name + '</strong> {{ __('Purchase') }}' +
                '</div>' +
                '<div class="notification-line-2" style="font-size: 14px; line-height: 1.6; margin-bottom: 6px; color: #6dd5ed; font-weight: 600; cursor: pointer;" onclick="goToProduct(\'' + notification.product_slug + '\')">' +
                notification.product_name +
                '</div>' +
                '<div class="notification-line-3" style="font-size: 13px; line-height: 1.6; opacity: 0.9;" data-created="' + notification.created_at + '" data-minutes="' + notification.minutes_ago + '">' +
                timeText +
                '</div>' +
                '</div>' +
                '</div>';
            
            container.innerHTML = popupHTML;
            
            // Update time every minute for active popup
            var timeUpdateInterval = setInterval(function() {
                var timeEl = document.querySelector('#' + popupId + ' .notification-line-3');
                if (timeEl) {
                    var created = parseInt(timeEl.getAttribute('data-created'));
                    var minutes = parseInt(timeEl.getAttribute('data-minutes'));
                    var newTimeText = calculateTimeAgo(created, minutes);
                    timeEl.textContent = newTimeText;
                } else {
                    clearInterval(timeUpdateInterval);
                }
            }, 60000); // Update every minute
            
            // Auto remove after interval
            notificationTimer = setTimeout(function() {
                clearInterval(timeUpdateInterval);
                removeNotificationPopup(popupId);
            }, popupInterval);
        }

        function removeNotificationPopup(popupId) {
            var popup = document.getElementById(popupId);
            if (popup) {
                popup.style.animation = 'slideOutLeft 0.5s ease forwards';
                setTimeout(function() {
                    if (popup && popup.parentNode) {
                        popup.parentNode.removeChild(popup);
                    }
                    // Wait for break interval before showing next notification
                    if (!isPaused && purchaseNotifications.length > 0) {
                        startBreakPeriod();
                    }
                }, 500);
            } else {
                // If popup already removed, start break period
                if (!isPaused && purchaseNotifications.length > 0) {
                    startBreakPeriod();
                }
            }
        }

        function startBreakPeriod() {
            // Clear container during break
            var container = document.getElementById('purchase-notification-container');
            if (container) {
                container.innerHTML = '';
            }
            
            // Wait for break interval, then show next notification
            breakTimer = setTimeout(function() {
                if (!isPaused && purchaseNotifications.length > 0) {
                    showNextPurchaseNotification();
                }
            }, breakInterval);
        }

        function showNextPurchaseNotification() {
            if (purchaseNotifications.length === 0 || isPaused) return;
            
            var notification = purchaseNotifications[currentNotificationIndex];
            createNotificationPopup(notification);
            
            // Move to next notification
            currentNotificationIndex = (currentNotificationIndex + 1) % purchaseNotifications.length;
        }

        function closePurchaseNotification(popupId) {
            if (notificationTimer) {
                clearTimeout(notificationTimer);
            }
            if (breakTimer) {
                clearTimeout(breakTimer);
            }
            removeNotificationPopup(popupId);
        }

        function goToProduct(slug) {
            if (slug) {
                window.location.href = '{{ url("/") }}/product/' + slug;
            }
        }

        // Pause on hover
        document.addEventListener('DOMContentLoaded', function() {
            var container = document.getElementById('purchase-notification-container');
            if (container) {
                container.addEventListener('mouseenter', function() {
                    isPaused = true;
                    if (notificationTimer) {
                        clearTimeout(notificationTimer);
                    }
                    if (breakTimer) {
                        clearTimeout(breakTimer);
                    }
                });
                container.addEventListener('mouseleave', function() {
                    isPaused = false;
                    // Resume - if no popup is showing, start break period or show next
                    var container = document.getElementById('purchase-notification-container');
                    if (container && container.innerHTML.trim() === '') {
                        // No popup showing, start break period which will show next
                        if (purchaseNotifications.length > 0) {
                            startBreakPeriod();
                        }
                    }
                });
            }
            
            // Start showing notifications after page load
            if (purchaseNotifications.length > 0) {
                setTimeout(function() {
                    showNextPurchaseNotification();
                }, 1000); // Start after 1 second (first notification shows immediately)
            }
        });
    </script>
    @endif
    
    <!-- Sticky Header Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var desktopHeaderOriginal = document.getElementById('main-header-original');
            var mobileHeaderOriginal = document.getElementById('mobile-header-original');
            var scrollThreshold = 150;
            var stickyDesktopHeader = null;
            var stickyMobileHeader = null;
            
            // Create sticky clone for desktop
            function createStickyDesktop() {
                if (!stickyDesktopHeader && desktopHeaderOriginal) {
                    stickyDesktopHeader = desktopHeaderOriginal.cloneNode(true);
                    stickyDesktopHeader.id = 'main-header-sticky';
                    stickyDesktopHeader.classList.add('main-header-sticky');
                    stickyDesktopHeader.classList.remove('main-header-area');
                    document.body.appendChild(stickyDesktopHeader);
                }
            }
            
            // Create sticky clone for mobile
            function createStickyMobile() {
                if (!stickyMobileHeader && mobileHeaderOriginal) {
                    stickyMobileHeader = mobileHeaderOriginal.cloneNode(true);
                    stickyMobileHeader.id = 'mobile-header-sticky';
                    stickyMobileHeader.classList.add('mobile-header-sticky');
                    stickyMobileHeader.classList.remove('mobile-header-layout');
                    
                    // Hide the search bar section in sticky mobile header
                    var searchBarContainer = stickyMobileHeader.querySelector('.pb-2');
                    if (searchBarContainer) {
                        searchBarContainer.style.display = 'none';
                    }
                    
                    document.body.appendChild(stickyMobileHeader);
                    
                    // Re-attach mobile menu toggle event to sticky header
                    var stickyMenuBtn = stickyMobileHeader.querySelector('.mobile-menu-toggle');
                    if (stickyMenuBtn) {
                        stickyMenuBtn.addEventListener('click', function() {
                            openMobileMenu();
                        });
                    }
                }
            }
            
            // Handle scroll
            window.addEventListener('scroll', function() {
                var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Desktop sticky header
                if (window.innerWidth >= 992) {
                    if (!stickyDesktopHeader) createStickyDesktop();
                    
                    if (scrollTop > scrollThreshold) {
                        if (stickyDesktopHeader) {
                            stickyDesktopHeader.classList.add('show');
                        }
                    } else {
                        if (stickyDesktopHeader) {
                            stickyDesktopHeader.classList.remove('show');
                        }
                    }
                }
                
                // Mobile sticky header
                if (window.innerWidth < 992) {
                    if (!stickyMobileHeader) createStickyMobile();
                    
                    if (scrollTop > scrollThreshold) {
                        if (stickyMobileHeader) {
                            stickyMobileHeader.classList.add('show');
                        }
                    } else {
                        if (stickyMobileHeader) {
                            stickyMobileHeader.classList.remove('show');
                        }
                    }
                }
            });
            
            // Initial creation on page load
            if (window.innerWidth >= 992) {
                createStickyDesktop();
            } else {
                createStickyMobile();
            }
        });
    </script>

</body>

</html>

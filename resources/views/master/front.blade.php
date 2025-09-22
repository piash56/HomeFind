@php
    // Define pages where header and footer should be hidden
    $hideHeaderFooter = [
        'front.index',             // Root page (shop page)
        'front.product',           // Single product page
        'front.checkout.billing',  // Checkout page
        'front.checkout.success',  // Order success page
        'front.order.track',       // Track order page
    ];
    
    $currentRoute = Route::currentRouteName();
    $shouldHideHeaderFooter = in_array($currentRoute, $hideHeaderFooter);
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

    <!-- SEO Meta Tags-->
    @yield('meta')
    <meta name="author" content="GeniusDevs">
    <meta name="distribution" content="web">
    <meta name="description" content="{{ $setting->meta_description }}">
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="image" content="{{ asset('storage/images/' . $setting->meta_image) }}">
    <meta property="og:title" content="{{ $setting->title}}">
    <meta property="og:description" content="{{ $setting->meta_description }}">
    <meta property="og:image" content="{{ asset('storage/images/' . $setting->meta_image) }}">
    <meta property="og:image:secure_url" content="{{ asset('storage/images/' . $setting->meta_image) }}" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="627" />
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $setting->title }}">
    <meta property="og:type" content="website">
    

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
    @if ($setting->is_facebook_pixel == '1')
        {!! $setting->facebook_pixel !!}
    @endif
    {{-- Facebook pixel End --}}

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
        <div class="menu-top-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="t-m-s-a">
                            <a class="track-order-link" href="{{ route('front.order.track') }}"><i
                                    class="icon-map-pin"></i>{{ __('Track Order') }}</a>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="right-area">

                            <a class="track-order-link wishlist-mobile d-inline-block d-lg-none"
                                href="{{ route('user.wishlist.index') }}"><i
                                    class="icon-heart"></i>{{ __('Wishlist') }}</a>

                            {{-- <div class="t-h-dropdown ">
                                <a class="main-link" href="#">{{ __('Language') }}<i
                                        class="icon-chevron-down"></i></a>
                                <div class="t-h-dropdown-menu">
                                    @foreach (DB::table('languages')->whereType('Website')->get() as $language)
                                        <a class="{{ Session::get('language') == $language->id ? 'active' : ($language->is_default == 1 && !Session::has('language') ? 'active' : '') }}"
                                            href="{{ route('front.language.setup', $language->id) }}"><i
                                                class="icon-chevron-right pr-2"></i>{{ $language->language }}</a>
                                    @endforeach
                                </div>
                            </div> --}}


                            <div class="login-register ">
                                @if (!Auth::user())
                                    <a class="track-order-link mr-0" href="{{ route('user.login') }}">
                                        {{ __('Login') }}
                                    </a>
                                @else
                                    <div class="t-h-dropdown">
                                        <div class="main-link">
                                            <i class="icon-user pr-2"></i> <span
                                                class="text-label">{{ Auth::user()->first_name }}</span>
                                        </div>
                                        <div class="t-h-dropdown-menu">
                                            <a href="{{ route('user.dashboard') }}"><i
                                                    class="icon-chevron-right pr-2"></i>{{ __('Dashboard') }}</a>
                                            <a href="{{ route('user.logout') }}"><i
                                                    class="icon-chevron-right pr-2"></i>{{ __('Logout') }}</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Topbar-->
        <div class="topbar">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between">
                            <!-- Logo-->
                            <div class="site-branding"><a class="site-logo align-self-center"
                                    href="{{ route('front.index') }}"><img
                                        src="{{ asset('storage/images/' . $setting->logo) }}"
                                        alt="{{ $setting->title }}"></a></div>
                            <!-- Search / Categories-->
                            <div class="search-box-wrap d-none d-lg-block d-flex">
                                <div class="search-box-inner align-self-center">
                                    <div class="search-box d-flex">
                                        <select name="category" id="category_select" class="categoris">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach (DB::table('categories')->whereStatus(1)->get() as $category)
                                                <option value="{{ $category->slug }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <form class="input-group" id="header_search_form"
                                            action="{{ route('front.catalog') }}" method="get">
                                            <input type="hidden" name="category" value=""
                                                id="search__category">
                                            <span class="input-group-btn">
                                                <button type="submit"><i class="icon-search"></i></button>
                                            </span>
                                            <input class="form-control" type="text"
                                                data-target="{{ route('front.search.suggest') }}"
                                                id="__product__search" name="search"
                                                placeholder="{{ __('Search by product name') }}">
                                            <div class="serch-result d-none">
                                                {{-- search result --}}
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <span class="d-block d-lg-none close-m-serch"><i class="icon-x"></i></span>
                            </div>
                            <!-- Toolbar-->
                            <div class="toolbar d-flex">

                                <div class="toolbar-item close-m-serch visible-on-mobile"><a href="#">
                                        <div>
                                            <i class="icon-search"></i>
                                        </div>
                                    </a>
                                </div>
                                <div class="toolbar-item visible-on-mobile mobile-menu-toggle"><a href="#">
                                        <div><i class="icon-menu"></i><span
                                                class="text-label">{{ __('Menu') }}</span></div>
                                    </a>
                                </div>

                                @if (Auth::check())
                                    <div class="toolbar-item hidden-on-mobile"><a
                                            href="{{ route('user.wishlist.index') }}">
                                            <div><span class="compare-icon"><i class="icon-heart"></i><span
                                                        class="count-label wishlist_count">{{ Auth::user()->wishlists->count() }}</span></span><span
                                                    class="text-label">{{ __('Wishlist') }}</span></div>
                                        </a>
                                    </div>
                                @else
                                    <div class="toolbar-item hidden-on-mobile"><a
                                            href="{{ route('user.wishlist.index') }}">
                                            <div><span class="compare-icon"><i class="icon-heart"></i></span><span
                                                    class="text-label">{{ __('Wishlist') }}</span></div>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Mobile Menu-->
                            <div class="mobile-menu">
                                <!-- Slideable (Mobile) Menu-->
                                <div class="mm-heading-area">
                                    <h4>{{ __('Navigation') }}</h4>
                                    <div class="toolbar-item visible-on-mobile mobile-menu-toggle mm-t-two">
                                        <a href="#">
                                            <div> <i class="icon-x"></i></div>
                                        </a>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item" role="presentation99">
                                        <span class="active" id="mmenu-tab" data-bs-toggle="tab"
                                            data-bs-target="#mmenu" role="tab" aria-controls="mmenu"
                                            aria-selected="true">{{ __('Menu') }}</span>
                                    </li>
                                    <li class="nav-item" role="presentation99">
                                        <span class="" id="mcat-tab" data-bs-toggle="tab"
                                            data-bs-target="#mcat" role="tab" aria-controls="mcat"
                                            aria-selected="false">{{ __('Category') }}</span>
                                    </li>

                                </ul>
                                <div class="tab-content p-0">
                                    <div class="tab-pane fade show active" id="mmenu" role="tabpanel"
                                        aria-labelledby="mmenu-tab">
                                        <nav class="slideable-menu">
                                            <ul>
                                                <li class="{{ request()->routeIs('front.index') ? 'active' : '' }}"><a
                                                        href="{{ route('front.index') }}"><i
                                                            class="icon-chevron-right"></i>{{ __('Home') }}</a>
                                                </li>
                                                @if ($setting->is_shop == 1)
                                                    <li
                                                        class="{{ request()->routeIs('front.catalog*') ? 'active' : '' }}">
                                                        <a href="{{ route('front.catalog') }}"><i
                                                                class="icon-chevron-right"></i>{{ __('Shop') }}</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </nav>
                                    </div>
                                    <div class="tab-pane fade" id="mcat" role="tabpanel"
                                        aria-labelledby="mcat-tab">
                                        <nav class="slideable-menu">
                                            @include('includes.mobile-category')

                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Navbar-->
        <div class="navbar">
            <div class="container">
                <div class="row g-3 w-100">
                    @if ($setting->is_show_category == 1)
                        <div class="col-lg-3">
                            @include('includes.categories')
                        </div>
                    @endif
                    <div class="col-lg-9 d-flex justify-content-between">
                        <div class="nav-inner">
                            @include('master.inc.site-menu')
                        </div>
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
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <!-- Contact Info-->
                    <section class="widget widget-light-skin">
                        <h3 class="widget-title">{{ __('Get In Touch') }}</h3>
                        <p class="mb-1"><strong>{{ __('Address') }}: </strong> {{ $setting->footer_address }}</p>
                        <p class="mb-1"><strong>{{ __('Phone') }}: </strong> {{ $setting->footer_phone }}</p>
                        <p class="mb-1"><strong>{{ __('Email') }}: </strong> {{ $setting->footer_email }}</p>
                        <ul class="list-unstyled text-sm">
                            <li><span class=""><strong>{{ $setting->working_days_from_to }}:
                                    </strong></span>{{ $setting->friday_start }} - {{ $setting->friday_end }}</li>
                        </ul>
                        @php
                            $links = json_decode($setting->social_link, true)['links'];
                            $icons = json_decode($setting->social_link, true)['icons'];

                        @endphp
                        <div class="footer-social-links">
                            @foreach ($links as $link_key => $link)
                                <a href="{{ $link }}"><span><i
                                            class="{{ $icons[$link_key] }}"></i></span></a>
                            @endforeach
                        </div>
                    </section>
                </div>
            </div>
            <!-- Copyright-->
            <p class="footer-copyright"> {{ $setting->copy_right }}</p>
        </div>
    </footer>
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
    <script type="text/javascript" src="{{ asset('assets/back/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('assets/front/js/scripts.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/lazy.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/lazy.plugin.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/myscript.js') }}"></script>
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

</body>

</html>

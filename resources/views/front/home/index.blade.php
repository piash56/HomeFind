@extends('master.front')
@section('meta')
<meta name="keywords" content="{{$setting->meta_keywords}}">
<meta name="description" content="{{$setting->meta_description}}">
@endsection
@section('hometitle')
    {{$setting->home_page_title ? $setting->home_page_title : $setting->title}}
@endsection

@section('content')
<!-- Hero Section with Featured Product Slider -->
<section class="hero-section py-5 mb-5" style="background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border-radius: 0 0 50px 50px;">
    <div class="container">
        <div class="row align-items-center">
            {{-- Left: Hero text ~60% (7/12) --}}
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="hero-content text-white">
                    @php
                        // Get all hero settings with defaults
                        $heroBadgeText = $setting->getHeroSetting('badge_text', __('Premium Quality Products at Best Prices'));
                        $heroBadgeIcon = $setting->getHeroSetting('badge_icon', 'fas fa-bolt');
                        $heroHeadline1 = $setting->getHeroSetting('headline_line1', __('Shop Smart,'));
                        $heroHeadline2 = $setting->getHeroSetting('headline_line2', __('Save More'));
                        $heroDesc = $setting->getHeroSetting('description', __('Discover premium products with unbeatable deals. Quality you can trust, prices you\'ll love.'));
                        $button1Text = $setting->getHeroSetting('button1_text', __('Explore Hot Deals'));
                        $button1Link = $setting->getHeroSetting('button1_link', route('front.products'));
                        $button1Icon = $setting->getHeroSetting('button1_icon', 'fas fa-fire');
                        $button2Text = $setting->getHeroSetting('button2_text', __('Shop Now'));
                        $button2Link = $setting->getHeroSetting('button2_link', route('front.products'));
                        $button2Icon = $setting->getHeroSetting('button2_icon', 'fas fa-shopping-bag');
                        $stat1Number = $setting->getHeroSetting('stat1_number');
                        $stat1Label = $setting->getHeroSetting('stat1_label', __('Products'));
                        $stat2Number = $setting->getHeroSetting('stat2_number');
                        $stat2Label = $setting->getHeroSetting('stat2_label', __('Orders'));
                        $stat3Number = $setting->getHeroSetting('stat3_number');
                        $stat3Label = $setting->getHeroSetting('stat3_label', __('Happy Customers'));
                    @endphp

                    <!-- Introductory Badge -->
                    <div class="mb-3">
                        <span class="badge px-3 py-2 d-inline-flex align-items-center" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 25px; font-size: 13px; font-weight: 600;">
                            <i class="{{ $heroBadgeIcon }} me-2" style="background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"></i>
                            <span>{{ $heroBadgeText }}</span>
                        </span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="display-4 fw-bold mb-3" style="line-height: 1.2;">
                        <span class="d-block" style="color: #fff;">{{ $heroHeadline1 }}</span>
                        <span class="d-block" style="background-color: #DD2476; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{ $heroHeadline2 }}</span>
                    </h1>

                    <!-- Descriptive Text -->
                    <p class="lead mb-4" style="font-size: 18px; line-height: 1.6; opacity: 0.95; max-width: 90%;">
                        {{ Str::limit($heroDesc, 120) }}
                    </p>

                    <!-- Call-to-Action Buttons -->
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="{{ $button1Link }}" class="btn-lg px-4 py-3 fw-bold d-inline-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border-radius: 10px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3); transition: all 0.3s ease;">
                            <i class="{{ $button1Icon }} me-2"></i>{{ $button1Text }} <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        <a href="{{ $button2Link }}" class="btn-lg px-4 py-3 fw-bold d-inline-flex align-items-center justify-content-center" style="background: transparent; color: #fff; border: 2px solid #fff; border-radius: 10px; transition: all 0.3s ease;">
                            <i class="{{ $button2Icon }} me-2"></i>{{ $button2Text }}
                        </a>
                    </div>

                    <!-- Statistics/Metrics -->
                    <div class="hero-stats-row d-flex flex-wrap gap-4 mt-4 pt-3" style="border-top: 1px solid rgba(255, 255, 255, 0.2);">
                        <div class="stat-item">
                            <div class="fw-bold" style="font-size: 28px; line-height: 1; color: #4E65FF;">{{ $stat1Number ? $stat1Number : $totalProducts }}{{ $stat1Number ? '' : '+' }}</div>
                            <div class="small" style="font-size: 13px; opacity: 0.9;">{{ $stat1Label }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="fw-bold" style="font-size: 28px; line-height: 1; color: #4E65FF;">{{ $stat2Number ? $stat2Number : $totalOrders }}{{ $stat2Number ? '' : '+' }}</div>
                            <div class="small" style="font-size: 13px; opacity: 0.9;">{{ $stat2Label }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="fw-bold" style="font-size: 28px; line-height: 1; color: #4E65FF;">{{ $stat3Number ? $stat3Number : $totalCustomers }}{{ $stat3Number ? '' : '+' }}</div>
                            <div class="small" style="font-size: 13px; opacity: 0.9;">{{ $stat3Label }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Middle spacer ~10% (1/12) for breathing space between text and slider --}}
            <div class="d-none d-lg-block col-lg-1"></div>

            {{-- Right: Featured product slider ~30% (4/12) --}}
            <div class="col-lg-4">
                @php
                    $featuredCount = $featuredProducts ? $featuredProducts->count() : 0;
                @endphp
                @if($featuredCount > 0)
                <div class="featured-product-slider" id="featured-product-slider" style="display: block; visibility: visible; opacity: 1;">
                    <div class="featured-slider-viewport">
                    @foreach($featuredProducts as $item)
                    <div class="featured-product-card bg-white rounded-4 shadow-lg position-relative overflow-hidden" style="min-height: 450px; border: 1px solid rgba(102, 126, 234, 0.1);">
                        <!-- Badges Container -->
                        <div class="position-absolute top-0 start-0 w-100 p-3" style="z-index: 10;">
                            <div class="d-flex justify-content-between align-items-start">
                                <!-- Hot Deal Badge -->
                                <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(78, 101, 255, 0.3);">
                                    <i class="fas fa-fire me-1"></i>{{__('Stock Clearance')}}
                                </span>
                                
                                <!-- Discount Badge -->
                                @if($item->previous_price && $item->previous_price != 0)
                                <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #ed213a 0%, #93291e 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(237, 33, 58, 0.3);">
                                    -{{PriceHelper::DiscountPercentage($item)}}
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Product Image Section - Professional Display with Gallery -->
                        <div class="product-image-wrapper position-relative mb-3" style="height: 220px; border-radius: 12px 12px 0 0; overflow: hidden; margin: -1px -1px 0 -1px; display: flex; gap: 8px; padding: 8px;">
                            <!-- Left Side: Featured/Thumbnail Image (50%) -->
                            <div class="featured-image-left" style="flex: 0 0 calc(50% - 4px); height: 204px; border-radius: 8px; overflow: hidden;">
                                <a href="{{route('front.product', $item->slug)}}" class="d-block h-100 w-100">
                                    <img src="{{asset('storage/images/'.$item->thumbnail)}}" 
                                         alt="{{$item->name}}" 
                                         class="featured-product-image" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </a>
                            </div>
                            
                            <!-- Right Side: First Gallery Image (50%) -->
                            @php
                                $firstGallery = $item->galleries->first();
                            @endphp
                            <div class="gallery-image-right" style="flex: 0 0 calc(50% - 4px); height: 204px; border-radius: 8px; overflow: hidden;">
                                @if($firstGallery)
                                    <a href="{{route('front.product', $item->slug)}}" class="d-block h-100 w-100">
                                        <img src="{{asset('storage/images/'.$firstGallery->photo)}}" 
                                             alt="{{$item->name}}" 
                                             class="gallery-product-image" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    </a>
                                @else
                                    <!-- Fallback: Show thumbnail if no gallery image -->
                                    <a href="{{route('front.product', $item->slug)}}" class="d-block h-100 w-100">
                                        <img src="{{asset('storage/images/'.$item->thumbnail)}}" 
                                             alt="{{$item->name}}" 
                                             class="gallery-product-image" 
                                             style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7;">
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Product Content -->
                        <div class="px-4 pb-4">
                            <!-- Product Name -->
                            <h4 class="product-name mb-2" style="font-size: 15px; font-weight: 600;">
                                <a href="{{route('front.product', $item->slug)}}" class="text-dark text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 22px;">
                                    {{ Str::limit($item->name, 60) }}
                                </a>
                            </h4>
                            
                            <!-- Rating Stars -->
                            <div class="rating-stars mb-2" style="font-size: 13px;">
                                {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                            </div>
                            
                            <!-- Prices -->
                            <div class="home-product-price-row mb-3">
                                @if ($item->previous_price != 0)
                                <div class="home-product-old-price">
                                    <del class="text-muted" style="font-size: 16px;">{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                                </div>
                                @endif
                                <div class="home-product-main-price">
                                    <span class="fw-bold" style="font-size: 26px; background-color: #4E65FF;linear-gradient(135deg, #DD2476 0%, #FF512F 100%) -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{PriceHelper::grandCurrencyPrice($item)}}</span>
                                </div>
                            </div>
                            
                            <!-- Stock Progress Bar -->
                            @if($item->stock && $item->stock > 0)
                            @php
                                $stockPercentage = min(100, ($item->stock / 100) * 100);
                                $soldCount = max(0, 100 - $item->stock);
                            @endphp
                            <div class="stock-progress mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted" style="font-size: 14px;">{{__('Sold')}}: {{$soldCount}}</small>
                                    <small class="text-muted" style="font-size: 14px;">{{__('Available')}}: {{$item->stock}}</small>
                                </div>
                                <div class="progress" style="height: 18px; border-radius: 10px; background: #e9ecef;">
                                    <div class="progress-bar" role="progressbar" style="width: {{$stockPercentage}}%; border-radius: 10px; background: #4E65FF ;" aria-valuenow="{{$stockPercentage}}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Order Now Button -->
                            <div class="mt-3">
                                @if($item->is_stock())
                                <a href="{{route('front.product', $item->slug)}}" class="order-now-btn-home w-100 fw-bold text-white d-flex align-items-center justify-content-center mb-2" style=" border-radius: 10px; background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: none; padding: 10px 20px; font-size: 18px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3); transition: all 0.3s ease; text-decoration: none;">
                                    <i class="fas fa-shopping-cart me-2"></i>{{__('Order Now')}}
                                </a>
                                <a href="javascript:;" class="add_to_single_cart w-100 fw-bold d-flex align-items-center justify-content-center" data-target="{{ $item->id }}" style="border-radius: 10px; border: 2px solid #4E65FF; color: #4E65FF; padding: 10px 20px; font-size: 15px; transition: all 0.3s ease; text-decoration: none;">
                                    <i class="fas fa-cart-plus me-2"></i>{{__('Add to Cart')}}
                                </a>
                                @else
                                <button class="btn w-100 rounded-pill text-white" disabled style="background: #6c757d; border: none; padding: 11px 20px; font-size: 14px;">
                                    {{__('Out of Stock')}}
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>
                </div>
                @else
                <div class="bg-white rounded-4 p-5 text-center" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                    <div>
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">{{__('No featured products available')}}</p>
                        <small class="text-muted">Please mark products as "Featured" in the admin panel</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Best Selling Products Section -->
<section id="best-selling-section" class="best-selling-section py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="section-title fw-bold mb-2" style="font-size: 2.5rem; color: #232323;">{{__('Best Selling Products')}}</h2>
                <p class="text-muted">{{__('Handpicked products loved by our customers')}}</p>
                <div class="title-divider mx-auto mb-4" style="width: 80px; height: 4px; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border-radius: 2px;"></div>
            </div>
        </div>
        
        @if(isset($bestSellingProducts) && $bestSellingProducts && $bestSellingProducts->count() > 0)
        @php
            $productCount = $bestSellingProducts->count();
            $showTwoRows = $productCount > 10;
            $firstRowProducts = $showTwoRows ? $bestSellingProducts->take(6) : $bestSellingProducts;
            $secondRowProducts = $showTwoRows ? $bestSellingProducts->skip(6)->take(6) : collect();
        @endphp
        
        @if($showTwoRows)
            <!-- First Row -->
            <div class="best-selling-slider owl-carousel mb-4" id="best-selling-products-slider-row-1">
                @foreach($firstRowProducts as $item)
            <div class="item">
                <div class="product-card bg-white rounded-4 shadow-lg position-relative overflow-hidden h-100" style="border: 1px solid rgba(102, 126, 234, 0.1); min-height: 450px;">
                    <!-- Badges Container -->
                    <div class="position-absolute top-0 start-0 w-100 p-3" style="z-index: 10;">
                        <div class="d-flex justify-content-between align-items-start">
                            <!-- Best Seller Badge -->
                            @if ($item->is_stock())
                            <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(78, 101, 255, 0.3);">
                                <i class="fas fa-fire me-1"></i>{{__('Best Seller')}}
                            </span>
                            @else
                            <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: #6c757d; border: none; border-radius: 20px;">
                                {{__('Out of Stock')}}
                            </span>
                            @endif
                            
                            <!-- Discount Badge -->
                            @if($item->previous_price && $item->previous_price != 0)
                            <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #ed213a 0%, #93291e 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(237, 33, 58, 0.3);">
                                -{{PriceHelper::DiscountPercentage($item)}}
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Product Image Section - Single Featured Image Only -->
                    <div class="product-image-wrapper position-relative mb-3" style="height: 220px; border-radius: 12px 12px 0 0; overflow: hidden; margin: -1px -1px 0 -1px; padding: 8px;">
                        <a href="{{route('front.product', $item->slug)}}" class="d-block h-100 w-100">
                            <img src="{{asset('storage/images/'.$item->thumbnail)}}" 
                                 alt="{{$item->name}}" 
                                 class="featured-product-image" 
                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                        </a>
                    </div>
                    
                    <!-- Product Content -->
                    <div class="px-4 pb-4" style="flex: 1; display: flex; flex-direction: column;">
                        <!-- Product Name -->
                        <h4 class="product-name mb-2" style="font-size: 20px; font-weight: 600;">
                            <a href="{{route('front.product', $item->slug)}}" class="text-dark text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 20px;">
                                {{ Str::limit($item->name, 60) }}
                            </a>
                        </h4>
                        
                        <!-- Rating Stars -->
                        <div class="rating-stars mb-2" style="font-size: 13px;">
                            {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                        </div>
                        
                        <!-- Prices -->
                        <div class="home-product-price-row">
                            @if ($item->previous_price != 0)
                            <div class="home-product-old-price">
                                <del class="text-muted" style="font-size: 16px;">{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                            </div>
                            @endif
                            <div class="home-product-main-price">
                                <span class="fw-bold" style="font-size: 26px; background-color: #4E65FF;linear-gradient(135deg, #DD2476 0%, #FF512F 100%) -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{PriceHelper::grandCurrencyPrice($item)}}</span>
                            </div>
                        </div>
                        
                        <!-- Order Now Button -->
                        <div class="" style="margin-top: 10px;">
                            @if($item->is_stock())
                            <a href="{{route('front.product', $item->slug)}}" class="order-now-btn-home w-100 fw-bold text-white d-flex align-items-center justify-content-center mb-2" style="border-radius: 10px; background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: none; padding: 10px 20px; font-size: 18px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3); transition: all 0.3s ease; text-decoration: none;">
                                <i class="fas fa-shopping-cart me-2"></i>{{__('Order Now')}}
                            </a>
                            <a href="javascript:;" class="add_to_single_cart w-100 fw-bold d-flex align-items-center justify-content-center" data-target="{{ $item->id }}" style="border-radius: 10px; border: 2px solid #4E65FF; color: #4E65FF; padding: 10px 20px; font-size: 15px; transition: all 0.3s ease; text-decoration: none;">
                                <i class="fas fa-cart-plus me-2"></i>{{__('Add to Cart')}}
                            </a>
                            @else
                            <button class="btn w-100 rounded-pill text-white" disabled style="background: #6c757d; border: none; padding: 11px 20px; font-size: 14px;">
                                {{__('Out of Stock')}}
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
                @endforeach
            </div>
            
            @if($secondRowProducts->count() > 0)
                <!-- Second Row -->
                <div class="best-selling-slider owl-carousel" id="best-selling-products-slider-row-2">
                    @foreach($secondRowProducts as $item)
                        <div class="item">
                            <div class="product-card bg-white rounded-4 shadow-lg position-relative overflow-hidden h-100" style="border: 1px solid rgba(102, 126, 234, 0.1); min-height: 450px;">
                                <!-- Badges Container -->
                                <div class="position-absolute top-0 start-0 w-100 p-3" style="z-index: 10;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <!-- Best Seller Badge -->
                                        @if ($item->is_stock())
                                        <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(78, 101, 255, 0.3);">
                                            <i class="fas fa-fire me-1"></i>{{__('Best Seller')}}
                                        </span>
                                        @else
                                        <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: #6c757d; border: none; border-radius: 20px;">
                                            {{__('Out of Stock')}}
                                        </span>
                                        @endif
                                        
                                        <!-- Discount Badge -->
                                        @if($item->previous_price && $item->previous_price != 0)
                                        <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #ed213a 0%, #93291e 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(237, 33, 58, 0.3);">
                                            -{{PriceHelper::DiscountPercentage($item)}}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Product Image Section -->
                                <div class="product-image-wrapper position-relative" style="height: 220px; border-radius: 12px 12px 0 0; overflow: hidden; margin: -1px -1px 0 -1px; padding: 8px;">
                                    <a href="{{route('front.product', $item->slug)}}" class="d-block h-100 w-100">
                                        <img src="{{asset('storage/images/'.$item->thumbnail)}}" 
                                             alt="{{$item->name}}" 
                                             class="featured-product-image" 
                                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                    </a>
                                </div>
                                
                                <!-- Product Content -->
                                <div class="px-4 pb-4" style="flex: 1; display: flex; flex-direction: column;">
                                    <!-- Product Name -->
                                    <h4 class="product-name mb-2" style="font-size: 20px; font-weight: 600;">
                                        <a href="{{route('front.product', $item->slug)}}" class="text-dark text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 20px;">
                                            {{ Str::limit($item->name, 60) }}
                                        </a>
                                    </h4>
                                    
                                    <!-- Rating Stars -->
                                    <div class="rating-stars mb-2" style="font-size: 13px;">
                                        {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                    </div>
                                    
                                    <!-- Prices -->
                                    <div class="home-product-price-row">
                                        @if ($item->previous_price != 0)
                                        <div class="home-product-old-price">
                                            <del class="text-muted" style="font-size: 16px;">{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                                        </div>
                                        @endif
                                        <div class="home-product-main-price">
                                            <span class="fw-bold" style="font-size: 26px; background-color: #4E65FF;linear-gradient(135deg, #DD2476 0%, #FF512F 100%) -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{PriceHelper::grandCurrencyPrice($item)}}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Order Now Button -->
                                    <div class="" style="margin-top: 10px;">
                                        @if($item->is_stock())
                                        <a href="{{route('front.product', $item->slug)}}" class="order-now-btn-home w-100 fw-bold text-white d-flex align-items-center justify-content-center mb-2" style="border-radius: 10px; background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: none; padding: 10px 20px; font-size: 18px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3); transition: all 0.3s ease; text-decoration: none;">
                                            <i class="fas fa-shopping-cart me-2"></i>{{__('Order Now')}}
                                        </a>
                                        <a href="javascript:;" class="add_to_single_cart w-100 fw-bold d-flex align-items-center justify-content-center" data-target="{{ $item->id }}" style="border-radius: 10px; border: 2px solid #4E65FF; color: #4E65FF; padding: 10px 20px; font-size: 15px; transition: all 0.3s ease; text-decoration: none;">
                                            <i class="fas fa-cart-plus me-2"></i>{{__('Add to Cart')}}
                                        </a>
                                        @else
                                        <button class="btn w-100 rounded-pill text-white" disabled style="background: #6c757d; border: none; padding: 11px 20px; font-size: 14px;">
                                            {{__('Out of Stock')}}
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <!-- Single Row (10 or fewer products) -->
            <div class="best-selling-slider owl-carousel" id="best-selling-products-slider">
                @foreach($bestSellingProducts as $item)
                    <div class="item">
                        <div class="product-card bg-white rounded-4 shadow-lg position-relative overflow-hidden h-100" style="border: 1px solid rgba(102, 126, 234, 0.1); min-height: 450px;">
                            <!-- Badges Container -->
                            <div class="position-absolute top-0 start-0 w-100 p-3" style="z-index: 10;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <!-- Best Seller Badge -->
                                    @if ($item->is_stock())
                                    <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(78, 101, 255, 0.3);">
                                        <i class="fas fa-fire me-1"></i>{{__('Best Seller')}}
                                    </span>
                                    @else
                                    <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: #6c757d; border: none; border-radius: 20px;">
                                        {{__('Out of Stock')}}
                                    </span>
                                    @endif
                                    
                                    <!-- Discount Badge -->
                                    @if($item->previous_price && $item->previous_price != 0)
                                    <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #ed213a 0%, #93291e 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(237, 33, 58, 0.3);">
                                        -{{PriceHelper::DiscountPercentage($item)}}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Product Image Section -->
                            <div class="product-image-wrapper position-relative" style="height: 220px; border-radius: 12px 12px 0 0; overflow: hidden; margin: -1px -1px 0 -1px; padding: 8px;">
                                <a href="{{route('front.product', $item->slug)}}" class="d-block h-100 w-100">
                                    <img src="{{asset('storage/images/'.$item->thumbnail)}}" 
                                         alt="{{$item->name}}" 
                                         class="featured-product-image" 
                                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                </a>
                            </div>
                            
                            <!-- Product Content -->
                            <div class="px-4 pb-4" style="flex: 1; display: flex; flex-direction: column;">
                                <!-- Product Name -->
                                <h4 class="product-name mb-2" style="font-size: 20px; font-weight: 600;">
                                    <a href="{{route('front.product', $item->slug)}}" class="text-dark text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 20px;">
                                        {{ Str::limit($item->name, 60) }}
                                    </a>
                                </h4>
                                
                                <!-- Rating Stars -->
                                <div class="rating-stars mb-2" style="font-size: 13px;">
                                    {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                </div>
                                
                                <!-- Prices -->
                                <div class="home-product-price-row">
                                    @if ($item->previous_price != 0)
                                    <div class="home-product-old-price">
                                        <del class="text-muted" style="font-size: 16px;">{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                                    </div>
                                    @endif
                                    <div class="home-product-main-price">
                                        <span class="fw-bold" style="font-size: 26px; background-color: #4E65FF;linear-gradient(135deg, #DD2476 0%, #FF512F 100%) -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{PriceHelper::grandCurrencyPrice($item)}}</span>
                                    </div>
                                </div>
                                
                                <!-- Order Now Button -->
                                <div class="" style="margin-top: 10px;">
                                    @if($item->is_stock())
                                    <a href="{{route('front.product', $item->slug)}}" class="order-now-btn-home w-100 fw-bold text-white d-flex align-items-center justify-content-center mb-2" style="border-radius: 10px; background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: none; padding: 10px 20px; font-size: 18px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3); transition: all 0.3s ease; text-decoration: none;">
                                        <i class="fas fa-shopping-cart me-2"></i>{{__('Order Now')}}
                                    </a>
                                    <a href="javascript:;" class="add_to_single_cart w-100 fw-bold d-flex align-items-center justify-content-center" data-target="{{ $item->id }}" style="border-radius: 10px; border: 2px solid #4E65FF; color: #4E65FF; padding: 10px 20px; font-size: 15px; transition: all 0.3s ease; text-decoration: none;">
                                        <i class="fas fa-cart-plus me-2"></i>{{__('Add to Cart')}}
                                    </a>
                                    @else
                                    <button class="btn w-100 rounded-pill text-white" disabled style="background: #6c757d; border: none; padding: 11px 20px; font-size: 14px;">
                                        {{__('Out of Stock')}}
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        <div class="text-center mt-5">
            <a href="{{route('front.products')}}" class="btn-lg px-4 py-3 fw-bold d-inline-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border-radius: 10px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3); transition: all 0.3s ease; text-decoration: none;">
                <i class="fas fa-shopping-bag me-2"></i>{{__('View All Products')}} <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        @else
        <div class="text-center py-5">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>{{__('No best selling products available at the moment.')}}
            </div>
            <a href="{{route('front.products')}}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill mt-3">
                {{__('Browse All Products')}}
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Featured Products Grid Section (Admin Selected) -->
<section id="featured-grid-section" class="featured-grid-section py-5" style="background: #f9fafb;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="section-title fw-bold mb-2" style="font-size: 2.5rem; color: #232323;">{{ __('Featured Products') }}</h2>
                <p class="text-muted">{{ __('Special products selected by our store') }}</p>
                <div class="title-divider mx-auto mb-4" style="width: 80px; height: 4px; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border-radius: 2px;"></div>
            </div>
        </div>

        @if(isset($featuredGridProducts) && $featuredGridProducts && $featuredGridProducts->count() > 0)
        @php
            $featuredCount = $featuredGridProducts->count();
            $featuredShowTwoRows = $featuredCount > 10;
            $featuredFirstRowProducts = $featuredShowTwoRows ? $featuredGridProducts->take(6) : $featuredGridProducts;
            $featuredSecondRowProducts = $featuredShowTwoRows ? $featuredGridProducts->skip(6)->take(6) : collect();
        @endphp
        
        @if($featuredShowTwoRows)
            <!-- First Row -->
            <div class="featured-products-slider owl-carousel mb-4" id="featured-products-slider-row-1">
                @foreach($featuredFirstRowProducts as $item)
                    <div class="item">
                        <div class="product-card bg-white rounded-4 shadow-lg position-relative overflow-hidden h-100" style="border: 1px solid rgba(102, 126, 234, 0.1); min-height: 450px;">
                            <!-- Badges Container -->
                            <div class="position-absolute top-0 start-0 w-100 p-3" style="z-index: 10;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <!-- Featured Badge -->
                                    @if ($item->is_stock())
                                        <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(78, 101, 255, 0.3);">
                                            <i class="fas fa-star me-1"></i>{{ __('Featured') }}
                                        </span>
                                    @else
                                        <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: #6c757d; border: none; border-radius: 20px;">
                                            {{ __('Out of Stock') }}
                                        </span>
                                    @endif

                                    <!-- Discount Badge -->
                                    @if($item->previous_price && $item->previous_price != 0)
                                        <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #ed213a 0%, #93291e 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(237, 33, 58, 0.3);">
                                            -{{ PriceHelper::DiscountPercentage($item) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Product Image -->
                            <div class="product-image-wrapper position-relative mb-3" style="height: 220px; border-radius: 12px 12px 0 0; overflow: hidden; margin: -1px -1px 0 -1px; padding: 8px;">
                                <a href="{{ route('front.product', $item->slug) }}" class="d-block h-100 w-100">
                                    <img src="{{ asset('storage/images/'.$item->thumbnail) }}"
                                         alt="{{ $item->name }}"
                                         class="featured-product-image"
                                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                </a>
                            </div>

                            <!-- Product Content -->
                            <div class="px-4 pb-4" style="flex: 1; display: flex; flex-direction: column;">
                                <!-- Product Name -->
                                <h4 class="product-name mb-2" style="font-size: 20px; font-weight: 600;">
                                    <a href="{{ route('front.product', $item->slug) }}" class="text-dark text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 20px;">
                                        {{ Str::limit($item->name, 60) }}
                                    </a>
                                </h4>

                                <!-- Rating Stars -->
                                <div class="rating-stars mb-2" style="font-size: 13px;">
                                    {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                </div>

                                <!-- Prices -->
                                <div class="home-product-price-row">
                                    @if ($item->previous_price != 0)
                                        <div class="home-product-old-price">
                                            <del class="text-muted" style="font-size: 16px;">{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del>
                                        </div>
                                    @endif
                                    <div class="home-product-main-price">
                                        <span class="fw-bold" style="font-size: 26px; background-color: #4E65FF;linear-gradient(135deg, #DD2476 0%, #FF512F 100%) -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                            {{ PriceHelper::grandCurrencyPrice($item) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Order Now Button -->
                                <div class="" style="margin-top: 10px;">
                                    @if($item->is_stock())
                                        <a href="{{ route('front.product', $item->slug) }}" class="order-now-btn-home w-100 fw-bold text-white d-flex align-items-center justify-content-center mb-2" style="border-radius: 10px; background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: none; padding: 10px 20px; font-size: 18px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3); transition: all 0.3s ease; text-decoration: none;">
                                            <i class="fas fa-shopping-cart me-2"></i>{{ __('Order Now') }}
                                        </a>
                                        <a href="javascript:;" class="add_to_single_cart w-100 fw-bold d-flex align-items-center justify-content-center" data-target="{{ $item->id }}" style="border-radius: 10px; border: 2px solid #4E65FF; color: #4E65FF; padding: 10px 20px; font-size: 15px; transition: all 0.3s ease; text-decoration: none;">
                                            <i class="fas fa-cart-plus me-2"></i>{{ __('Add to Cart') }}
                                        </a>
                                    @else
                                        <button class="btn w-100 rounded-pill text-white" disabled style="background: #6c757d; border: none; padding: 11px 20px; font-size: 14px;">
                                            {{ __('Out of Stock') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($featuredSecondRowProducts->count() > 0)
                <!-- Second Row -->
                <div class="featured-products-slider owl-carousel" id="featured-products-slider-row-2">
                    @foreach($featuredSecondRowProducts as $item)
                        <div class="item">
                            <div class="product-card bg-white rounded-4 shadow-lg position-relative overflow-hidden h-100" style="border: 1px solid rgba(102, 126, 234, 0.1); min-height: 450px;">
                                <!-- Badges Container -->
                                <div class="position-absolute top-0 start-0 w-100 p-3" style="z-index: 10;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <!-- Featured Badge -->
                                        @if ($item->is_stock())
                                            <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(78, 101, 255, 0.3);">
                                                <i class="fas fa-star me-1"></i>{{ __('Featured') }}
                                            </span>
                                        @else
                                            <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: #6c757d; border: none; border-radius: 20px;">
                                                {{ __('Out of Stock') }}
                                            </span>
                                        @endif

                                        <!-- Discount Badge -->
                                        @if($item->previous_price && $item->previous_price != 0)
                                            <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #ed213a 0%, #93291e 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(237, 33, 58, 0.3);">
                                                -{{ PriceHelper::DiscountPercentage($item) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Product Image -->
                                <div class="product-image-wrapper position-relative mb-3" style="height: 220px; border-radius: 12px 12px 0 0; overflow: hidden; margin: -1px -1px 0 -1px; padding: 8px;">
                                    <a href="{{ route('front.product', $item->slug) }}" class="d-block h-100 w-100">
                                        <img src="{{ asset('storage/images/'.$item->thumbnail) }}"
                                             alt="{{ $item->name }}"
                                             class="featured-product-image"
                                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                    </a>
                                </div>

                                <!-- Product Content -->
                                <div class="px-4 pb-4" style="flex: 1; display: flex; flex-direction: column;">
                                    <!-- Product Name -->
                                    <h4 class="product-name mb-2" style="font-size: 20px; font-weight: 600;">
                                        <a href="{{ route('front.product', $item->slug) }}" class="text-dark text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 20px;">
                                            {{ Str::limit($item->name, 60) }}
                                        </a>
                                    </h4>

                                    <!-- Rating Stars -->
                                    <div class="rating-stars mb-2" style="font-size: 13px;">
                                        {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                    </div>

                                    <!-- Prices -->
                                    <div class="home-product-price-row">
                                        @if ($item->previous_price != 0)
                                            <div class="home-product-old-price">
                                                <del class="text-muted" style="font-size: 16px;">{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del>
                                            </div>
                                        @endif
                                        <div class="home-product-main-price">
                                            <span class="fw-bold" style="font-size: 26px; background-color: #4E65FF;linear-gradient(135deg, #DD2476 0%, #FF512F 100%) -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                                {{ PriceHelper::grandCurrencyPrice($item) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Order Now Button -->
                                    <div class="" style="margin-top: 10px;">
                                        @if($item->is_stock())
                                            <a href="{{ route('front.product', $item->slug) }}" class="order-now-btn-home w-100 fw-bold text-white d-flex align-items-center justify-content-center mb-2" style="border-radius: 10px; background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: none; padding: 10px 20px; font-size: 18px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3); transition: all 0.3s ease; text-decoration: none;">
                                                <i class="fas fa-shopping-cart me-2"></i>{{ __('Order Now') }}
                                            </a>
                                            <a href="javascript:;" class="add_to_single_cart w-100 fw-bold d-flex align-items-center justify-content-center" data-target="{{ $item->id }}" style="border-radius: 10px; border: 2px solid #4E65FF; color: #4E65FF; padding: 10px 20px; font-size: 15px; transition: all 0.3s ease; text-decoration: none;">
                                                <i class="fas fa-cart-plus me-2"></i>{{ __('Add to Cart') }}
                                            </a>
                                        @else
                                            <button class="btn w-100 rounded-pill text-white" disabled style="background: #6c757d; border: none; padding: 11px 20px; font-size: 14px;">
                                                {{ __('Out of Stock') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <!-- Single Row (10 or fewer products) -->
            <div class="featured-products-slider owl-carousel">
                @foreach($featuredGridProducts as $item)
                    <div class="item">
                        <div class="product-card bg-white rounded-4 shadow-lg position-relative overflow-hidden h-100" style="border: 1px solid rgba(102, 126, 234, 0.1); min-height: 450px;">
                            <!-- Badges Container -->
                            <div class="position-absolute top-0 start-0 w-100 p-3" style="z-index: 10;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <!-- Featured Badge -->
                                    @if ($item->is_stock())
                                        <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(78, 101, 255, 0.3);">
                                            <i class="fas fa-star me-1"></i>{{ __('Featured') }}
                                        </span>
                                    @else
                                        <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: #6c757d; border: none; border-radius: 20px;">
                                            {{ __('Out of Stock') }}
                                        </span>
                                    @endif

                                    <!-- Discount Badge -->
                                    @if($item->previous_price && $item->previous_price != 0)
                                        <span class="badge px-3 py-2 text-white" style="font-size: 11px; font-weight: 600; background: linear-gradient(135deg, #ed213a 0%, #93291e 100%); border: none; border-radius: 20px; box-shadow: 0 2px 8px rgba(237, 33, 58, 0.3);">
                                            -{{ PriceHelper::DiscountPercentage($item) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Product Image -->
                            <div class="product-image-wrapper position-relative mb-3" style="height: 220px; border-radius: 12px 12px 0 0; overflow: hidden; margin: -1px -1px 0 -1px; padding: 8px;">
                                <a href="{{ route('front.product', $item->slug) }}" class="d-block h-100 w-100">
                                    <img src="{{ asset('storage/images/'.$item->thumbnail) }}"
                                         alt="{{ $item->name }}"
                                         class="featured-product-image"
                                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                </a>
                            </div>

                            <!-- Product Content -->
                            <div class="px-4 pb-4" style="flex: 1; display: flex; flex-direction: column;">
                                <!-- Product Name -->
                                <h4 class="product-name mb-2" style="font-size: 20px; font-weight: 600;">
                                    <a href="{{ route('front.product', $item->slug) }}" class="text-dark text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 20px;">
                                        {{ Str::limit($item->name, 60) }}
                                    </a>
                                </h4>

                                <!-- Rating Stars -->
                                <div class="rating-stars mb-2" style="font-size: 13px;">
                                    {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                </div>

                                <!-- Prices -->
                                <div class="home-product-price-row">
                                    @if ($item->previous_price != 0)
                                        <div class="home-product-old-price">
                                            <del class="text-muted" style="font-size: 16px;">{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del>
                                        </div>
                                    @endif
                                    <div class="home-product-main-price">
                                        <span class="fw-bold" style="font-size: 26px; background-color: #4E65FF;linear-gradient(135deg, #DD2476 0%, #FF512F 100%) -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                            {{ PriceHelper::grandCurrencyPrice($item) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Order Now Button -->
                                <div class="" style="margin-top: 10px;">
                                    @if($item->is_stock())
                                        <a href="{{ route('front.product', $item->slug) }}" class="order-now-btn-home w-100 fw-bold text-white d-flex align-items-center justify-content-center mb-2" style="border-radius: 10px; background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: none; padding: 10px 20px; font-size: 18px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3); transition: all 0.3s ease; text-decoration: none;">
                                            <i class="fas fa-shopping-cart me-2"></i>{{ __('Order Now') }}
                                        </a>
                                        <a href="javascript:;" class="add_to_single_cart w-100 fw-bold d-flex align-items-center justify-content-center" data-target="{{ $item->id }}" style="border-radius: 10px; border: 2px solid #4E65FF; color: #4E65FF; padding: 10px 20px; font-size: 15px; transition: all 0.3s ease; text-decoration: none;">
                                            <i class="fas fa-cart-plus me-2"></i>{{ __('Add to Cart') }}
                                        </a>
                                    @else
                                        <button class="btn w-100 rounded-pill text-white" disabled style="background: #6c757d; border: none; padding: 11px 20px; font-size: 14px;">
                                            {{ __('Out of Stock') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        @else
            <div class="text-center py-5">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>{{ __('No featured products available right now.') }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Reviews Section -->
<section id="reviews-section" class="reviews-section py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="section-title fw-bold mb-2" style="font-size: 2.5rem; color: #232323;">{{__('What Our Customers Say')}}</h2>
                <p class="text-muted">{{__('Latest reviews from satisfied customers')}}</p>
                <div class="title-divider mx-auto mb-4" style="width: 80px; height: 4px; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border-radius: 2px;"></div>
            </div>
        </div>
        
        @if($latestReviews->count() > 0)
        <div class="row g-4">
            <!-- Left Column: Latest Customer Reviews List -->
            <div class="col-lg-7">
                <div class="review-list-card bg-white rounded-4 shadow-sm" style="border: 1px solid #e9ecef; overflow: hidden;">
                    <!-- Header -->
                    <div class="review-list-header p-3 text-white" style="background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-comments me-2" style="font-size: 20px;"></i>
                                <h5 class="mb-0 fw-bold" style="color: #ffffff;">{{__('Latest Customer Reviews')}}</h5>
                            </div>
                            <a href="{{route('front.products')}}" class="btn-sm text-white fw-bold" style="background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: none; border-radius: 20px; padding: 6px 16px; box-shadow: 0 2px 8px rgba(255, 81, 47, 0.3); transition: all 0.3s ease;">
                                {{__('View All')}}
                            </a>
                        </div>
                    </div>
                    
                    <!-- Scrollable Review List -->
                    <div class="review-list-scrollable p-3" style="max-height: 600px; overflow-y: auto;">
                        @foreach($latestReviews as $review)
                        <div class="review-item mb-3 pb-3" style="border-bottom: 1px solid #f0f0f0;">
                            <div class="d-flex align-items-start">
                                <!-- Avatar -->
                                <div class="avatar-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); color: white; font-weight: 600; font-size: 16px;">
                                    {{ strtoupper(substr($review->customer_name, 0, 1)) }}
                                </div>
                                
                                <!-- Review Content -->
                                <div class="flex-grow-1">
                                    <!-- Name and Rating -->
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-0 fw-bold" style="font-size: 15px; color: #232323;">{{$review->customer_name}}</h6>
                                            <div class="rating-stars mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="fas fa-star" style="color: #ffc107; font-size: 12px;"></i>
                                                    @else
                                                        <i class="far fa-star" style="color: #ddd; font-size: 12px;"></i>
                                                    @endif
                                                @endfor
                                                <span class="ms-1" style="font-size: 13px; color: #666;">{{number_format($review->rating, 1)}}</span>
                                                @if($review->order_id)
                                                <span class="badge bg-success ms-2" style="font-size: 10px; padding: 2px 6px;">{{__('Verified')}}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-flex align-items-center justify-content-end" style="font-size: 12px;">
                                                <i class="far fa-clock me-1"></i>
                                                @php
                                                    $daysAgo = $review->created_at->diffInDays(now());
                                                    if ($daysAgo == 0) {
                                                        echo __('Today');
                                                    } elseif ($daysAgo == 1) {
                                                        echo __('1d ago');
                                                    } else {
                                                        echo $daysAgo . 'd ago';
                                                    }
                                                @endphp
                                            </small>
                                            
                                            <!-- Review Photos Below Time Ago -->
                                            @if($review->getReviewImages())
                                            <div class="review-images mt-2 d-flex gap-1 flex-wrap justify-content-end">
                                                @foreach(array_slice($review->getReviewImages(), 0, 3) as $image)
                                                <a href="{{asset($image)}}" target="_blank" class="review-image-link">
                                                    <img src="{{asset($image)}}" alt="Review Image" class="rounded" style="width: 40px; height: 40px; object-fit: cover; border: 1px solid #e0e0e0;">
                                                </a>
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Review Text -->
                                    @if($review->review_text)
                                    <p class="review-text mb-2" style="color: #555; line-height: 1.5; font-size: 14px;">
                                        "{{ Str::limit($review->review_text, 200) }}"
                                    </p>
                                    @endif
                                    
                                    <!-- Product Link -->
                                    @if($review->item)
                                    <div class="product-link">
                                        <a href="{{route('front.product', $review->item->slug)}}" class="text-decoration-none small" style="color: #666;">
                                            <i class="fas fa-box me-1"></i>{{ Str::limit($review->item->name, 50) }}
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Scroll Indicator -->
                        @if($latestReviews->count() >= 5)
                        <div class="text-center mt-3 pt-2">
                            <small class="text-muted d-flex align-items-center justify-content-center" style="font-size: 12px;">
                                <i class="fas fa-chevron-down me-2"></i>{{__('Scroll to see more reviews')}}
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Overall Rating Summary -->
            <div class="col-lg-5">
                <div class="rating-summary-card bg-white rounded-4 p-4 shadow-sm h-100" style="border: 1px solid #e9ecef; background: linear-gradient(135deg, #fffef7 0%, #ffffff 100%);">
                    <!-- Overall Score -->
                    <div class="text-center mb-4">
                        <div class="overall-score mb-2" style="font-size: 3.5rem; font-weight: 700; color: #232323;">
                            {{$averageRating}}<span style="font-size: 2rem; color: #666;">/5</span>
                        </div>
                        <div class="rating-stars mb-2">
                            @php
                                $fullStars = floor($averageRating);
                                $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $fullStars)
                                    <i class="fas fa-star" style="color: #ffc107; font-size: 24px;"></i>
                                @elseif($i == $fullStars + 1 && $hasHalfStar)
                                    <i class="fas fa-star-half-alt" style="color: #ffc107; font-size: 24px;"></i>
                                @else
                                    <i class="far fa-star" style="color: #ddd; font-size: 24px;"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-muted mb-0" style="font-size: 14px;">{{__('From')}} {{$totalReviews}} {{__('reviews')}}</p>
                    </div>
                    
                    <!-- Star Distribution -->
                    <div class="star-distribution mb-4">
                        @for($stars = 5; $stars >= 1; $stars--)
                        <div class="star-row mb-2">
                            <div class="d-flex align-items-center">
                                <div class="star-label me-2" style="width: 40px; font-size: 13px; color: #666;">
                                    {{$stars}} {{__('stars')}}
                                </div>
                                <div class="progress flex-grow-1" style="height: 8px; background: #f0f0f0; border-radius: 4px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{$starDistribution[$stars]['percentage']}}%; background: #ffc107; border-radius: 4px;" aria-valuenow="{{$starDistribution[$stars]['percentage']}}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="star-percentage ms-2" style="width: 45px; text-align: right; font-size: 12px; color: #666;">
                                    {{$starDistribution[$stars]['percentage']}}%
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                    
                    <!-- View All Reviews Button -->
                    <div class="text-center">
                        <a href="{{route('front.products')}}" class="w-100 fw-bold text-white d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: none; padding: 12px 20px; font-size: 16px; border-radius: 10px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.3); transition: all 0.3s ease; text-decoration: none;">
                            <i class="fas fa-star me-2"></i>{{__('View All Reviews')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>{{__('No reviews available at the moment.')}}
            </div>
        </div>
        @endif
    </div>
</section>

<style>
.hero-section {
    margin-top: -20px;
}

/* Hero Section Left Content Styling */
.hero-content .stat-item {
    transition: transform 0.3s ease;
}

.hero-content .stat-item:hover {
    transform: translateY(-3px);
}

.hero-content .btn {
    transition: all 0.3s ease;
}

.hero-content .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4) !important;
}

@media (max-width: 768px) {
    /* Keep all 3 hero stat items on one line on mobile */
    .hero-content .hero-stats-row {
        flex-wrap: nowrap !important;
        gap: 8px !important;
    }
    
    .hero-content .hero-stats-row .stat-item {
        flex: 1 1 0 !important;
        min-width: 0 !important;
        text-align: center;
    }
    
    .hero-content .hero-stats-row .stat-item .fw-bold {
        font-size: 18px !important;
    }
    
    .hero-content .hero-stats-row .stat-item .small {
        font-size: 11px !important;
    }
    
    .hero-content .d-flex.gap-3 {
        flex-direction: column;
    }
    
    .hero-content .btn {
        width: 100%;
    }
}

/* Simple custom slider for featured products */
.featured-product-slider {
    position: relative;
    width: 100%;
    min-height: 400px;
    overflow: visible; /* allow arrows outside the card */
}

/* Keep slide clipping inside a viewport wrapper (so arrows can be outside) */
.featured-product-slider .featured-slider-viewport {
    position: relative;
    width: 100%;
    min-height: 400px;
    overflow: hidden;
    border-radius: 18px;
}

.featured-product-slider .featured-product-card {
    display: none;
    width: 100%;
    transition: opacity 0.5s ease, transform 0.5s ease;
}

/* Show first card by default (before JS loads) */
.featured-product-slider .featured-product-card:first-child {
    display: block;
}

.featured-product-slider .featured-product-card.active {
    display: block;
    animation: slideIn 0.5s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.featured-product-slider .slider-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.9);
    color: #667eea;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    cursor: pointer;
    z-index: 5;
    transition: background 0.3s ease, transform 0.3s ease;
}

.featured-product-slider .slider-nav-btn:hover {
    background: #ffffff;
    transform: translateY(-50%) scale(1.05);
}

.featured-product-slider .slider-nav-btn.prev {
    left: -26px;
}

.featured-product-slider .slider-nav-btn.next {
    right: -26px;
}

.featured-product-slider .slider-dots {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 6px;
    z-index: 5;
}

.featured-product-slider .slider-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    border: 1px solid rgba(102, 126, 234, 0.5);
    cursor: pointer;
    transition: background 0.3s ease, transform 0.3s ease;
}

.featured-product-slider .slider-dot.active {
    background: #667eea;
    transform: scale(1.1);
}

.featured-product-card {
    /* No hover effects - removed all transitions and transforms */
}

/* Progress bar: ensure full track is visible and avoid "dot" look on low % */
.featured-product-card .stock-progress .progress {
    width: 100%;
    overflow: hidden;
}

.featured-product-card .stock-progress .progress-bar {
    min-width: 8px;
}

.offer-card:hover {
    transform: translateY(-5px);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.product-card:hover .product-thumb img {
    transform: scale(1.1);
}

.product-card:hover .product-button-group {
    opacity: 1;
}

.review-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

/* Review Section Styles */
.review-list-scrollable {
    scrollbar-width: thin;
    scrollbar-color: #c0c0c0 #f0f0f0;
}

.review-list-scrollable::-webkit-scrollbar {
    width: 6px;
}

.review-list-scrollable::-webkit-scrollbar-track {
    background: #f0f0f0;
    border-radius: 3px;
}

.review-list-scrollable::-webkit-scrollbar-thumb {
    background: #c0c0c0;
    border-radius: 3px;
}

.review-list-scrollable::-webkit-scrollbar-thumb:hover {
    background: #a0a0a0;
}

.review-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.review-image-link {
    transition: transform 0.2s ease;
    display: inline-block;
}

.review-image-link:hover {
    transform: scale(1.1);
    z-index: 10;
    position: relative;
}

.review-image-link img {
    transition: border-color 0.2s ease;
}

.review-image-link:hover img {
                                    border-color: #4E65FF !important;
}

.rating-summary-card {
    position: sticky;
    top: 20px;
}

@media (max-width: 992px) {
    .rating-summary-card {
        position: relative;
        top: 0;
        margin-top: 2rem;
    }
    
    .review-list-scrollable {
        max-height: 400px !important;
    }
}

/* Best Selling Products Slider Styles */
.best-selling-section,
.featured-grid-section {
    background: #fff;
    padding: 60px 0;
}

.best-selling-slider,
.featured-products-slider {
    position: relative;
    width: 100%;
}

/* Container for proper width */
.best-selling-section .container,
.featured-grid-section .container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 15px;
}

/* Owl Carousel specific styles - let owl carousel handle layout completely */
.best-selling-slider.owl-carousel,
.featured-products-slider.owl-carousel {
    display: block;
    position: relative;
    overflow: visible;
}

.best-selling-slider.owl-carousel .owl-stage-outer,
.featured-products-slider.owl-carousel .owl-stage-outer {
    overflow: hidden;
    position: relative;
    padding: 20px 0;
    margin: 0 -5px;
}

.best-selling-slider.owl-carousel,
.featured-products-slider.owl-carousel {
    overflow: visible;
    position: relative;
    padding: 0 15px;
}

.best-selling-slider.owl-carousel .owl-stage,
.featured-products-slider.owl-carousel .owl-stage {
    display: flex;
    align-items: stretch;
}

.best-selling-slider.owl-carousel .owl-item,
.featured-products-slider.owl-carousel .owl-item {
    display: flex;
    height: auto;
}

.best-selling-slider.owl-carousel .owl-item .item,
.featured-products-slider.owl-carousel .owl-item .item {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 0 5px;
}

/* Remove any conflicting styles - let owl carousel handle everything */
.best-selling-slider.owl-carousel .item,
.featured-products-slider.owl-carousel .item {
    /* Let owl carousel control display */
}

/* Professional product card styling - matching featured product design */
.best-selling-slider .product-card,
.featured-products-slider .product-card {
    margin-bottom: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
}

/* Remove hover effects for best selling and featured product cards */
.best-selling-slider .product-card:hover,
.featured-products-slider .product-card:hover {
    transform: none !important;
    box-shadow: none !important;
}

.best-selling-slider .product-image-wrapper,
.featured-products-slider .product-image-wrapper {
    position: relative;
    overflow: hidden;
}

.best-selling-slider .product-image-wrapper img {
    transition: transform 0.5s ease;
}

.best-selling-slider .product-card:hover .product-image-wrapper img,
.featured-products-slider .product-card:hover .product-image-wrapper img {
    transform: none !important;
}

/* Remove hover effects for hero featured product cards */
.hero-section .featured-product-card:hover {
    transform: none !important;
    box-shadow: none !important;
}

.hero-section .featured-product-card:hover .product-image-wrapper img {
    transform: none !important;
}

.order-now-btn-home{
    border: 2px solid #DD2476 !important;
    font-size: 15px !important;

}

/* Order Now Button Hover & Active Styles - Best Selling & Featured Sections */
.order-now-btn-home:hover,
.order-now-btn-home:active,
.order-now-btn-home:focus {
    background: transparent !important;
    background-image: none !important;
    background-color: transparent !important;
    background-clip: border-box !important;
    border: 2px solid #DD2476 !important;
    color: #DD2476 !important;
    box-shadow: none !important;
}

.order-now-btn-home:hover *,
.order-now-btn-home:active *,
.order-now-btn-home:focus * {
    color: #DD2476 !important;
}

.order-now-btn-home:hover i,
.order-now-btn-home:active i,
.order-now-btn-home:focus i {
    color: #DD2476 !important;
}

.add_to_single_cart {
    font-size: 15px !important;
}

/* Add to Cart Button Hover Styles - Best Selling & Featured Sections */
.best-selling-slider .product-card .add_to_single_cart:hover,
.featured-products-slider .product-card .add_to_single_cart:hover,
.best-selling-section .product-card .add_to_single_cart:hover,
.featured-grid-section .product-card .add_to_single_cart:hover,
.hero-section .featured-product-card .add_to_single_cart:hover {
    background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%) !important;
    border: 2px solid transparent !important;
    border-color: transparent !important;
    color: #fff !important;
}

.best-selling-slider .product-card .add_to_single_cart:hover *,
.featured-products-slider .product-card .add_to_single_cart:hover *,
.best-selling-section .product-card .add_to_single_cart:hover *,
.featured-grid-section .product-card .add_to_single_cart:hover *,
.hero-section .featured-product-card .add_to_single_cart:hover * {
    color: #fff !important;
}

.best-selling-slider .product-card .add_to_single_cart:hover i,
.featured-products-slider .product-card .add_to_single_cart:hover i,
.best-selling-section .product-card .add_to_single_cart:hover i,
.featured-grid-section .product-card .add_to_single_cart:hover i,
.hero-section .featured-product-card .add_to_single_cart:hover i {
    color: #fff !important;
}

/* Single image display for best selling products */
.best-selling-slider .product-image-wrapper {
    display: block;
    width: 100%;
}

.best-selling-slider .stock-progress {
    margin-bottom: 15px;
}

.best-selling-slider .stock-progress .progress {
    width: 100%;
    overflow: hidden;
}

.best-selling-slider .stock-progress .progress-bar {
    min-width: 8px;
}

/* Hide any undefined elements or owl carousel dots */
.best-selling-slider .owl-dots,
.best-selling-slider .owl-dot,
.best-selling-slider [class*="undefined"],
.best-selling-slider *:contains("undefined"),
.featured-products-slider .owl-dots,
.featured-products-slider .owl-dot,
.featured-products-slider [class*="undefined"],
.featured-products-slider *:contains("undefined"),
.featured-grid-section .owl-dots,
.featured-grid-section .owl-dot,
.featured-grid-section [class*="undefined"],
.featured-grid-section *:contains("undefined") {
    display: none !important;
    visibility: hidden !important;
}

/* Additional styling for best selling product cards matching featured design */
.best-selling-slider .product-card .px-4 {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.best-selling-slider .product-name,
.best-selling-slider .product-name a {
    color: #232323;
    transition: all 0.3s ease;
    font-size: 20px !important;
}

.best-selling-slider .product-name a:hover {
    background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Navigation arrows */
.best-selling-slider .owl-nav,
.featured-products-slider .owl-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: calc(100% + 30px);
    left: -15px;
    display: flex;
    justify-content: space-between;
    pointer-events: auto;
    margin-top: 0;
    z-index: 10000;
    padding: 0;
}

.best-selling-slider .owl-nav button,
.featured-products-slider .owl-nav button {
    pointer-events: all !important;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #f5f5f5 !important;
    color: #232323 !important;
    border: 1px solid #e5e5e5 !important;
    display: flex !important;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    margin: 0;
    cursor: pointer !important;
    z-index: 10001 !important;
    position: absolute !important;
    opacity: 1 !important;
    visibility: visible !important;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
}

.best-selling-slider .owl-nav button span,
.best-selling-slider .owl-nav button i,
.featured-products-slider .owl-nav button span,
.featured-products-slider .owl-nav button i {
    display: block;
    line-height: 1;
}

/* Ensure only one icon per button - hide all but first */
.best-selling-slider .owl-nav button i:not(:first-child),
.best-selling-slider .owl-nav button span:not(:first-child),
.featured-products-slider .owl-nav button i:not(:first-child),
.featured-products-slider .owl-nav button span:not(:first-child) {
    display: none !important;
}

/* Remove any text content from buttons */
.best-selling-slider .owl-nav button,
.featured-products-slider .owl-nav button {
    text-indent: 0;
    overflow: hidden;
}

.best-selling-slider .owl-nav button:before,
.best-selling-slider .owl-nav button:after,
.featured-products-slider .owl-nav button:before,
.featured-products-slider .owl-nav button:after {
    display: none !important;
    content: none !important;
}

.best-selling-slider .owl-nav button:hover,
.featured-products-slider .owl-nav button:hover {
    transform: scale(1.15);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    background: #ebebeb !important;
    border-color: #d5d5d5 !important;
}

.best-selling-slider .owl-nav button.owl-prev,
.featured-products-slider .owl-nav button.owl-prev {
    left: -15px !important;
    position: absolute !important;
}

.best-selling-slider .owl-nav button.owl-next,
.featured-products-slider .owl-nav button.owl-next {
    right: -15px !important;
    position: absolute !important;
}

/* Ensure buttons are always visible and clickable */
.best-selling-slider .owl-nav button:not(.disabled),
.featured-products-slider .owl-nav button:not(.disabled) {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: all !important;
    display: flex !important;
}

/* Prevent any overlay from blocking clicks - but allow button itself */
.best-selling-slider .owl-nav button *,
.featured-products-slider .owl-nav button * {
    pointer-events: none;
}

.best-selling-slider .owl-nav button i,
.featured-products-slider .owl-nav button i {
    pointer-events: none;
}

/* Ensure no other elements are blocking the buttons */
.best-selling-slider .owl-stage-outer,
.best-selling-slider .owl-stage,
.best-selling-slider .owl-item {
    pointer-events: auto;
}

/* Make sure buttons are above everything */
.best-selling-slider .owl-nav button,
.featured-products-slider .owl-nav button {
    position: absolute !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
}

.best-selling-slider .owl-nav button.disabled,
.featured-products-slider .owl-nav button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 1400px) {
    .best-selling-slider.owl-carousel {
        padding: 0 10px;
    }
    
    .best-selling-slider .owl-nav {
        width: calc(100% + 20px);
        left: -10px;
    }
    
    .best-selling-slider .owl-nav button.owl-prev {
        left: -10px;
    }
    
    .best-selling-slider .owl-nav button.owl-next {
        right: -10px;
    }
}

@media (max-width: 1200px) {
    .best-selling-slider.owl-carousel {
        padding: 0 10px;
    }
    
    .best-selling-slider .owl-nav {
        width: calc(100% + 20px);
        left: -10px;
    }
    
    .best-selling-slider .owl-nav button.owl-prev {
        left: -10px;
    }
    
    .best-selling-slider .owl-nav button.owl-next {
        right: -10px;
    }
}

/* Price row: old price left, main price right - desktop and mobile */
.home-product-price-row {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
    gap: 10px !important;
}

.home-product-old-price {
    margin-bottom: 0 !important;
    flex-shrink: 0 !important;
}

.home-product-main-price {
    flex-shrink: 0 !important;
}

.best-selling-slider .product-card .home-product-price-row del,
.featured-products-slider .product-card .home-product-price-row del,
.hero-section .featured-product-card .home-product-price-row del,
.best-selling-section .product-card .home-product-price-row del,
.featured-grid-section .product-card .home-product-price-row del {
    font-size: 14px !important;
    text-align: left !important;
    white-space: nowrap !important;
}

.best-selling-slider .product-card .home-product-main-price .fw-bold,
.featured-products-slider .product-card .home-product-main-price .fw-bold,
.hero-section .featured-product-card .home-product-main-price .fw-bold,
.best-selling-section .product-card .home-product-main-price .fw-bold,
.featured-grid-section .product-card .home-product-main-price .fw-bold {
    font-size: 20px !important;
    text-align: right !important;
    white-space: nowrap !important;
}

@media (max-width: 768px) {
    .hero-section {
        border-radius: 0 0 30px 30px;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .section-title {
        font-size: 2rem !important;
    }
    
    /* Best Selling Section Mobile */
    .best-selling-section,
    .featured-grid-section {
        padding: 2rem 0 !important;
    }
    
    .best-selling-section .container,
    .featured-grid-section .container {
        padding: 0 8px !important;
    }
    
    .best-selling-slider.owl-carousel,
    .featured-products-slider.owl-carousel {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .best-selling-slider .owl-stage-outer,
    .featured-products-slider .owl-stage-outer {
        padding: 8px 0 !important;
    }
    
    /* Hide navigation arrows on mobile */
    .best-selling-slider .owl-nav,
    .featured-products-slider .owl-nav {
        display: none !important;
    }
    
    /* Product cards on mobile - 2 per row with minimal gap */
    .best-selling-slider .owl-item,
    .featured-products-slider .owl-item {
        padding: 0 2px !important;
    }
    
    .best-selling-slider.owl-carousel,
    .featured-products-slider.owl-carousel {
        margin: 0 -2px !important;
    }
    
    .best-selling-slider .product-card,
    .featured-products-slider .product-card {
        width: 100% !important;
        min-height: auto !important;
        margin: 0 !important;
        padding: 0 0 8px 0 !important;
        display: flex !important;
        flex-direction: column !important;
    }
    
    /* Larger product image on mobile - fills most of the card */
    .best-selling-slider .product-image-wrapper,
    .featured-products-slider .product-image-wrapper {
        height: 200px !important;
        padding: 8px !important;
        margin: -1px -1px 0 -1px !important;
    }
    
    .best-selling-slider .product-image-wrapper img,
    .featured-products-slider .product-image-wrapper img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }
    
    .best-selling-slider .product-card .px-4,
    .featured-products-slider .product-card .px-4 {
        padding: 10px 12px 8px 12px !important;
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
    }
    
    /* Product name - aligned left */
    .best-selling-slider .product-name,
    .featured-products-slider .product-name {
        font-size: 14px !important;
        /* line-height: 1.4 !important; */
        /* min-height: 38px !important; */
        margin-bottom: 8px !important;
        text-align: left !important;
    }
    
    .best-selling-slider .product-name a,
    .featured-products-slider .product-name a {
        font-size: 14px !important;
        -webkit-line-clamp: 2 !important;
        text-align: left !important;
        display: -webkit-box !important;
        -webkit-box-orient: vertical !important;
    }
    
    .best-selling-slider .rating-stars,
    .featured-products-slider .rating-stars {
        font-size: 11px !important;
        margin-bottom: 6px !important;
        text-align: left !important;
    }
    
    /* Remove gap between price and button */
    .best-selling-slider .product-card .mt-auto,
    .featured-products-slider .product-card .mt-auto {
        margin-top: 6px !important;
    }
    
    .best-selling-slider .product-card a[href*="product"],
    .best-selling-slider .product-card button,
    .featured-products-slider .product-card a[href*="product"],
    .featured-products-slider .product-card button {
        /* padding: 10px 16px !important; */
        font-size: 14px !important;
        border-radius: 8px !important;
        margin-top: 0 !important;
    }
    
    .best-selling-slider .product-card a[href*="product"] i,
    .best-selling-slider .product-card button i,
    .featured-products-slider .product-card a[href*="product"] i,
    .featured-products-slider .product-card button i {
        font-size: 12px !important;
    }
    
    /* Badge sizing on mobile */
    .best-selling-slider .badge,
    .featured-products-slider .badge {
        font-size: 9px !important;
        padding: 5px 10px !important;
    }
    
    .best-selling-slider .badge i,
    .featured-products-slider .badge i {
        font-size: 9px !important;
    }
    
    /* Position badges container */
    .best-selling-slider .product-card .position-absolute,
    .featured-products-slider .product-card .position-absolute {
        padding: 8px !important;
    }
    
    /* Mobile-specific price adjustments */
    @media (max-width: 576px) {
        .home-product-price-row {
            gap: 8px !important;
        }
        .hero-section .home-product-main-price .fw-bold {
            font-size: 18px !important;
        }
    }
}
</style>

<script>
// Remove undefined elements from featured products section
document.addEventListener('DOMContentLoaded', function() {
    // Remove undefined elements from featured products slider
    setTimeout(function() {
        var $featuredSection = $('.featured-grid-section, .featured-products-slider');
        if ($featuredSection.length) {
            // Remove owl dots and undefined elements
            $featuredSection.find('.owl-dots, .owl-dot, [class*="undefined"]').remove();
            $featuredSection.find('*').filter(function() {
                return $(this).text().trim() === 'undefined';
            }).remove();
            // Remove any elements containing "undefined" text
            $featuredSection.find('*').each(function() {
                if ($(this).text().indexOf('undefined') !== -1 && $(this).text().trim() === 'undefined') {
                    $(this).remove();
                }
            });
        }
    }, 1000);
});

document.addEventListener('DOMContentLoaded', function () {
    const slider = document.getElementById('featured-product-slider');
    if (!slider) return;

    const slides = Array.from(slider.querySelectorAll('.featured-product-card'));
    if (!slides.length) return;

    let currentIndex = 0;
    let autoTimer = null;

    // Create navigation buttons
    const prevBtn = document.createElement('button');
    prevBtn.className = 'slider-nav-btn prev';
    prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';

    const nextBtn = document.createElement('button');
    nextBtn.className = 'slider-nav-btn next';
    nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';

    slider.appendChild(prevBtn);
    slider.appendChild(nextBtn);

    // Create dots
    const dotsContainer = document.createElement('div');
    dotsContainer.className = 'slider-dots';
    slides.forEach((_, index) => {
        const dot = document.createElement('span');
        dot.className = 'slider-dot' + (index === 0 ? ' active' : '');
        dot.dataset.index = index;
        dotsContainer.appendChild(dot);
    });
    slider.appendChild(dotsContainer);

    const dots = Array.from(dotsContainer.querySelectorAll('.slider-dot'));

    function showSlide(newIndex) {
        if (newIndex === currentIndex) return;

        const total = slides.length;
        const normalize = (i) => (i + total) % total;
        const normalizedIndex = normalize(newIndex);

        const oldSlide = slides[currentIndex];
        const newSlide = slides[normalizedIndex];

        // Hide old slide
        oldSlide.classList.remove('active');
        oldSlide.style.display = 'none';

        // Show new slide
        newSlide.classList.add('active');
        newSlide.style.display = 'block';

        // Update dots
        dots.forEach(dot => dot.classList.remove('active'));
        if (dots[normalizedIndex]) {
            dots[normalizedIndex].classList.add('active');
        }

        currentIndex = normalizedIndex;
    }

    function nextSlide() {
        showSlide(currentIndex + 1);
    }

    function prevSlide() {
        showSlide(currentIndex - 1);
    }

    function startAuto() {
        if (autoTimer) clearInterval(autoTimer);
        if (slides.length <= 1) return;
        autoTimer = setInterval(nextSlide, 5000);
    }

    function stopAuto() {
        if (autoTimer) {
            clearInterval(autoTimer);
            autoTimer = null;
        }
    }

    // Initial state - ensure first slide is active and visible
    slides.forEach((slide, index) => {
        if (index === 0) {
            slide.classList.add('active');
            slide.style.display = 'block';
        } else {
            slide.classList.remove('active');
            slide.style.display = 'none';
        }
    });

    // Event listeners
    nextBtn.addEventListener('click', function () {
        stopAuto();
        nextSlide();
        startAuto();
    });

    prevBtn.addEventListener('click', function () {
        stopAuto();
        prevSlide();
        startAuto();
    });

    dots.forEach(dot => {
        dot.addEventListener('click', function () {
            const index = parseInt(this.dataset.index, 10);
            stopAuto();
            showSlide(index);
            startAuto();
        });
    });

    // Pause on hover
    slider.addEventListener('mouseenter', stopAuto);
    slider.addEventListener('mouseleave', startAuto);

    // Start autoplay
    startAuto();
});

// Best Selling Products Slider is initialized in extraindex.js
// This ensures it loads at the right time with other sliders
</script>
@endsection

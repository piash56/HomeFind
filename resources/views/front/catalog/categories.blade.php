@extends('master.front')
@section('meta')
<meta name="keywords" content="{{$setting->meta_keywords}}">
<meta name="description" content="{{$setting->meta_description}}">
@endsection
@section('title')
    {{__('All Categories')}}
@endsection

@section('content')
<!-- Page Content-->
<div class="container padding-bottom-3x padding-top-2x mb-1">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="page-title fw-bold mb-2" style="font-size: 2.5rem; color: #232323;">{{__('All Categories')}}</h1>
            <p class="text-muted">{{__('Browse products by category')}}</p>
            <div class="title-divider mx-auto mb-4" style="width: 80px; height: 4px; background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%); border-radius: 2px;"></div>
        </div>
    </div>

    <!-- Categories Grid -->
    @if($categories->count() > 0)
    <div class="row g-3">
        @foreach($categories as $category)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="category-card-wrapper">
                <div class="category-card bg-white rounded-4 shadow-lg position-relative overflow-hidden h-100" style="border: 1px solid rgba(102, 126, 234, 0.1); transition: all 0.3s ease; min-height: 310px; display: flex; flex-direction: column;">
                    <!-- Product Count Badge -->
                    <div class="product-count-badge position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                        <span class="badge px-2 py-1 text-white" style="font-size: 11px; font-weight: 700; background: linear-gradient(135deg, #DD2476 0%, #FF512F 100%); border: none; border-radius: 15px; box-shadow: 0 2px 8px rgba(221, 36, 118, 0.4); white-space: nowrap;">
                            <i class="fas fa-box me-1"></i>{{$category->items_count}}
                        </span>
                    </div>
                    
                    <!-- Category Image -->
                    <div class="category-image-wrapper position-relative" style="height: 200px; overflow: hidden; border-radius: 12px 12px 0 0; margin: -3px -3px 0 -3px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <a href="{{route('front.products').'?category='.$category->slug}}" class="d-block h-100 w-100 position-relative">
                            <div class="category-image-overlay position-absolute w-100 h-100" style="background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 100%); z-index: 1;"></div>
                            <img class="lazy category-image position-absolute"
                                 data-src="{{asset('storage/images/'.$category->photo)}}" 
                                 alt="{{$category->name}}" 
                                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; top: 0; left: 0;">
                        </a>
                    </div>
                    
                    <!-- Category Content -->
                    <div class="category-content px-3 py-2" style="flex: 1; display: flex; flex-direction: column; background: #fff; justify-content: space-between;">
                        <!-- Category Name -->
                        <h3 class="category-name text-center" style="font-size: 18px; font-weight: 700; line-height: 1.3; min-height: 44px; color: #232323; margin-bottom: 0; padding-top: 6px; display: flex; align-items: center; justify-content: center;">
                            <a href="{{route('front.products').'?category='.$category->slug}}" class="text-dark text-decoration-none category-name-link">
                                {{$category->name}}
                            </a>
                        </h3>
                        
                        <!-- View Products Button -->
                        <div class="mt-auto" style="margin-top: 10px;">
                            <a href="{{route('front.products').'?category='.$category->slug}}" 
                               class="category-view-btn w-100 fw-bold text-white d-flex align-items-center justify-content-center" 
                               style="border-radius: 10px; background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%); border: none; padding: 11px 16px; font-size: 14px; box-shadow: 0 4px 15px rgba(255, 81, 47, 0.25); transition: all 0.3s ease; text-decoration: none; position: relative; overflow: hidden; white-space: nowrap;">
                                <span class="position-relative d-inline-flex align-items-center" style="z-index: 2;">
                                    <i class="fas fa-shopping-bag me-2"></i><span>{{__('View Products')}}</span>
                                </span>
                                <span class="btn-shine position-absolute" style="top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.5s;"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-5">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>{{__('No categories available at the moment.')}}
        </div>
    </div>
    @endif
</div>

<style>
/* Category Card Professional Design */
.category-card-wrapper {
    position: relative;
}

.category-card {
    border: 2px solid transparent !important;
    background: #fff !important;
}

.category-card:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12) !important;
    border-color: rgba(78, 101, 255, 0.2) !important;
}

.category-card:hover .category-image {
    transform: scale(1.15);
}

.category-card:hover .category-name-link {
    color: #4E65FF !important;
    background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.category-card:hover .category-view-btn {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 81, 47, 0.35) !important;
}

.category-card:hover .category-view-btn .btn-shine {
    left: 100%;
}

/* Product Count Badge */
.product-count-badge {
    animation: fadeInDown 0.5s ease;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Category Image Overlay */
.category-image-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.05) 100%);
    z-index: 1;
    pointer-events: none;
}

.category-card:hover .category-image-wrapper::before {
    background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 100%);
}

/* Category Name */
.category-name-link {
    transition: all 0.3s ease;
    display: inline-block;
}

/* Button Shine Effect */
.category-view-btn:hover .btn-shine {
    left: 100%;
}

/* Reduce gaps between cards */
.row.g-3 {
    margin-left: -6px;
    margin-right: -6px;
}

.row.g-3 > * {
    padding-left: 6px;
    padding-right: 6px;
    margin-bottom: 12px;
}

/* Ensure button text doesn't wrap */
.category-view-btn {
    white-space: nowrap !important;
    overflow: hidden;
    text-overflow: ellipsis;
}

.category-view-btn span,
.category-view-btn i {
    white-space: nowrap !important;
    display: inline-block;
}

.category-view-btn .position-relative {
    white-space: nowrap !important;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .category-card {
        min-height: 280px !important;
    }
    
    .category-image-wrapper {
        height: 160px !important;
        margin: -3px -3px 0 -3px !important;
    }
    
    .category-name {
        font-size: 16px !important;
        min-height: 40px !important;
        margin-bottom: 10px !important;
        padding-top: 6px !important;
    }
    
    .product-count-badge {
        top: 8px !important;
        right: 8px !important;
    }
    
    .product-count-badge .badge {
        font-size: 9px !important;
        padding: 5px 10px !important;
    }
    
    .category-view-btn {
        font-size: 13px !important;
        padding: 10px 17px !important;
    }
    
    .category-content {
        padding: 10px 12px !important;
    }
    
    .category-content .mt-auto {
        margin-top: 6px !important;
    }
}

@media (max-width: 576px) {
    .category-card {
        min-height: 250px !important;
    }
    
    .category-image-wrapper {
        height: 140px !important;
        margin: -3px -3px 0 -3px !important;
    }
    
    .category-name {
        font-size: 15px !important;
        min-height: 38px !important;
        margin-bottom: 8px !important;
    }
    
    .category-view-btn {
        font-size: 12px !important;
        padding: 9px 15px !important;
    }
    
    .category-view-btn i {
        font-size: 11px !important;
    }
}
</style>
@endsection

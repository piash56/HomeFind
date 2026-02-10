<div class="row g-3" id="main_div">
    @if($items->count() > 0)
        @if ($checkType != 'list')
            @foreach ($items as $item)
            <div class="col-xxl-3 col-md-4 col-6">
                <div class="product-card ">
                    @if ($item->is_stock())
                        <div class="product-badge text-white
                            @if($item->is_type == 'feature' || $item->is_type == 'best')
                            catalog-type-badge
                            @elseif($item->is_type == 'new')
                            bg-danger
                            @elseif($item->is_type == 'top')
                            bg-info
                            @elseif($item->is_type == 'flash_deal')
                            bg-success
                            @else
                            bg-secondary
                            @endif
                            " @if($item->is_type == 'feature' || $item->is_type == 'best') style="background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%) !important; border: none;" @endif"> {{  $item->is_type != 'undefine' ?  (str_replace('_',' ',__("$item->is_type"))) : ''   }}
                        </div>
                    @else
                    <div class="product-badge bg-secondary border-default text-body
                    ">{{__('out of stock')}}</div>
                    @endif

                @if($item->previous_price && $item->previous_price !=0)
                <div class="product-badge product-badge2 text-white catalog-percent-badge"> -{{PriceHelper::DiscountPercentage($item)}}</div>
                @endif
                <div class="product-thumb">
                    <img class="lazy" data-src="{{asset('storage/images/'.$item->thumbnail)}}" alt="Product">
                </div>
                <div class="product-card-body">
                    <div class="product-category">
                        <a href="{{route('front.products').'?category='.$item->category->slug}}">{{$item->category->name}}</a>
                    </div>
                    <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                        {{ Str::limit($item->name, 38) }}
                    </a></h3>
                    <div class="rating-stars">
                        {!! Helper::renderStarRating($item->reviews->avg('rating'))!!}
                    </div>
                    <h4 class="product-price">
                        @if ($item->previous_price !=0)
                        <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                        @endif
                        <span class="catalog-main-price">{{PriceHelper::grandCurrencyPrice($item)}}</span>
                    </h4>
                </div>

                </div>
            </div>
            @endforeach
        @else
            @foreach ($items as $item)
                <div class="col-lg-12">
                    <div class="product-card product-list">
                        <div class="product-thumb" >
                        @if ($item->is_stock())

                            <div class="product-badge text-white
                                @if($item->is_type == 'feature' || $item->is_type == 'best')
                                catalog-type-badge
                                @elseif($item->is_type == 'new')
                                bg-danger
                                @elseif($item->is_type == 'top')
                                bg-info
                                @elseif($item->is_type == 'flash_deal')
                                bg-success
                                @else
                                bg-secondary
                                @endif
                                " @if($item->is_type == 'feature' || $item->is_type == 'best') style="background: linear-gradient(135deg, #4E65FF 0%, #92EFFD 100%) !important; border: none;" @endif">{{  $item->is_type != 'undefine' ?  ucfirst(str_replace('_',' ',$item->is_type)) : ''   }}
                            </div>
                            @else
                            <div class="product-badge bg-secondary border-default text-body
                            ">{{__('out of stock')}}</div>
                            @endif
                            @if($item->previous_price && $item->previous_price !=0)
                            <div class="product-badge product-badge2 text-white catalog-percent-badge"> -{{PriceHelper::DiscountPercentage($item)}}</div>
                            @endif

                            <img class="lazy" data-src="{{asset('storage/images/'.$item->thumbnail)}}" alt="Product">
                            <div class="product-button-group">
                                <a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                                <a data-target="{{route('fornt.compare.product',$item->id)}}" class="product-button product_compare" href="javascript:;" title="{{__('Compare')}}"><i class="icon-repeat"></i></a>
                                @include('includes.item_footer',['sitem' => $item])
                            </div>
                        </div>
                            <div class="product-card-inner">
                                <div class="product-card-body">
                                    <div class="product-category"><a href="{{route('front.products').'?category='.$item->category->slug}}">{{$item->category->name}}</a></div>
                                    <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                                        {{ Str::limit($item->name, 52) }}
                                    </a></h3>
                                    <div class="rating-stars">
                                        {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                    </div>
                                    <h4 class="product-price">
                                        @if ($item->previous_price !=0)
                                        <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                                        @endif
                                        <span class="catalog-main-price">{{PriceHelper::grandCurrencyPrice($item)}}</span>
                                    </h4>
                                    <p class="text-sm sort_details_show  text-muted hidden-xs-down my-1">
                                    {{ Str::limit(strip_tags($item->sort_details), 100) }}
                                    </p>
                                </div>


                            </div>
                        </div>
                </div>
            @endforeach
        @endif
    @else
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="h4 mb-0">{{ __('No Product Found') }}</h4>
                </div>
            </div>
        </div>
    @endif
</div>


<!-- Pagination-->
<div class="row mt-15" id="item_pagination">
    <div class="col-lg-12 text-center">
        {{$items->links()}}
    </div>
</div>

<style>
#main_div .catalog-percent-badge {
    background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%) !important;
}
/* Override master gradient text so catalog main price is solid #4E65FF */
#main_div .product-card .product-price .catalog-main-price {
    color: #4E65FF !important;
    -webkit-text-fill-color: #4E65FF !important;
    background: none !important;
    background-clip: unset !important;
    -webkit-background-clip: unset !important;
    font-weight: 600;
    font-size: 1.15em;
}
#main_div .product-price del {
    color: #6c757d;
}
</style>
<script type="text/javascript" src="{{asset('assets/front/js/catalog.js')}}"></script>

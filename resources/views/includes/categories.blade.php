
    @php
        $categories = App\Models\Category::whereStatus(1)->orderby('serial','asc')->take(8)->get();
    @endphp


    <div class="left-category-area">
        <div class="category-header">
            <h4><i class="icon-align-justify"></i> {{ __('Categories') }}</h4>
        </div>
        <div class="category-list">
            @foreach ($categories as $key => $pcategory)
                <div class="c-item">
                    <a class="d-block navi-link" href="{{route('front.catalog').'?category='.$pcategory->slug}}">
                        <img class="lazy" data-src="{{asset('storage/images/'.$pcategory->photo)}}">
                        <span class="text-gray-dark">{{$pcategory->name}}</span>
                    </a>
                </div>
            @endforeach
        <a href="{{route('front.catalog')}}" class="d-block navi-link view-all-category">
            <img class="lazy" data-src="{{ asset('storage/images/category.jpg') }}" alt="">
            <span class="text-gray-dark">{{ __('All Categories')}}</span>
        </a>
    </div>


    </div>



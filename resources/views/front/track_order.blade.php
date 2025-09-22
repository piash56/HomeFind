@extends('master.front')
@section('title')
    {{__('Order Track')}}
@endsection

@section('content')
@php
    $currentRoute = Route::currentRouteName();
    $hideBreadcrumbs = in_array($currentRoute, ['front.product', 'front.checkout.billing', 'front.checkout.success', 'front.order.track']);
@endphp

@if (!$hideBreadcrumbs)
<div class="page-title">
    <div class="container">
      <div class="row">
          <div class="col-lg-12">
            <ul class="breadcrumbs">
                <li><a href="{{route('front.index')}}">{{__('Home')}}</a> </li>
                <li class="separator"></li>
                <li>{{ __('Track Order') }}</li>
              </ul>
          </div>
      </div>
    </div>
</div>
@endif
    <div class="container">
        <div class="row justify-content-center pt-5">
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input class="form-control" type="text" id="order_number" name="order_number" placeholder="{{ __('Order Number') }}">
                            <span class="input-group-addon"><i class="icon-map-pin"></i></span>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-4 mt-sm-0">
                        <button class="btn btn-primary btn-block mt-0" id="submit_number"  data-href="{{route('front.order.track.submit')}}" type="submit"><span>{{ __('Track Now') }}</span></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row py-4">
            <div class="col-lg-12">
                <div id="track-order">

                </div>
            </div>
        </div>
    </div>
@endsection


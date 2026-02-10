@extends('master.front')
@section('title')
    {{ __('Cart') }}
@endsection
@section('content')
<div class="container padding-bottom-3x padding-top-2x mb-1">
    <h1 class="mb-4">{{ __('Shopping Cart') }}</h1>
    <div class="cart-page-wrapper position-relative">
        <div id="cart-loader-overlay" class="cart-loader-overlay" style="display: none;">
            <div class="cart-loader-spinner"></div>
            <span class="cart-loader-text">{{ __('Updating cart...') }}</span>
        </div>
        <div id="view_cart_load">
            @if(Session::has('cart') && count(Session::get('cart')) > 0)
                @include('includes.cart')
            @else
                <div class="card border-0">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">{{ __('Your cart is empty.') }}</p>
                        <a href="{{ route('front.products') }}" class="btn btn-primary">{{ __('Continue shopping') }}</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<style>
.cart-page-wrapper { min-height: 120px; }
.cart-loader-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.85);
    z-index: 100;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}
.cart-loader-spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #e9ecef;
    border-top-color: #4E65FF;
    border-radius: 50%;
    animation: cart-loader-spin 0.9s linear infinite;
}
.cart-loader-text {
    margin-top: 12px;
    font-size: 14px;
    color: #495057;
}
@keyframes cart-loader-spin {
    to { transform: rotate(360deg); }
}
</style>
@endsection

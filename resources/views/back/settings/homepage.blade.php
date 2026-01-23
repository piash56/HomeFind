@extends('master.back')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Home Page Settings') }}</b></h3>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="p-5">
                                <form class="admin-form" action="{{ route('back.setting.homepage.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @include('alerts.alerts')

                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{ __('Hero Section Badge') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Badge Text') }}</label>
                                                        <input type="text" class="form-control" name="hero_badge_text" value="{{ $setting->getHeroSetting('badge_text', __('Premium Quality Products at Best Prices')) }}" placeholder="{{ __('Premium Quality Products at Best Prices') }}">
                                                        <small class="form-text text-muted">{{ __('Text displayed in the badge at the top of hero section') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Badge Icon') }} <small>({{ __('Font Awesome class') }})</small></label>
                                                        <input type="text" class="form-control" name="hero_badge_icon" value="{{ $setting->getHeroSetting('badge_icon', 'fas fa-bolt') }}" placeholder="fas fa-bolt">
                                                        <small class="form-text text-muted">{{ __('Example: fas fa-bolt, fas fa-star, fas fa-gift') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{ __('Hero Section Headline') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Headline Line 1') }}</label>
                                                        <input type="text" class="form-control" name="hero_headline_line1" value="{{ $setting->getHeroSetting('headline_line1', __('Shop Smart,')) }}" placeholder="{{ __('Shop Smart,') }}">
                                                        <small class="form-text text-muted">{{ __('First line of the main headline') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Headline Line 2') }}</label>
                                                        <input type="text" class="form-control" name="hero_headline_line2" value="{{ $setting->getHeroSetting('headline_line2', __('Save More')) }}" placeholder="{{ __('Save More') }}">
                                                        <small class="form-text text-muted">{{ __('Second line of the headline (will be highlighted)') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{ __('Hero Section Description') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>{{ __('Description Text') }}</label>
                                                <textarea class="form-control" name="hero_description" rows="3" placeholder="{{ __('Discover premium products with unbeatable deals. Quality you can trust, prices you\'ll love.') }}">{{ $setting->getHeroSetting('description', '') }}</textarea>
                                                <small class="form-text text-muted">{{ __('Description text displayed below the headline') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{ __('Hero Section Buttons') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <h6 class="text-primary">{{ __('Button 1 (Primary)') }}</h6>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Button 1 Text') }}</label>
                                                        <input type="text" class="form-control" name="hero_button1_text" value="{{ $setting->getHeroSetting('button1_text', __('Explore Hot Deals')) }}" placeholder="{{ __('Explore Hot Deals') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Button 1 Link') }}</label>
                                                        <input type="text" class="form-control" name="hero_button1_link" value="{{ $setting->getHeroSetting('button1_link', route('front.products')) }}" placeholder="{{ route('front.products') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Button 1 Icon') }} <small>({{ __('Font Awesome class') }})</small></label>
                                                        <input type="text" class="form-control" name="hero_button1_icon" value="{{ $setting->getHeroSetting('button1_icon', 'fas fa-fire') }}" placeholder="fas fa-fire">
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <h6 class="text-primary">{{ __('Button 2 (Secondary)') }}</h6>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Button 2 Text') }}</label>
                                                        <input type="text" class="form-control" name="hero_button2_text" value="{{ $setting->getHeroSetting('button2_text', __('Shop Now')) }}" placeholder="{{ __('Shop Now') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Button 2 Link') }}</label>
                                                        <input type="text" class="form-control" name="hero_button2_link" value="{{ $setting->getHeroSetting('button2_link', route('front.products')) }}" placeholder="{{ route('front.products') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Button 2 Icon') }} <small>({{ __('Font Awesome class') }})</small></label>
                                                        <input type="text" class="form-control" name="hero_button2_icon" value="{{ $setting->getHeroSetting('button2_icon', 'fas fa-shopping-bag') }}" placeholder="fas fa-shopping-bag">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{ __('Hero Section Statistics') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <h6 class="text-primary">{{ __('Statistic 1') }}</h6>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Stat 1 Number') }}</label>
                                                        <input type="text" class="form-control" name="hero_stat1_number" value="{{ $setting->getHeroSetting('stat1_number', '') }}" placeholder="1000+">
                                                        <small class="form-text text-muted">{{ __('Leave empty to use actual count from database') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Stat 1 Label') }}</label>
                                                        <input type="text" class="form-control" name="hero_stat1_label" value="{{ $setting->getHeroSetting('stat1_label', __('Products')) }}" placeholder="{{ __('Products') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <h6 class="text-primary">{{ __('Statistic 2') }}</h6>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Stat 2 Number') }}</label>
                                                        <input type="text" class="form-control" name="hero_stat2_number" value="{{ $setting->getHeroSetting('stat2_number', '') }}" placeholder="500+">
                                                        <small class="form-text text-muted">{{ __('Leave empty to use actual count from database') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Stat 2 Label') }}</label>
                                                        <input type="text" class="form-control" name="hero_stat2_label" value="{{ $setting->getHeroSetting('stat2_label', __('Orders')) }}" placeholder="{{ __('Orders') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <h6 class="text-primary">{{ __('Statistic 3') }}</h6>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Stat 3 Number') }}</label>
                                                        <input type="text" class="form-control" name="hero_stat3_number" value="{{ $setting->getHeroSetting('stat3_number', '') }}" placeholder="200+">
                                                        <small class="form-text text-muted">{{ __('Leave empty to use actual count from database') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Stat 3 Label') }}</label>
                                                        <input type="text" class="form-control" name="hero_stat3_label" value="{{ $setting->getHeroSetting('stat3_label', __('Happy Customers')) }}" placeholder="{{ __('Happy Customers') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-secondary">{{ __('Update') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

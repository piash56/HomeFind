@extends('master.back')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ __('General Settings') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('back.setting.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Site Title') }}</label>
                                        <input type="text" class="form-control" name="title" value="{{ $setting->title }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Tagline') }}</label>
                                        <input type="text" class="form-control" name="home_page_title" value="{{ $setting->home_page_title }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Logo') }}</label>
                                        <div class="mb-2">
                                            <img src="{{ $setting->logo ? asset('storage/images/' . $setting->logo) : asset('storage/images/placeholder.png') }}" alt="logo" style="height:50px;">
                                        </div>
                                        <input type="file" class="form-control" name="logo" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Favicon') }}</label>
                                        <div class="mb-2">
                                            <img src="{{ $setting->favicon ? asset('storage/images/' . $setting->favicon) : asset('storage/images/placeholder.png') }}" alt="favicon" style="height:32px;">
                                        </div>
                                        <input type="file" class="form-control" name="favicon" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <hr>
                                    <h5 class="mb-3">{{ __('Tracking & Pixels') }}</h5>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group d-flex align-items-center">
                                        <input type="checkbox" id="is_facebook_pixel" name="is_facebook_pixel" {{ $setting->is_facebook_pixel ? 'checked' : '' }} style="margin-right:10px;">
                                        <label class="mb-0" for="is_facebook_pixel">{{ __('Enable Facebook (Meta) Pixel') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="facebook_pixel">{{ __('Facebook Pixel Base Code') }}</label>
                                        <textarea class="form-control" id="facebook_pixel" name="facebook_pixel" rows="6" placeholder="&lt;!-- Meta Pixel Code --&gt;">{{ $setting->facebook_pixel }}</textarea>
                                        <small class="form-text text-muted">{{ __('Paste the full base code from Meta. It will be injected into the <head> when enabled.') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group d-flex align-items-center">
                                        <input type="checkbox" id="is_gtm" name="is_gtm" {{ $setting->is_gtm ? 'checked' : '' }} style="margin-right:10px;">
                                        <label class="mb-0" for="is_gtm">{{ __('Enable Google Tag Manager') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="gtm_head_code">{{ __('GTM Head Code (paste the <script> snippet)') }}</label>
                                        <textarea class="form-control" id="gtm_head_code" name="gtm_head_code" rows="6" placeholder="&lt;!-- Google Tag Manager --&gt; ...">{{ $setting->gtm_head_code }}</textarea>
                                        <small class="form-text text-muted">{{ __('This will be injected into the <head>.') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="gtm_body_code">{{ __('GTM Body Code (paste the <noscript> iframe)') }}</label>
                                        <textarea class="form-control" id="gtm_body_code" name="gtm_body_code" rows="4" placeholder="&lt;noscript&gt;...&lt;/noscript&gt;">{{ $setting->gtm_body_code }}</textarea>
                                        <small class="form-text text-muted">{{ __('This will be injected immediately after the opening <body>.') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



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
                                
                                <!-- SEO & Social Media Settings -->
                                <div class="col-md-12">
                                    <hr>
                                    <h5 class="mb-3">{{ __('SEO & Social Media Settings') }}</h5>
                                    <p class="text-muted mb-3">{{ __('These settings control how your website appears when shared on social media platforms like Facebook, WhatsApp, Twitter, etc.') }}</p>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('Meta Description') }} <small class="text-muted">({{ __('For social media sharing') }})</small></label>
                                        <textarea class="form-control" name="meta_description" rows="4" placeholder="{{ __('Enter a description of your website. This will appear when sharing your website URL on social media.') }}">{{ $setting->meta_description }}</textarea>
                                        <small class="form-text text-muted">{{ __('Recommended: 150-160 characters. This description will be used for social media previews (WhatsApp, Facebook, etc.).') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('Meta Keywords') }}</label>
                                        <input type="text" class="form-control" name="meta_keywords" value="{{ $setting->meta_keywords }}" placeholder="{{ __('e.g., ecommerce, shopping, online store') }}">
                                        <small class="form-text text-muted">{{ __('Comma-separated keywords relevant to your website.') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('Social Media Image') }} <small class="text-muted">({{ __('OG Image') }})</small></label>
                                        <div class="mb-2">
                                            <img src="{{ $setting->meta_image ? asset('storage/images/' . $setting->meta_image) : asset('storage/images/placeholder.png') }}" alt="meta image" style="max-width:300px; max-height:200px; border:1px solid #ddd; padding:5px;">
                                        </div>
                                        <input type="file" class="form-control" name="meta_image" accept="image/*">
                                        <small class="form-text text-muted">{{ __('Recommended size: 1200x627 pixels. This image will appear when sharing your website URL on social media.') }}</small>
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
                                
                                <div class="col-md-12">
                                    <hr>
                                    <h5 class="mb-3">{{ __('Top Bar Taglines') }}</h5>
                                    <p class="text-muted mb-3">{{ __('Customize the three taglines displayed in the top bar of your website.') }}</p>
                                </div>
                                
                                <!-- Tagline 1 -->
                                <div class="col-md-12 mb-3">
                                    <h6 class="text-primary">{{ __('Tagline 1') }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Tagline 1 Icon') }} <small>({{ __('Font Awesome class') }})</small></label>
                                        <input type="text" class="form-control" name="tagline1_icon" value="{{ $setting->tagline1_icon ?? 'fas fa-truck' }}" placeholder="fas fa-truck">
                                        <small class="form-text text-muted">{{ __('Example: fas fa-truck, fas fa-shipping-fast') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Tagline 1 Text') }}</label>
                                        <input type="text" class="form-control" name="tagline1_text" value="{{ $setting->tagline1_text ?? 'Free delivery over 500tk' }}" placeholder="{{ __('Free delivery over 500tk') }}">
                                    </div>
                                </div>
                                
                                <!-- Tagline 2 -->
                                <div class="col-md-12 mb-3 mt-3">
                                    <h6 class="text-primary">{{ __('Tagline 2') }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Tagline 2 Icon') }} <small>({{ __('Font Awesome class') }})</small></label>
                                        <input type="text" class="form-control" name="tagline2_icon" value="{{ $setting->tagline2_icon ?? 'fas fa-percent' }}" placeholder="fas fa-percent">
                                        <small class="form-text text-muted">{{ __('Example: fas fa-percent, fas fa-tag') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Tagline 2 Text') }}</label>
                                        <input type="text" class="form-control" name="tagline2_text" value="{{ $setting->tagline2_text ?? '5% off for website order' }}" placeholder="{{ __('5% off for website order') }}">
                                    </div>
                                </div>
                                
                                <!-- Tagline 3 -->
                                <div class="col-md-12 mb-3 mt-3">
                                    <h6 class="text-primary">{{ __('Tagline 3') }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Tagline 3 Icon') }} <small>({{ __('Font Awesome class') }})</small></label>
                                        <input type="text" class="form-control" name="tagline3_icon" value="{{ $setting->tagline3_icon ?? 'fas fa-gift' }}" placeholder="fas fa-gift">
                                        <small class="form-text text-muted">{{ __('Example: fas fa-gift, fas fa-star') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Tagline 3 Text') }}</label>
                                        <input type="text" class="form-control" name="tagline3_text" value="{{ $setting->tagline3_text ?? '2nd time? get your 15% voucher' }}" placeholder="{{ __('2nd time? get your 15% voucher') }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <hr>
                                    <h5 class="mb-3">{{ __('Header & Footer Visibility') }}</h5>
                                    <p class="text-muted mb-3">{{ __('Control whether header and footer are displayed on specific pages.') }}</p>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group d-flex align-items-center">
                                        <input type="checkbox" id="show_header_footer_product_page" name="show_header_footer_product_page" value="1" {{ $setting->show_header_footer_product_page ? 'checked' : '' }} style="margin-right:10px;">
                                        <label class="mb-0" for="show_header_footer_product_page">{{ __('Show Header & Footer on Single Product Page') }}</label>
                                    </div>
                                    <small class="form-text text-muted">{{ __('When enabled, the header and footer will be displayed on individual product pages.') }}</small>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group d-flex align-items-center">
                                        <input type="checkbox" id="show_header_footer_shop_page" name="show_header_footer_shop_page" value="1" {{ $setting->show_header_footer_shop_page ? 'checked' : '' }} style="margin-right:10px;">
                                        <label class="mb-0" for="show_header_footer_shop_page">{{ __('Show Header & Footer on Shop Page') }}</label>
                                    </div>
                                    <small class="form-text text-muted">{{ __('When enabled, the header and footer will be displayed on the shop/catalog page.') }}</small>
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



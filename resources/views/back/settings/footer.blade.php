@extends('master.back')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Footer Settings') }}</b></h3>
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
                                <form class="admin-form" action="{{ route('back.setting.footer.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @include('alerts.alerts')

                                    <!-- Quick Links Section -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{ __('Quick Links') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $quickLinks = $setting->footer_quick_links ? json_decode($setting->footer_quick_links, true) : [];
                                            @endphp
                                            @for($i = 1; $i <= 3; $i++)
                                            <div class="row mb-3">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>{{ __('Link Label') }} {{ $i }}</label>
                                                        <input type="text" class="form-control" name="quick_link_label_{{$i}}" 
                                                               value="{{ isset($quickLinks[$i-1]) ? $quickLinks[$i-1]['label'] : '' }}" 
                                                               placeholder="{{ __('e.g., Home, Products, About') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="form-group">
                                                        <label>{{ __('Link URL') }} {{ $i }}</label>
                                                        <input type="text" class="form-control" name="quick_link_url_{{$i}}" 
                                                               value="{{ isset($quickLinks[$i-1]) ? $quickLinks[$i-1]['url'] : '' }}" 
                                                               placeholder="{{ __('e.g., / or /products or https://example.com') }}">
                                                        <small class="form-text text-muted">{{ __('Use route name like: front.index, front.products or full URL') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endfor
                                        </div>
                                    </div>

                                    <!-- Contact Information Section -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{ __('Contact Information') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Address') }}</label>
                                                        <textarea class="form-control" name="footer_address" rows="2" placeholder="{{ __('Enter your business address') }}">{{ $setting->footer_address }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Phone') }}</label>
                                                        <input type="text" class="form-control" name="footer_phone" value="{{ $setting->footer_phone }}" placeholder="{{ __('e.g., +1 234 567 8900') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('Email') }}</label>
                                                        <input type="email" class="form-control" name="footer_email" value="{{ $setting->footer_email }}" placeholder="{{ __('e.g., info@example.com') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Working Hours Section -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{ __('Working Hours') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Working Days') }}</label>
                                                        <input type="text" class="form-control" name="working_days_from_to" value="{{ $setting->working_days_from_to }}" placeholder="{{ __('e.g., Monday - Friday') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Start Time') }}</label>
                                                        <input type="text" class="form-control" name="friday_start" value="{{ $setting->friday_start }}" placeholder="{{ __('e.g., 9:00 AM') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>{{ __('End Time') }}</label>
                                                        <input type="text" class="form-control" name="friday_end" value="{{ $setting->friday_end }}" placeholder="{{ __('e.g., 6:00 PM') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Social Media Links Section -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{ __('Social Media Links') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $socialLinks = $setting->social_link ? json_decode($setting->social_link, true) : ['icons' => [], 'links' => []];
                                                $socialIcons = $socialLinks['icons'] ?? [];
                                                $socialUrls = $socialLinks['links'] ?? [];
                                            @endphp
                                            <div id="social-links-container">
                                                @if(!empty($socialIcons) && count($socialIcons) > 0)
                                                    @foreach($socialIcons as $key => $icon)
                                                    <div class="row mb-3 social-link-row">
                                                        <div class="col-md-5">
                                                            <div class="form-group">
                                                                <label>{{ __('Icon Class') }} ({{ __('Font Awesome') }})</label>
                                                                <input type="text" class="form-control" name="social_icons[]" value="{{ $icon }}" placeholder="{{ __('e.g., fab fa-facebook-f') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>{{ __('Social Media URL') }}</label>
                                                                <input type="url" class="form-control" name="social_links[]" value="{{ $socialUrls[$key] ?? '' }}" placeholder="{{ __('https://facebook.com/yourpage') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-sm w-100 remove-social-link">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                @else
                                                    <div class="row mb-3 social-link-row">
                                                        <div class="col-md-5">
                                                            <div class="form-group">
                                                                <label>{{ __('Icon Class') }} ({{ __('Font Awesome') }})</label>
                                                                <input type="text" class="form-control" name="social_icons[]" placeholder="{{ __('e.g., fab fa-facebook-f') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>{{ __('Social Media URL') }}</label>
                                                                <input type="url" class="form-control" name="social_links[]" placeholder="{{ __('https://facebook.com/yourpage') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-sm w-100 remove-social-link">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm" id="add-social-link">
                                                <i class="fas fa-plus"></i> {{ __('Add Social Link') }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Copyright Section -->
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{ __('Copyright') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>{{ __('Copyright Text') }}</label>
                                                <textarea class="form-control" name="copy_right" rows="2" placeholder="{{ __('e.g., © 2024 Your Company Name. All rights reserved.') }}">{{ $setting->copy_right }}</textarea>
                                                <small class="form-text text-muted">{{ __('This text will appear at the bottom of the footer') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">{{ __('Update Footer Settings') }}</button>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add social link
    document.getElementById('add-social-link').addEventListener('click', function() {
        const container = document.getElementById('social-links-container');
        const newRow = document.createElement('div');
        newRow.className = 'row mb-3 social-link-row';
        newRow.innerHTML = `
            <div class="col-md-5">
                <div class="form-group">
                    <label>{{ __('Icon Class') }} ({{ __('Font Awesome') }})</label>
                    <input type="text" class="form-control" name="social_icons[]" placeholder="{{ __('e.g., fab fa-facebook-f') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __('Social Media URL') }}</label>
                    <input type="url" class="form-control" name="social_links[]" placeholder="{{ __('https://facebook.com/yourpage') }}">
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm w-100 remove-social-link">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newRow);
    });

    // Remove social link
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-social-link')) {
            const row = e.target.closest('.social-link-row');
            if (document.querySelectorAll('.social-link-row').length > 1) {
                row.remove();
            } else {
                alert('{{ __('At least one social link is required') }}');
            }
        }
    });
});
</script>
@endsection

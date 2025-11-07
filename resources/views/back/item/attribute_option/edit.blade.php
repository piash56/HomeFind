@extends('master.back')

@section('content')

<div class="container-fluid">

<!-- Option Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Edit Options') }}</b> </h3>
                <a class="btn btn-primary   btn-sm" href="{{route('back.option.index',$item->id)}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
                </div>
        </div>
    </div>

<!-- Form -->
<div class="row">

<div class="col-xl-12 col-lg-12 col-md-12">

<div class="card o-hidden border-0 shadow-lg">
<div class="card-body ">
<!-- Nested Row within Card Body -->
<div class="row justify-content-center">
<div class="col-lg-12">
<form class="admin-form" action="{{ route('back.option.update',[$item->id,$option->id]) }}"
method="POST" enctype="multipart/form-data">

                                    @csrf

                                    @method('PUT')

@include('alerts.alerts')

<div class="form-group">
                                        <label for="attribute_id">{{ __('Attribute') }} *</label>
                                        <select name="attribute_id" class="form-control" id="attribute_id" >
                                            <option value="">{{ __('Select Attribute') }}</option>
                                            @foreach($attributes as $attribute)
                                            <option value="{{ $attribute->id }}" {{ $attribute->id == $option->attribute_id ? 'selected' : '' }}>{{ $attribute->name }}</option>
                                            @endforeach
                                        </select>
</div>

<div class="form-group">
<label for="attr_name">{{ __('Name') }} *</label>
<input type="text" name="name" class="form-control" id="attr_name"
placeholder="{{ __('Enter Name') }}" value="{{ $option->name }}" >
</div>

                                    <div class="form-group">
<label for="stock">{{ __('Stock') }} *</label>
<input type="text" name="stock" class="form-control" id="stock"
placeholder="{{ __('Enter Stock') }}" value="{{ $option->stock }}" >
                                            <label for="unlimited">
                                                <input type="checkbox" {{$option->stock == 'unlimited' ? 'checked' : ''}} class="my-2" id="unlimited">
                                            {{__('Unlimited Stock')}}
                                            </label>
</div>

                                    <div class="form-group">
                                        <label for="price">{{ __('Price') }} *</label>
                                        <small>({{ __('Set 0 to make it free') }})</small>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span
                                                    class="input-group-text">{{ $curr->sign }}</span>
                                            </div>
                                            <input type="text" id="price"
                                                name="price" class="form-control"
                                                placeholder="{{ __('Enter Price') }}"
                                                value="{{ PriceHelper::setPrice($option->price) }}" >
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="image">{{ __('Attribute Image') }}</label>
                                        <small class="d-block mb-2 text-muted">{{ __('Optional: Upload an image for this attribute option') }}</small>
                                        @if($option->image)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/images/' . $option->image) }}" alt="{{ $option->name }}" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                            </div>
                                        @endif
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="image" id="image" accept="image/*">
                                                <label class="custom-file-label" for="image">{{ $option->image ? __('Change Image') : __('Choose Image') }}</label>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">{{ __('Recommended: 200x200px, max 2MB') }}</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="color_code">{{ __('Color Code') }}</label>
                                        <small class="d-block mb-2 text-muted">{{ __('Optional: Enter hex color code (e.g., #FF0000) or use color picker') }}</small>
                                        <div class="input-group">
                                            <input type="text" name="color_code" class="form-control jscolor" id="color_code" 
                                                value="{{ old('color_code', $option->color_code ?: '#FFFFFF') }}" placeholder="#FFFFFF">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="color_preview" style="width: 50px; background-color: {{ $option->color_code ?: '#FFFFFF' }};"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="gallery_image_id">{{ __('Gallery Image') }}</label>
                                        <small class="d-block mb-2 text-muted">{{ __('Optional: Select a gallery image to show when this option is selected') }}</small>
                                        <div class="position-relative">
                                            <select name="gallery_image_id" class="form-control custom-gallery-select" id="gallery_image_id" style="padding-left: 50px;">
                                                <option value="">{{ __('Select Gallery Image') }}</option>
                                                @foreach($galleries as $gallery)
                                                    <option value="{{ $gallery->id }}" 
                                                        data-image="{{ asset('storage/images/' . $gallery->photo) }}"
                                                        {{ ($option->gallery_image_id == $gallery->id) ? 'selected' : '' }}>
                                                        {{ __('Gallery Image') }} #{{ $gallery->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="gallery_select_preview" class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%); width: 35px; height: 35px; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                @if($option->gallery_image_id)
                                                    @php
                                                        $selectedGallery = $galleries->firstWhere('id', $option->gallery_image_id);
                                                    @endphp
                                                    @if($selectedGallery)
                                                        <img src="{{ asset('storage/images/' . $selectedGallery->photo) }}" 
                                                             alt="Preview" 
                                                             style="width: 100%; height: 100%; object-fit: cover;">
                                                    @else
                                                        <i class="fas fa-image text-muted" style="font-size: 14px;"></i>
                                                    @endif
                                                @else
                                                    <i class="fas fa-image text-muted" style="font-size: 14px;"></i>
                                                @endif
                                            </div>
                                        </div>
                                        <div id="gallery_image_preview" class="mt-2" style="min-height: 60px;">
                                            @if($option->gallery_image_id)
                                                @php
                                                    $selectedGallery = $galleries->firstWhere('id', $option->gallery_image_id);
                                                @endphp
                                                @if($selectedGallery)
                                                    <img src="{{ asset('storage/images/' . $selectedGallery->photo) }}" 
                                                         alt="Gallery Image" 
                                                         style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                                @endif
                                            @endif
                                        </div>
                                        @if(count($galleries) > 0)
                                            <small class="form-text text-muted">{{ __('Selected gallery image will replace the product featured image when this attribute is chosen') }}</small>
                                        @else
                                            <small class="form-text text-warning">{{ __('No gallery images available. Please add gallery images to the product first.') }}</small>
                                        @endif
                                    </div>

                                    <input type="hidden" id="attr_keyword" name="keyword" value="{{ $option->keyword }}">

<div class="form-group">
<button type="submit" class="btn btn-secondary">{{ __('Submit') }}</button>
</div>


<div>
</form>
</div>
</div>
</div>
</div>

</div>

</div>

</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/back/js/jscolor.js') }}"></script>
<script>
    $(document).ready(function() {
        // Update color preview when color changes
        $('#color_code').on('change', function() {
            var color = $(this).val();
            $('#color_preview').css('background-color', color);
        });

        // Custom file input label
        $('#image').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('label').addClass('selected').html(fileName || '{{ __('Choose Image') }}');
        });

        // Unlimited stock checkbox
        $('#unlimited').on('change', function() {
            if ($(this).is(':checked')) {
                $('#stock').val('unlimited').prop('readonly', true);
            } else {
                $('#stock').val('').prop('readonly', false);
            }
        });

        // Set initial state for unlimited checkbox
        if ($('#unlimited').is(':checked')) {
            $('#stock').prop('readonly', true);
        }

        // Gallery image preview
        $('#gallery_image_id').on('change', function() {
            var selectedOption = $(this).find(':selected');
            var imageUrl = selectedOption.data('image');
            var previewDiv = $('#gallery_image_preview');
            var selectPreview = $('#gallery_select_preview');
            
            // Update preview below dropdown
            previewDiv.empty();
            if (imageUrl && selectedOption.val() !== '') {
                previewDiv.html('<img src="' + imageUrl + '" alt="Gallery Image" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">');
            }
            
            // Update thumbnail in select dropdown
            selectPreview.empty();
            if (imageUrl && selectedOption.val() !== '') {
                selectPreview.html('<img src="' + imageUrl + '" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">');
            } else {
                selectPreview.html('<i class="fas fa-image text-muted" style="font-size: 14px;"></i>');
            }
        });

        // Initialize preview on page load
        $('#gallery_image_id').trigger('change');
    });
</script>
@endsection

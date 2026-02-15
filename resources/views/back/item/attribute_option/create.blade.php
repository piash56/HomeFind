@extends('master.back')

@section('content')

<div class="container-fluid">

<!-- Option Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Create  Options') }}</b></h3>
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
<form class="admin-form" action="{{ route('back.option.store',$item->id) }}" method="POST"
enctype="multipart/form-data">

                                    @csrf

@include('alerts.alerts')

<div class="form-group">
                                        <label for="attribute_id">{{ __('Attribute') }} *</label>
                                        <select name="attribute_id" class="form-control" id="attribute_id" >
                                            <option value="">{{ __('Select Attribute') }}</option>
                                            @foreach($attributes as $attribute)
                                            <option value="{{ $attribute->id }}" {{ $attribute->id == old('attribute_id') ? 'selected' : '' }}>{{ $attribute->name }}</option>
                                            @endforeach
                                        </select>
</div>

<div class="form-group">
<label for="attr_name">{{ __('Name') }} *</label>
<input type="text" name="name" class="form-control" id="attr_name"
placeholder="{{ __('Enter Name') }}" value="{{ old('name') }}" >
</div>

<div class="form-group">
<label for="stock">{{ __('Stock') }} *</label>
<input type="text" name="stock" class="form-control" id="stock"
placeholder="{{ __('Enter Stock') }}" value="{{ old('stock') }}" >
                                            <label for="unlimited">
                                                <input type="checkbox" class="my-2" id="unlimited">
                                            {{__('Unlimited Stock')}}
                                            </label>
</div>
                                    

                                    <div class="alert alert-info">
                                        <strong>{{ __('Price Behavior:') }}</strong>
                                        <ul class="mb-0 mt-2">
                                            <li><strong>{{ __('If both Old & New Price are set:') }}</strong> {{ __('This variation will REPLACE the main product price entirely') }}</li>
                                            <li><strong>{{ __('If only New Price is set (Old = 0):') }}</strong> {{ __('New Price will be ADDED to the main product price') }}</li>
                                            <li><strong>{{ __('If both are 0:') }}</strong> {{ __('Will use the main product price only') }}</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="previous_price">{{ __('Old Price') }}</label>
                                                <small class="d-block text-muted">({{ __('Optional: Set 0 if no old price') }})</small>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">{{ $curr->sign }}</span>
                                                    </div>
                                                    <input type="text" id="previous_price"
                                                        name="previous_price" class="form-control"
                                                        placeholder="{{ __('Enter Old Price') }}"
                                                        value="{{ old('previous_price', '0') }}" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="price">{{ __('New Price') }} *</label>
                                                <small class="d-block text-muted">({{ __('Set 0 to use main product price only') }})</small>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">{{ $curr->sign }}</span>
                                                    </div>
                                                    <input type="text" id="price"
                                                        name="price" class="form-control"
                                                        placeholder="{{ __('Enter New Price') }}"
                                                        value="{{ old('price') }}" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="image">{{ __('Variation Image') }}</label>
                                        <small class="d-block mb-2 text-muted">{{ __('Upload an image for this variation. This will be shown when the variation is selected.') }}</small>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="image" id="image" accept="image/*">
                                                <label class="custom-file-label" for="image">{{ __('Choose Image') }}</label>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">{{ __('Recommended: 800x800px, max 2MB') }}</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="color_code">{{ __('Color Code') }}</label>
                                        <small class="d-block mb-2 text-muted">{{ __('Optional: Enter hex color code (e.g., #FF0000) or use color picker') }}</small>
                                        <div class="input-group">
                                            <input type="text" name="color_code" class="form-control jscolor" id="color_code" 
                                                value="{{ old('color_code', '#FFFFFF') }}" placeholder="#FFFFFF">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="color_preview" style="width: 50px; background-color: {{ old('color_code', '#FFFFFF') }};"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="attr_keyword" name="keyword" value="{{ old('keyword') }}">

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
    });
</script>
@endsection

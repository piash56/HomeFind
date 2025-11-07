@extends('master.back')

@section('content')

<div class="container-fluid">

	<!-- Attribute Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Create Attribute') }}</b> </h3>
                <a class="btn btn-primary   btn-sm" href="{{route('back.attribute.index',$item->id)}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
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
								<form class="admin-form" action="{{ route('back.attribute.store',$item->id) }}" method="POST"
									enctype="multipart/form-data">

                                    @csrf

									@include('alerts.alerts')

									<div class="form-group">
										<label for="attr_name">{{ __('Name') }} *</label>
										<input type="text" name="name" class="form-control" id="attr_name"
											placeholder="{{ __('Enter Name') }}" value="{{ old('name') }}" >
									</div>

									<div class="form-group">
										<label for="display_type">{{ __('Display Type') }} *</label>
										<small class="d-block mb-2 text-muted">{{ __('Choose how this attribute will be displayed on the product page') }}</small>
										<select name="display_type" class="form-control" id="display_type">
											<option value="name" {{ old('display_type', 'name') == 'name' ? 'selected' : '' }}>{{ __('Name (Dropdown)') }}</option>
											<option value="color" {{ old('display_type') == 'color' ? 'selected' : '' }}>{{ __('Color Picker (Color Swatches)') }}</option>
											<option value="image" {{ old('display_type') == 'image' ? 'selected' : '' }}>{{ __('Image (Image Selector)') }}</option>
										</select>
										<small class="form-text text-muted">
											<strong>{{ __('Name') }}:</strong> {{ __('Shows options in a dropdown') }}<br>
											<strong>{{ __('Color') }}:</strong> {{ __('Shows color swatches that can be clicked') }}<br>
											<strong>{{ __('Image') }}:</strong> {{ __('Shows images that can be clicked') }}
										</small>
									</div>

                                    <input type="hidden" id="attr_keyword" name="keyword" value="{{ old('keyword') }}">
                                    <input type="hidden" name="item_id" value="{{ $item->id }}">

									<div class="form-group">
										<button type="submit" class="btn btn-secondary ">{{ __('Submit') }}</button>
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

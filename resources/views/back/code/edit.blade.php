@extends('master.back')

@section('content')

<div class="container-fluid">

	<!-- Code Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class=" mb-0 bc-title"><b>{{ __('Update Coupon') }}</b> </h3>
                <a class="btn btn-primary btn-sm" href="{{route('back.code.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
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
                            <form class="admin-form" action="{{ route('back.code.update',$code->id) }}"
                                method="POST" enctype="multipart/form-data">

                                @csrf

                                @method('PUT')

                                @include('alerts.alerts')

                                <div class="form-group">
                                    <label for="title">{{ __('Title') }} *</label>
                                    <input type="text" name="title" class="form-control" id="title"
                                        placeholder="{{ __('Enter Title') }}" value="{{ $code->title }}" >
                                </div>

                                <div class="form-group">
                                    <label for="code">{{ __('Code') }} *</label>
                                    <input type="text" name="code_name" class="form-control" id="code"
                                        placeholder="{{ __('Enter Code') }}" value="{{ $code->code_name }}" >
                                </div>

                                <div class="form-group">
                                    <label for="no_of_times">{{ __('Number Of Times') }} *</label>
                                    <input type="number" name="no_of_times" class="form-control" id="no_of_times"
                                        placeholder="{{ __('Enter Number Of Times') }}" value="{{ $code->no_of_times }}" min="1" >
                                </div>

                                <div class="form-group">
                                    <label for="product_id">{{ __('Specific Product (Optional)') }}</label>
                                    <select name="product_id" class="form-control" id="product_id">
                                        <option value="">{{ __('All Products') }}</option>
                                        @foreach(\App\Models\Item::orderBy('name')->get() as $product)
                                            <option value="{{ $product->id }}" {{ $code->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} - {{ PriceHelper::adminCurrencyPrice($product->discount_price) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">{{ __('Leave blank to apply coupon to all products') }}</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">{{ __('Start Date (Optional)') }}</label>
                                            <input type="date" name="start_date" class="form-control" id="start_date" value="{{ $code->start_date }}">
                                            <small class="text-muted">{{ __('Leave blank for no start date restriction') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_date">{{ __('End Date (Optional)') }}</label>
                                            <input type="date" name="end_date" class="form-control" id="end_date" value="{{ $code->end_date }}">
                                            <small class="text-muted">{{ __('Leave blank for no end date restriction') }}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="discount">{{ __('Discount') }}
                                        *</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <select name="type" class="form-control" id="discount_type">
                                                    <option value="percentage" {{$code->type == 'percentage' ? 'selected' : ''}}>{{__('Percentage')}} (%)</option>
                                                    <option value="amount" {{$code->type == 'amount' ? 'selected' : ''}}>{{__('Amount')}} ({{ PriceHelper::adminCurrency() }})</option>
                                                </select>
                                            </span>
                                        </div>
                                        <input type="number" id="discount"
                                            name="discount" class="form-control"
                                            placeholder="{{ __('Enter Discount') }}"
                                            min="0" step="0.1"
                                            value="{{ $code->type == 'amount' ? round($code->discount / $curr->value,2) : $code->discount }}" >
                                    </div>
                                    <small class="text-danger" id="discount_error" style="display:none;"></small>
                                </div>

								<div class="card bg-light p-3 mb-3">
									<h5 class="mb-3">{{ __('Free Delivery Option') }}</h5>
									
									<div class="form-group">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="is_free_delivery" name="is_free_delivery" value="1" {{ $code->is_free_delivery ? 'checked' : '' }}>
											<label class="custom-control-label" for="is_free_delivery">
												{{ __('Enable Free Delivery Coupon') }}
											</label>
										</div>
										<small class="text-muted">{{ __('When enabled, this coupon will provide free delivery instead of a price discount') }}</small>
									</div>

									<div class="form-group" id="minimum_amount_field" style="display: {{ $code->is_free_delivery ? 'block' : 'none' }};">
										<label for="minimum_order_amount">{{ __('Minimum Order Amount') }}</label>
										<input type="number" name="minimum_order_amount" class="form-control" id="minimum_order_amount"
											placeholder="{{ __('Enter Minimum Order Amount') }}" value="{{ $code->minimum_order_amount ?? 900 }}" min="0" step="0.01">
										<small class="text-muted">{{ __('Minimum cart subtotal required to apply this free delivery coupon') }}</small>
									</div>
								</div>

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

@section('scripts')
<script>
$(document).ready(function() {
    // Validate discount amount against product price
    function validateDiscount() {
        var productId = $('#product_id').val();
        var discountType = $('#discount_type').val();
        var discountValue = parseFloat($('#discount').val()) || 0;
        
        // Clear previous error
        $('#discount_error').hide().text('');
        
        if (productId && discountType === 'amount' && discountValue > 0) {
            // Get product price from the selected option text
            var selectedOption = $('#product_id option:selected');
            var priceText = selectedOption.text().split(' - ')[1];
            
            if (priceText) {
                // Extract numeric value from price (remove currency symbol and format)
                var priceMatch = priceText.match(/[\d,]+\.?\d*/);
                if (priceMatch) {
                    var productPrice = parseFloat(priceMatch[0].replace(/,/g, ''));
                    
                    if (discountValue > productPrice) {
                        $('#discount_error').show().text('{{ __('Discount amount cannot exceed product price') }}: ' + priceText);
                        $('button[type="submit"]').prop('disabled', true);
                        return false;
                    }
                }
            }
        }
        
        $('button[type="submit"]').prop('disabled', false);
        return true;
    }
    
    // Trigger validation on change
    $('#product_id, #discount_type, #discount').on('change keyup', validateDiscount);
    
    // Validate dates
    $('#start_date, #end_date').on('change', function() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        
        if (startDate && endDate && startDate > endDate) {
            alert('{{ __('End date must be after start date') }}');
            $('#end_date').val('');
        }
    });
    
    // Handle free delivery checkbox
    $('#is_free_delivery').on('change', function() {
        if ($(this).is(':checked')) {
            $('#minimum_amount_field').slideDown();
            // Hide discount fields when free delivery is enabled
            $('#discount, #discount_type').closest('.form-group').find('label').append(' <span class="text-muted">({{ __('Not applicable for free delivery') }})</span>');
            $('#discount').val(0).prop('readonly', true);
        } else {
            $('#minimum_amount_field').slideUp();
            $('#discount').prop('readonly', false);
            $('#discount, #discount_type').closest('.form-group').find('label span').remove();
        }
    });
    
    // Check on page load
    if ($('#is_free_delivery').is(':checked')) {
        $('#minimum_amount_field').show();
        $('#discount').val(0).prop('readonly', true);
    }
    
    // Initial validation
    validateDiscount();
});
</script>
@endsection

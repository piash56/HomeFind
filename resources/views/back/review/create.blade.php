@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class=" mb-0"><b>{{ __('Add Review') }}</b></h3>
                <a href="{{ route('admin.review.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Reviews') }}
                </a>
            </div>
        </div>
    </div>

	<!-- Create Form -->
	<div class="card shadow mb-4">
		<div class="card-body">
			@include('alerts.alerts')
			
			<form action="{{ route('admin.review.store') }}" method="POST" enctype="multipart/form-data">
				@csrf
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="customer_name">{{ __('Customer Name') }} *</label>
							<input type="text" class="form-control" id="customer_name" name="customer_name" 
								   value="{{ old('customer_name') }}" required>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="customer_phone">{{ __('Phone Number') }} *</label>
							<input type="text" class="form-control" id="customer_phone" name="customer_phone" 
								   value="{{ old('customer_phone') }}" required>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="order_id">{{ __('Order ID') }}</label>
							<input type="text" class="form-control" id="order_id" name="order_id" 
								   value="{{ old('order_id') }}" placeholder="{{ __('Enter Order ID (optional)') }}">
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="item_id">{{ __('Product') }} *</label>
							<div class="position-relative">
								<input type="text" class="form-control" id="product_search" 
									   placeholder="{{ __('Search and select product') }}" autocomplete="off" required>
								<input type="hidden" id="item_id" name="item_id" value="{{ old('item_id') }}" required>
								<div id="product_search_results" class="dropdown-menu w-100" style="display: none; position: absolute; top: 100%; z-index: 1000;">
									<!-- Search results will appear here -->
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="rating">{{ __('Rating') }} *</label>
							<select class="form-control" id="rating" name="rating" required>
								<option value="">{{ __('Select Rating') }}</option>
								<option value="1" {{ old('rating') == 1 ? 'selected' : '' }}>1 Star</option>
								<option value="2" {{ old('rating') == 2 ? 'selected' : '' }}>2 Stars</option>
								<option value="3" {{ old('rating') == 3 ? 'selected' : '' }}>3 Stars</option>
								<option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>4 Stars</option>
								<option value="5" {{ old('rating') == 5 ? 'selected' : '' }}>5 Stars</option>
							</select>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="status">{{ __('Status') }} *</label>
							<select class="form-control" id="status" name="status" required>
								<option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
								<option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
								<option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
							</select>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label for="review_text">{{ __('Review Text') }}</label>
					<textarea class="form-control" id="review_text" name="review_text" rows="4" 
							  placeholder="{{ __('Write review text...') }}">{{ old('review_text') }}</textarea>
				</div>
				
				<div class="form-group">
					<label for="review_images">{{ __('Review Images') }}</label>
					<input type="file" class="form-control-file" id="review_images" name="review_images[]" 
						   accept="image/*" multiple>
					<small class="form-text text-muted">{{ __('Max 3 images, Max 2MB each') }}</small>
					<div id="image-limit-message" class="alert alert-warning mt-2" style="display: none;">
						<i class="fas fa-exclamation-triangle"></i> Maximum 3 images allowed.
					</div>
					
					<div id="new-images-preview" class="mt-3" style="display: none;">
						<label>{{ __('Image Preview') }}:</label>
						<div class="row mt-2" id="preview-container"></div>
					</div>
				</div>
				
				<div class="form-group">
					<label for="admin_reply">{{ __('Admin Reply') }}</label>
					<textarea class="form-control" id="admin_reply" name="admin_reply" rows="3" 
							  placeholder="{{ __('Admin reply (optional)') }}">{{ old('admin_reply') }}</textarea>
					<small class="form-text text-muted">{{ __('Leave empty if no reply needed') }}</small>
				</div>
				
				<div class="form-group">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-save"></i> {{ __('Create Review') }}
					</button>
					<a href="{{ route('admin.review.index') }}" class="btn btn-secondary">
						<i class="fas fa-times"></i> {{ __('Cancel') }}
					</a>
				</div>
			</form>
		</div>
	</div>

</div>
<!-- End of Main Content -->

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;
    let isSearching = false;
    
    // Handle file input change
    $('#review_images').on('change', function() {
        const files = Array.from(this.files);
        
        if (files.length > 3) {
            $('#image-limit-message').show();
            this.value = '';
            return;
        } else {
            $('#image-limit-message').hide();
        }
        
        // Show preview of new images
        if (files.length > 0) {
            showNewImagePreview(files);
        }
    });
    
    // Product search functionality
    $('#product_search').on('input', function() {
        const query = $(this).val();
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (query.length >= 1) {
                searchProducts(query);
            } else if (query.length === 0) {
                // Show last 6 products when search is empty
                searchProducts('');
            }
        }, 200);
    });
    
    // Show products on focus
    $('#product_search').on('focus', function() {
        console.log('Product field focused');
        if ($(this).val().length === 0) {
            searchProducts('');
        } else {
            // If there's already text, search for it
            searchProducts($(this).val());
        }
    });
    
    // Show products on click
    $('#product_search').on('click', function() {
        console.log('Product field clicked');
        if ($(this).val().length === 0) {
            searchProducts('');
        } else {
            searchProducts($(this).val());
        }
    });
    
    // Hide search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#product_search, #product_search_results').length) {
            $('#product_search_results').hide();
        }
    });
    
    function searchProducts(query) {
        // Prevent multiple simultaneous searches
        if (isSearching) {
            return;
        }
        
        console.log('Searching for products with query:', query);
        console.log('URL:', '{{ route("admin.review.search-products") }}');
        
        isSearching = true;
        
        $.ajax({
            url: '{{ route("admin.review.search-products") }}',
            method: 'GET',
            data: { q: query },
            beforeSend: function() {
                $('#product_search_results').html('<div class="dropdown-item text-muted">Loading...</div>').show();
            },
            success: function(response) {
                console.log('Search successful. Query:', query, 'Products found:', response.products.length);
                console.log('Response:', response);
                displaySearchResults(response.products);
            },
            error: function(xhr, status, error) {
                console.log('Error searching products:', error);
                console.log('Status:', status);
                console.log('Response:', xhr.responseText);
                $('#product_search_results').html('<div class="dropdown-item text-danger">Error loading products: ' + error + '</div>').show();
            },
            complete: function() {
                isSearching = false;
            }
        });
    }
    
    function displaySearchResults(products) {
        const resultsContainer = $('#product_search_results');
        resultsContainer.empty();
        
        if (products.length === 0) {
            resultsContainer.html('<div class="dropdown-item text-muted">No products found</div>');
        } else {
            products.forEach(function(product) {
                const item = $(`
                    <div class="dropdown-item product-search-item" data-id="${product.id}" style="cursor: pointer; border-bottom: 1px solid #eee; padding: 8px 12px;">
                        <div class="d-flex align-items-center">
                            <img src="${product.thumbnail}" alt="${product.name}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px; flex-shrink: 0;" onerror="this.src='{{ asset('assets/images/noimage.png') }}'">
                            <div style="flex: 1; min-width: 0;">
                                <div class="font-weight-bold" style="font-size: 14px; margin-bottom: 2px;">${product.name}</div>
                                <small class="text-muted" style="font-size: 12px;">ID: ${product.id}</small>
                            </div>
                        </div>
                    </div>
                `);
                resultsContainer.append(item);
            });
        }
        
        resultsContainer.show();
    }
    
    // Handle product selection
    $(document).on('click', '.product-search-item', function() {
        const productId = $(this).data('id');
        const productName = $(this).find('.font-weight-bold').text();
        
        $('#item_id').val(productId);
        $('#product_search').val(productName);
        $('#product_search_results').hide();
    });
    
    function showNewImagePreview(files) {
        const previewContainer = $('#preview-container');
        previewContainer.empty();
        
        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewHtml = `
                        <div class="col-md-4 mb-3">
                            <div class="image-container position-relative d-inline-block">
                                <img src="${e.target.result}" class="img-thumbnail" 
                                     style="width: 120px; height: 120px; object-fit: cover; border: 2px solid #28a745;" 
                                     alt="New Image ${index + 1}">
                                <span class="badge badge-success position-absolute" 
                                      style="top: -8px; right: -8px;">New</span>
                            </div>
                        </div>
                    `;
                    previewContainer.append(previewHtml);
                };
                reader.readAsDataURL(file);
            }
        });
        
        $('#new-images-preview').show();
    }
});
</script>
@endsection

@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class=" mb-0"><b>{{ __('Edit Review') }}</b></h3>
                <a href="{{ route('admin.review.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Reviews') }}
                </a>
            </div>
        </div>
    </div>

	<!-- Edit Form -->
	<div class="card shadow mb-4">
		<div class="card-body">
			@include('alerts.alerts')
			
			<form action="{{ route('admin.review.update', $review->id) }}" method="POST" enctype="multipart/form-data">
				@csrf
				@method('PUT')
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="customer_name">{{ __('Customer Name') }} *</label>
							<input type="text" class="form-control" id="customer_name" name="customer_name" 
								   value="{{ old('customer_name', $review->customer_name) }}" required>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="customer_phone">{{ __('Phone Number') }} *</label>
							<input type="text" class="form-control" id="customer_phone" name="customer_phone" 
								   value="{{ old('customer_phone', $review->customer_phone) }}" required>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="rating">{{ __('Rating') }} *</label>
							<select class="form-control" id="rating" name="rating" required>
								<option value="1" {{ old('rating', $review->rating) == 1 ? 'selected' : '' }}>1 Star</option>
								<option value="2" {{ old('rating', $review->rating) == 2 ? 'selected' : '' }}>2 Stars</option>
								<option value="3" {{ old('rating', $review->rating) == 3 ? 'selected' : '' }}>3 Stars</option>
								<option value="4" {{ old('rating', $review->rating) == 4 ? 'selected' : '' }}>4 Stars</option>
								<option value="5" {{ old('rating', $review->rating) == 5 ? 'selected' : '' }}>5 Stars</option>
							</select>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="status">{{ __('Status') }} *</label>
							<select class="form-control" id="status" name="status" required>
								<option value="pending" {{ old('status', $review->status) == 'pending' ? 'selected' : '' }}>Pending</option>
								<option value="approved" {{ old('status', $review->status) == 'approved' ? 'selected' : '' }}>Approved</option>
								<option value="rejected" {{ old('status', $review->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
							</select>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label for="review_text">{{ __('Review Text') }}</label>
					<textarea class="form-control" id="review_text" name="review_text" rows="4" 
							  placeholder="{{ __('Write your review here...') }}">{{ old('review_text', $review->review_text) }}</textarea>
				</div>
				
				<div class="form-group">
					<label for="review_images">{{ __('Review Images') }}</label>
					<input type="file" class="form-control-file" id="review_images" name="review_images[]" 
						   accept="image/*" multiple>
					<small class="form-text text-muted">{{ __('Max 3 images, Max 2MB each. Leave empty to keep current images.') }}</small>
					<div id="image-limit-message" class="alert alert-warning mt-2" style="display: none;">
						<i class="fas fa-exclamation-triangle"></i> Maximum 3 images allowed. Remove some images to add new ones.
					</div>
					
					@php
						$reviewImages = $review->getReviewImages();
					@endphp
					@if(!empty($reviewImages))
						<div class="mt-3">
							<label>{{ __('Current Images') }}:</label>
							<div class="row mt-2">
								@foreach($reviewImages as $index => $image)
									<div class="col-md-4 mb-3">
										<div class="image-container position-relative d-inline-block">
											<img src="{{ asset($image) }}" class="img-thumbnail" 
												 style="width: 120px; height: 120px; object-fit: cover; border: 2px solid #ddd;" 
												 alt="Review Image {{ $index + 1 }}">
											<button type="button" class="btn btn-sm btn-danger remove-image-btn" 
													style="position: absolute; top: -8px; right: -8px; width: 24px; height: 24px; border-radius: 50%; padding: 0; font-size: 14px; line-height: 1;" 
													onclick="removeImage({{ $index }})" title="Remove Image">
												<i class="fas fa-times"></i>
											</button>
										</div>
									</div>
								@endforeach
							</div>
						</div>
					@endif
					
					<div id="new-images-preview" class="mt-3" style="display: none;">
						<label>{{ __('New Images Preview') }}:</label>
						<div class="row mt-2" id="preview-container"></div>
					</div>
				</div>
				
				<div class="form-group">
					<label for="admin_reply">{{ __('Admin Reply') }}</label>
					<textarea class="form-control" id="admin_reply" name="admin_reply" rows="4" 
							  placeholder="{{ __('Admin reply to this review') }}">{{ old('admin_reply', $review->admin_reply) }}</textarea>
					<small class="form-text text-muted">{{ __('Leave empty if no reply needed') }}</small>
				</div>
				
				<div class="form-group">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-save"></i> {{ __('Update Review') }}
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
let removedImageIndices = [];

$(document).ready(function() {
    // Handle file input change
    $('#review_images').on('change', function() {
        const files = Array.from(this.files);
        const currentImages = {{ count($reviewImages ?? []) }};
        const remainingImages = currentImages - removedImageIndices.length;
        const totalImages = remainingImages + files.length;
        
        if (totalImages > 3) {
            $('#image-limit-message').show();
            this.value = ''; // Clear the input
            return;
        } else {
            $('#image-limit-message').hide();
        }
        
        // Show preview of new images
        if (files.length > 0) {
            showNewImagePreview(files);
        }
    });
});

function removeImage(index) {
    if (confirm('Are you sure you want to remove this image?')) {
        // Add to removed indices
        if (!removedImageIndices.includes(index)) {
            removedImageIndices.push(index);
        }
        
        // Send AJAX request to remove image
        $.ajax({
            url: '{{ route("admin.review.remove-image", $review->id) }}',
            method: 'POST',
            data: {
                image_index: index,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload(); // Reload page to show updated images
                } else {
                    alert('Error removing image: ' + response.message);
                }
            },
            error: function() {
                alert('Error removing image. Please try again.');
            }
        });
    }
}

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
</script>
@endsection

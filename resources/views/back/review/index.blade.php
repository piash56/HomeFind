@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class=" mb-0"><b>{{ __('Product Reviews') }}</b></h3>
                <a href="{{ route('admin.review.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> {{ __('Add Review') }}
                </a>
            </div>
        </div>
    </div>

	<!-- DataTales -->
	<div class="card shadow mb-4">
		<div class="card-body">
			@include('alerts.alerts')
			<div class="gd-responsive-table">
				<table class="table table-bordered table-striped" id="admin-table" width="100%" cellspacing="0">

					<thead>
						<tr>
							<th>{{ __('Customer Name') }}</th>
							<th>{{ __('Phone') }}</th>
							<th>{{ __('Order ID') }}</th>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Rating') }}</th>
                            <th>{{ __('Status') }}</th>
							<th>{{ __('Actions') }}</th>
						</tr>
					</thead>

					<tbody>
                        @include('back.review.table',compact('reviews'))
					</tbody>

				</table>
			</div>
		</div>
	</div>

</div>

</div>
<!-- End of Main Content -->


{{-- DELETE MODAL --}}

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="confirm-deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

		<!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{ __('Confirm Delete?') }}</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
		</div>

		<!-- Modal Body -->
        <div class="modal-body">
			{{ __('You are going to delete this review. All contents related with this review will be lost.') }} {{ __('Do you want to delete it?') }}
		</div>

		<!-- Modal footer -->
        <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
			<form action="" class="d-inline btn-ok" method="POST">

                @csrf

                @method('DELETE')

                <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>

			</form>
		</div>

      </div>
    </div>
  </div>

{{-- DELETE MODAL ENDS --}}

{{-- ADMIN REPLY MODAL --}}
<div class="modal fade" id="admin-reply-modal" tabindex="-1" role="dialog" aria-labelledby="adminReplyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminReplyModalLabel">{{ __('Reply to Review') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="admin-reply-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __('Customer') }}:</label>
                                <input type="text" class="form-control" id="reply-customer-name" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __('Customer Review') }}:</label>
                                <textarea class="form-control" id="reply-review-text" rows="3" readonly></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="admin-reply-text">{{ __('Admin Reply') }} *</label>
                                <textarea class="form-control" id="admin-reply-text" name="admin_reply" rows="4" 
                                          placeholder="{{ __('Write your reply here...') }}" required></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="reply-review-id" name="review_id">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success" id="submit-admin-reply">
                        <span class="btn-text">{{ __('Submit Reply') }}</span>
                        <span class="spinner-border spinner-border-sm" style="display: none;"></span>
                    </button>
                </div>
            </form>
            
            <div id="reply-message" class="alert" style="display: none; margin: 0 15px 15px 15px;"></div>
        </div>
    </div>
</div>
{{-- ADMIN REPLY MODAL ENDS --}}

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Handle reply button click
    $(document).on('click', '.reply-btn', function() {
        const reviewId = $(this).data('review-id');
        const reviewText = $(this).data('review-text');
        const customerName = $(this).data('customer-name');
        const adminReply = $(this).data('admin-reply');
        
        // Populate modal fields
        $('#reply-review-id').val(reviewId);
        $('#reply-customer-name').val(customerName);
        $('#reply-review-text').val(reviewText || 'No review text provided');
        $('#admin-reply-text').val(adminReply || '');
        
        // Show modal
        $('#admin-reply-modal').modal('show');
    });
    
    // Handle admin reply form submission
    $('#admin-reply-form').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submit-admin-reply');
        const btnText = submitBtn.find('.btn-text');
        const spinner = submitBtn.find('.spinner-border');
        
        // Show loading state
        btnText.hide();
        spinner.show();
        submitBtn.prop('disabled', true);
        
        const formData = {
            review_id: $('#reply-review-id').val(),
            admin_reply: $('#admin-reply-text').val(),
            _token: $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
        };
        
        $.ajax({
            url: '{{ route("admin.review.reply") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showMessage('success', response.message);
                    $('#admin-reply-modal').modal('hide');
                    // Reload page to show updated data
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showMessage('danger', response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showMessage('danger', message);
            },
            complete: function() {
                // Hide loading state
                btnText.show();
                spinner.hide();
                submitBtn.prop('disabled', false);
            }
        });
    });
    
    function showMessage(type, text) {
        const messageDiv = $('#reply-message');
        messageDiv.removeClass('alert-success alert-danger alert-warning alert-info')
                  .addClass('alert-' + type)
                  .text(text)
                  .show();
        
        // Hide message after 5 seconds
        setTimeout(function() {
            messageDiv.hide();
        }, 5000);
    }
});
</script>
@endsection

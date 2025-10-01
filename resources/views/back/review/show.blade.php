@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class=" mb-0"><b>{{ __('Review Details') }}</b></h3>
                <a href="{{ route('admin.review.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Reviews') }}
                </a>
            </div>
        </div>
    </div>

	<!-- Review Details -->
	<div class="card shadow mb-4">
		<div class="card-body">
			@include('alerts.alerts')
			
			<div class="row">
				<div class="col-md-8">
					<table class="table table-borderless">
						<tr>
							<th width="200">{{ __('Customer Name') }}:</th>
							<td>{{ $review->customer_name }}</td>
						</tr>
						<tr>
							<th>{{ __('Phone') }}:</th>
							<td>{{ $review->customer_phone }}</td>
						</tr>
						<tr>
							<th>{{ __('Order ID') }}:</th>
							<td>{{ $review->order->transaction_number ?? 'N/A' }}</td>
						</tr>
						<tr>
							<th>{{ __('Product') }}:</th>
							<td>
								<a href="{{ route('front.product', $review->item->slug) }}" target="_blank">
									{{ $review->item->name }}
								</a>
							</td>
						</tr>
						<tr>
							<th>{{ __('Rating') }}:</th>
							<td>
								@for($i = 1; $i <= 5; $i++)
									@if($i <= $review->rating)
										<i class="fas fa-star text-warning"></i>
									@else
										<i class="far fa-star text-muted"></i>
									@endif
								@endfor
								<span class="ml-2">{{ $review->rating }}/5</span>
							</td>
						</tr>
						<tr>
							<th>{{ __('Status') }}:</th>
							<td>
								<span class="badge badge-{{ $review->status == 'approved' ? 'success' : ($review->status == 'rejected' ? 'danger' : 'warning') }}">
									{{ ucfirst($review->status) }}
								</span>
							</td>
						</tr>
						<tr>
							<th>{{ __('Review Date') }}:</th>
							<td>{{ $review->created_at->format('M d, Y H:i') }}</td>
						</tr>
						@if($review->review_text)
						<tr>
							<th>{{ __('Review Text') }}:</th>
							<td>
								<div class="border p-3 rounded bg-light">
									{{ $review->review_text }}
								</div>
							</td>
						</tr>
						@endif
					</table>
				</div>
				
				<div class="col-md-4">
					@php
						$reviewImages = $review->getReviewImages();
					@endphp
					@if(!empty($reviewImages))
					<div class="card">
						<div class="card-header">
							<h6>{{ __('Review Images') }}</h6>
						</div>
						<div class="card-body">
							<div class="row">
								@foreach($reviewImages as $index => $image)
									<div class="col-md-6 mb-2">
										<img src="{{ asset($image) }}" class="img-fluid rounded" alt="Review Image {{ $index + 1 }}" style="max-height: 150px; object-fit: cover;">
									</div>
								@endforeach
							</div>
						</div>
					</div>
					@endif
					
					@if($review->admin_reply)
					<div class="card mt-3">
						<div class="card-header">
							<h6>{{ __('Home Find Reply') }}</h6>
						</div>
						<div class="card-body">
							<p class="mb-0">{{ $review->admin_reply }}</p>
							@if($review->admin_reply_date)
								<small class="text-muted">Replied on: {{ $review->admin_reply_date->format('M d, Y H:i') }}</small>
							@endif
						</div>
					</div>
					@endif
					
					<div class="card mt-3">
						<div class="card-header">
							<h6>{{ __('Actions') }}</h6>
						</div>
						<div class="card-body">
							<a href="{{ route('admin.review.edit', $review->id) }}" class="btn btn-primary btn-sm btn-block">
								<i class="fas fa-edit"></i> {{ __('Edit Review') }}
							</a>
							
							@if($review->status == 'pending')
								<a href="{{ route('admin.review.update', $review->id) }}" class="btn btn-success btn-sm btn-block"
								   onclick="event.preventDefault(); document.getElementById('approve-form').submit();">
									<i class="fas fa-check"></i> {{ __('Approve') }}
								</a>
								<form id="approve-form" action="{{ route('admin.review.update', $review->id) }}" method="POST" style="display: none;">
									@csrf
									@method('PUT')
									<input type="hidden" name="status" value="approved">
									<input type="hidden" name="customer_name" value="{{ $review->customer_name }}">
									<input type="hidden" name="customer_phone" value="{{ $review->customer_phone }}">
									<input type="hidden" name="rating" value="{{ $review->rating }}">
									<input type="hidden" name="review_text" value="{{ $review->review_text }}">
								</form>
								
								<a href="{{ route('admin.review.update', $review->id) }}" class="btn btn-danger btn-sm btn-block"
								   onclick="event.preventDefault(); document.getElementById('reject-form').submit();">
									<i class="fas fa-times"></i> {{ __('Reject') }}
								</a>
								<form id="reject-form" action="{{ route('admin.review.update', $review->id) }}" method="POST" style="display: none;">
									@csrf
									@method('PUT')
									<input type="hidden" name="status" value="rejected">
									<input type="hidden" name="customer_name" value="{{ $review->customer_name }}">
									<input type="hidden" name="customer_phone" value="{{ $review->customer_phone }}">
									<input type="hidden" name="rating" value="{{ $review->rating }}">
									<input type="hidden" name="review_text" value="{{ $review->review_text }}">
								</form>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
<!-- End of Main Content -->

@endsection
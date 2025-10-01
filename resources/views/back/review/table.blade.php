
@foreach($reviews as $review)
<tr>
    <td>
        {{ $review->customer_name }}
    </td>
    <td>
        {{ $review->customer_phone }}
    </td>
    <td>
        {{ $review->order->transaction_number ?? 'N/A' }}
    </td>
    <td>
        <a href="{{route('front.product',$review->item->slug)}}">{{ $review->item->name }}</a>
    </td>
    <td>
        @for($i = 1; $i <= 5; $i++)
            @if($i <= $review->rating)
                <i class="fas fa-star text-warning"></i>
            @else
                <i class="far fa-star text-muted"></i>
            @endif
        @endfor
        ({{ $review->rating }}/5)
    </td>
    <td>
        <span class="badge badge-{{ $review->status == 'approved' ? 'success' : ($review->status == 'rejected' ? 'danger' : 'warning') }}">
            {{ ucfirst($review->status) }}
        </span>
    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($review->is_admin_added)
                                <span class="badge badge-info mr-2" title="{{ __('Added by Admin') }}">
                                    <i class="fas fa-user-shield"></i> Admin
                                </span>
                            @endif
                            <div class="action-list">
                                <a class="btn btn-secondary btn-sm "
                                    href="{{ route('admin.review.show',$review->id) }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-primary btn-sm "
                                    href="{{ route('admin.review.edit',$review->id) }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a class="btn btn-success btn-sm reply-btn" 
                                    href="javascript:;" 
                                    data-review-id="{{ $review->id }}"
                                    data-review-text="{{ $review->review_text }}"
                                    data-customer-name="{{ $review->customer_name }}"
                                    data-admin-reply="{{ $review->admin_reply }}"
                                    title="{{ __('Reply to Review') }}">
                                    <i class="fas fa-reply"></i>
                                </a>
                                <a class="btn btn-danger btn-sm " data-toggle="modal"
                                    data-target="#confirm-delete" href="javascript:;"
                                    data-href="{{ route('admin.review.destroy',$review->id) }}">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                    </td>
</tr>
@endforeach

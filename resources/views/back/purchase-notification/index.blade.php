@extends('master.back')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Purchase Notifications') }}</b></h3>
                <a class="btn btn-primary btn-sm" href="{{route('back.purchase-notification.create')}}"><i class="fas fa-plus"></i> {{ __('Add') }}</a>
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
                            <th>{{ __('Time') }}</th>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Sort Order') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                        <tr>
                            <td>{{ $notification->customer_name }}</td>
                            <td>{{ $notification->time_text }}</td>
                            <td>
                                @if($notification->item)
                                    <a href="{{ route('front.product', $notification->item->slug) }}" target="_blank">
                                        {{ Str::limit($notification->item->name, 40) }}
                                    </a>
                                @else
                                    <span class="text-muted">{{ __('Product Deleted') }}</span>
                                @endif
                            </td>
                            <td>{{ $notification->sort_order }}</td>
                            <td>
                                <select class="form-control form-control-sm status-change" data-id="{{ $notification->id }}">
                                    <option value="1" {{ $notification->status ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="0" {{ !$notification->status ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                            </td>
                            <td>
                                <div class="action-list">
                                    <a class="btn btn-primary btn-sm" href="{{ route('back.purchase-notification.edit', $notification->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" href="javascript:;" data-href="{{ route('back.purchase-notification.destroy', $notification->id) }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('No Data Found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Popup Interval Settings -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Popup Display Settings') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('back.purchase-notification.update-interval') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="popup_interval">{{ __('Popup Display Time (milliseconds)') }}</label>
                            <input type="number" class="form-control" name="purchase_popup_interval" id="popup_interval" 
                                value="{{ $popupInterval ?? 2000 }}" min="1000" step="500" required>
                            <small class="form-text text-muted">
                                {{ __('How long each notification stays visible. 1000 = 1 second, 5000 = 5 seconds') }}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="break_interval">{{ __('Break Time (milliseconds)') }}</label>
                            <input type="number" class="form-control" name="purchase_popup_break_interval" id="break_interval" 
                                value="{{ $breakInterval ?? 2000 }}" min="1000" step="500" required>
                            <small class="form-text text-muted">
                                {{ __('Time between notifications (break time). 1000 = 1 second, 5000 = 5 seconds') }}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">{{ __('Update') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DELETE MODAL --}}
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="confirm-deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirm-deleteModalLabel">{{ __('Confirm Delete?') }}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">{{ __('You are going to delete this Purchase Notification. All data related to this Purchase Notification will be lost.') }} <strong>{{ __('You want to delete this?') }}</strong></div>
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

@endsection

@section('scripts')
<script>
    $(document).on('change', '.status-change', function() {
        var id = $(this).data('id');
        var status = $(this).val();
        $.ajax({
            url: "{{ url('admin/purchase-notification/status') }}/" + id + "/" + status,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    location.reload();
                }
            }
        });
    });
</script>
@endsection

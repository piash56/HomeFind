@extends('master.back')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Edit Purchase Notification') }}</b></h3>
                <a class="btn btn-primary btn-sm" href="{{route('back.purchase-notification.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <form class="admin-form" action="{{ route('back.purchase-notification.update', $purchaseNotification->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                @include('alerts.alerts')

                                <div class="form-group">
                                    <label for="customer_name">{{ __('Customer Name') }} *</label>
                                    <input type="text" name="customer_name" class="form-control" id="customer_name"
                                        placeholder="{{ __('Enter Customer Name') }}" value="{{ old('customer_name', $purchaseNotification->customer_name) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="minutes_ago">{{ __('Minutes Ago') }} *</label>
                                    <input type="number" name="minutes_ago" class="form-control" id="minutes_ago"
                                        placeholder="{{ __('Enter Minutes Ago (e.g., 12)') }}" value="{{ old('minutes_ago', $purchaseNotification->minutes_ago) }}" min="0" max="999" required>
                                    <small class="form-text text-muted">{{ __('How many minutes ago the purchase was made') }}</small>
                                </div>

                                <div class="form-group">
                                    <label for="item_id">{{ __('Product') }} *</label>
                                    <select name="item_id" class="form-control" id="item_id" required>
                                        <option value="">{{ __('Select Product') }}</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" {{ old('item_id', $purchaseNotification->item_id) == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="sort_order">{{ __('Sort Order') }}</label>
                                    <input type="number" name="sort_order" class="form-control" id="sort_order"
                                        placeholder="{{ __('Enter Sort Order') }}" value="{{ old('sort_order', $purchaseNotification->sort_order) }}" min="0">
                                    <small class="form-text text-muted">{{ __('Lower numbers appear first') }}</small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="status" id="status" value="1" {{ old('status', $purchaseNotification->status) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status">{{ __('Active') }}</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-secondary">{{ __('Update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('master.back')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ __('General Settings') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('back.setting.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Site Title') }}</label>
                                        <input type="text" class="form-control" name="title" value="{{ $setting->title }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Tagline') }}</label>
                                        <input type="text" class="form-control" name="home_page_title" value="{{ $setting->home_page_title }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Logo') }}</label>
                                        <div class="mb-2">
                                            <img src="{{ $setting->logo ? asset('storage/images/' . $setting->logo) : asset('storage/images/placeholder.png') }}" alt="logo" style="height:50px;">
                                        </div>
                                        <input type="file" class="form-control" name="logo" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Favicon') }}</label>
                                        <div class="mb-2">
                                            <img src="{{ $setting->favicon ? asset('storage/images/' . $setting->favicon) : asset('storage/images/placeholder.png') }}" alt="favicon" style="height:32px;">
                                        </div>
                                        <input type="file" class="form-control" name="favicon" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



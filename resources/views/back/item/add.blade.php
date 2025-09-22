@extends('master.back')

@section('content')

<div class="container-fluid">

<!-- Page Heading -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h3 class="mb-0 bc-title"><b>{{ __('Add Product') }}</b> </h3>
        </div>
    </div>
</div>

<!-- Direct redirect to physical product creation -->
<script>
    window.location.replace("{{ route('back.item.create') }}");
</script>

</div>

@endsection

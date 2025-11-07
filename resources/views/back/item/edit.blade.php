@extends('master.back')

@section('content')

<div class="container-fluid">

<!-- Page Heading -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h3 class="mb-0 bc-title"><b>{{ __('Update Product') }}</b> </h3>
            <a class="btn btn-primary   btn-sm" href="{{route('back.item.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
            @include('alerts.alerts')
    </div>
</div>
<!-- Nested Row within Card Body -->

<form class="admin-form" action="{{ route('back.item.update',['item' => $item->id]) }}" method="POST"
    enctype="multipart/form-data">

    @csrf

    @method('PUT')
    <div class="row">

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('Name') }} *</label>
                        <input type="text" name="name" class="form-control item-name"
                            id="name"
                            placeholder="{{ __('Enter Name') }}"
                            value="{{ $item->name }}" >
                    </div>

                    <div class="form-group">
                        <label for="slug">{{ __('Slug') }} *</label>
                        <input type="text" name="slug" class="form-control"
                            id="slug"
                            placeholder="{{ __('Enter Slug') }}"
                            value="{{ $item->slug }}" >
                    </div>

                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group pb-0  mb-0">
                        <label class="d-block">{{ __('Featured Image') }} *</label>
                    </div>
                    <div class="form-group pb-0 pt-0 mt-0 mb-0">
                    <img class="admin-img lg" src="{{ $item->photo ? asset('storage/images/'.$item->photo) : asset('storage/images/placeholder.png') }}" >
                    </div>
                    <div class="form-group position-relative ">
                        <label class="file">
                            <input type="file"  accept="image/*"   class="upload-photo" name="photo"
                                id="file"  aria-label="File browser example">
                            <span
                                class="file-custom text-left">{{ __('Upload Image...') }}</span>
                        </label>
                        <br>
                        <span class="mt-1 text-info">{{ __('Image Size Should Be 800 x 800. or square size') }}</span>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group pb-0  mb-0">
                        <label>{{ __('Gallery Images') }} </label>
                    </div>
                    <div class="form-group pb-0 pt-0 mt-0 mb-0">
                        <div id="gallery-images">
                            <div class="d-block gallery_image_view">

                                @forelse($item->galleries as $gallery)
                                    <div class="single-g-item d-inline-block m-2">
                                            <span data-toggle="modal"
                                            data-target="#confirm-delete" href="javascript:;"
                                            data-href="{{ route('back.item.gallery.delete',$gallery->id) }}" class="remove-gallery-img">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                            <a class="popup-link" href="{{ $gallery->photo ? asset('storage/images/'.$gallery->photo) : asset('storage/images/placeholder.png') }}">
                                                <img class="admin-gallery-img" src="{{ $gallery->photo ? asset('storage/images/'.$gallery->photo) : asset('storage/images/placeholder.png') }}"
                                                    alt="No Image Found">
                                            </a>
                                    </div>
                                @empty
                                    <h6><b>{{ __('No Images Added') }}</b></h6>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="form-group position-relative ">
                        <label class="file">
                            <input type="file"  accept="image/*"   name="galleries[]" id="gallery_file"
                                    aria-label="File browser example" accept="image/*" multiple>
                            <span
                                class="file-custom text-left">{{ __('Upload Image...') }}</span>
                        </label>
                        <br>
                        <span class="mt-1 text-info">{{ __('Image Size Should Be 800 x 800. or square size') }}</span>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="sort_details">{{ __('Short Description') }} *</label>
                        <textarea name="sort_details" id="sort_details"
                            class="form-control"
                            placeholder="{{ __('Short Description') }}"
                            >{{$item->sort_details}}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="details">{{ __('Description') }} *</label>
                        <textarea name="details" id="details"
                            class="form-control text-editor"
                            rows="6"
                            placeholder="{{ __('Enter Description') }}"
                            >{{$item->details}}</textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="tags">{{ __('Product Tags') }}
                            </label>
                        <input type="text" name="tags" class="tags"
                            id="tags"
                            placeholder="{{ __('Tags') }}"
                            value="{{$item->tags}}">
                    </div>
                    <div class="form-group">
                        <label class="switch-primary">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_specification" value="1" {{$item->is_specification ==1 ? 'checked' : ''}}>
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Specifications') }}</span>
                        </label>
                    </div>

                    <div id="specifications-section" class="{{ $item->is_specification == 0 ? 'd-none' : '' }}">
                        @if(!empty($specification_name))
                        @foreach(array_combine($specification_name,$specification_description) as  $name => $description)
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_name[]"
                                        placeholder="{{ __('Specification Name') }}" value="{{$name}}">
                                    </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_description[]"
                                        placeholder="{{ __('Specification description') }}" value="{{$description}}">
                                    </div>
                            </div>
                            <div class="flex-btn">
                                @if($loop->first)
                                <button type="button" class="btn btn-success add-specification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}"> <i class="fa fa-plus"></i> </button>
                                @else
                                <button type="button" class="btn btn-danger remove-spcification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}"> <i class="fa fa-minus"></i> </button>
                                @endif
                            </div>
                        </div>

                        @endforeach
                        @else
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_name[]"
                                        placeholder="{{ __('Specification Name') }}" value="">
                                    </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_description[]"
                                        placeholder="{{ __('Specification description') }}" value="">
                                    </div>
                            </div>
                            <div class="flex-btn">
                                <button type="button" class="btn btn-success add-specification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}"> <i class="fa fa-plus"></i> </button>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_keywords">{{ __('Meta Keywords') }}
                            </label>
                        <input type="text" name="meta_keywords" class="tags"
                            id="meta_keywords"
                            placeholder="{{ __('Enter Meta Keywords') }}"
                            value="{{ $item->meta_keywords }}">
                    </div>
                    <div class="form-group">
                        <label
                            for="meta_description">{{ __('Meta Description') }}
                            </label>
                        <textarea name="meta_description" id="meta_description"
                            class="form-control" rows="5"
                            placeholder="{{ __('Enter Meta Description') }}">{{ $item->meta_description }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <input type="hidden" class="check_button" name="is_button" value="0">
                    <button type="submit" class="btn btn-secondary mr-2">{{ __('Update') }}</button>
                    <a class="btn btn-success" href="{{ route('back.attribute.index',$item->id) }}">{{ __('Manage Attributes') }}</a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="discount_price">{{ __('Current Price') }}
                            *</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text">{{ $curr->sign }}</span>
                            </div>
                            <input type="text" id="discount_price"
                                name="discount_price" class="form-control"
                                placeholder="{{ __('Enter Current Price') }}"
                                min="1" step="0.1"
                                value="{{ $item->discount_price }}" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="previous_price">{{ __('Previous Price') }}
                            </label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text">{{ $curr->sign }}</span>
                            </div>
                            <input type="text" id="previous_price"
                                name="previous_price" class="form-control"
                                placeholder="{{ __('Enter Previous Price') }}"
                                min="1" step="0.1"
                                value="{{ $item->previous_price }}" >
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bulk Pricing Section -->
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-2">
                        <label class="switch-primary">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" name="enable_bulk_pricing" id="enable_bulk_pricing" value="1" {{ $item->enable_bulk_pricing ? 'checked' : '' }}>
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Enable Bulk Pricing') }}</span>
                        </label>
                    </div>
                    
                    <div id="bulk-pricing-section" style="display: {{ $item->enable_bulk_pricing ? 'block' : 'none' }};">
                        <p class="text-muted small mb-3">{{ __('Set different prices for different quantities. Customers will see bulk options instead of quantity selector.') }}</p>
                        
                        <div id="bulk-pricing-items">
                            @php
                                $bulkPricingData = $item->getBulkPricingData();
                            @endphp
                            
                            @if(!empty($bulkPricingData))
                                @foreach($bulkPricingData as $index => $tier)
                                    <div class="bulk-pricing-item border rounded p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group mb-2">
                                                    <label>{{ __('Quantity') }}</label>
                                                    <input type="number" class="form-control" name="bulk_quantity[]" placeholder="2" min="1" value="{{ $tier['quantity'] ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-2">
                                                    <label>{{ __('Price') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">{{ $curr->sign }}</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="bulk_price[]" placeholder="0.00" min="0" step="0.01" value="{{ $tier['price'] ?? '' }}" style="max-width: none;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-bulk-pricing mt-3">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="bulk-pricing-item border rounded p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-2">
                                                <label>{{ __('Quantity') }}</label>
                                                <input type="number" class="form-control" name="bulk_quantity[]" placeholder="2" min="1" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label>{{ __('Price') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">{{ $curr->sign }}</span>
                                                    </div>
                                                    <input type="text" class="form-control" name="bulk_price[]" placeholder="0.00" min="0" step="0.01" value="" style="max-width: none;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <button type="button" class="btn btn-danger btn-sm remove-bulk-pricing mt-3" disabled>
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <button type="button" class="btn btn-success btn-sm" id="add-bulk-pricing">
                            <i class="fa fa-plus"></i> {{ __('Add Bulk Tier') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="category_id">{{ __('Select Category') }} *</label>
                        <select name="category_id" id="category_id" class="form-control" >
                            @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                            <option value="{{ $cat->id }}" {{ $cat->id == $item->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="brand_id">{{ __('Select Brand') }} </label>
                        <select name="brand_id" id="brand_id" class="form-control" >
                            <option value="" selected>{{__('Select Brand')}}</option>
                            @foreach(DB::table('brands')->whereStatus(1)->get() as $brand)
                            <option value="{{ $brand->id }}" {{$brand->id == $item->brand_id ? 'selected' : ''}} >{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="related_products_search">{{ __('Related Products') }}</label>
                        <small class="d-block mb-2 text-muted">{{ __('Select products to show in "আপনি পছন্দ করতে পারেন" section. Leave empty to show products from same category.') }}</small>
                        <div class="position-relative">
                            <input type="text" class="form-control" id="related_products_search" 
                                   placeholder="{{ __('Search and select products') }}" autocomplete="off">
                            <div id="related_products_search_results" class="dropdown-menu w-100" style="display: none; position: absolute; top: 100%; z-index: 1000; max-height: 300px; overflow-y: auto; background: white; border: 1px solid #ddd;">
                                <!-- Search results will appear here -->
                            </div>
                        </div>
                        <div id="selected_related_products" class="mt-3" style="min-height: 50px;">
                            @php
                                $relatedProducts = $item->related_products ? json_decode($item->related_products, true) : [];
                            @endphp
                            @if(!empty($relatedProducts))
                                @foreach($relatedProducts as $relatedId)
                                    @php
                                        $relatedItem = \App\Models\Item::find($relatedId);
                                    @endphp
                                    @if($relatedItem)
                                        <div class="selected-product-item mb-2 p-2 border rounded d-flex align-items-center justify-content-between" data-product-id="{{ $relatedItem->id }}">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('storage/images/' . ($relatedItem->thumbnail ?: $relatedItem->photo)) }}" alt="{{ $relatedItem->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                                                <span>{{ $relatedItem->name }}</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger remove-related-product" data-product-id="{{ $relatedItem->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <input type="hidden" name="related_products[]" value="{{ $relatedItem->id }}">
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="stock">{{ __('Total in stock') }}
                            *</label>
                        <div class="input-group mb-3">
                            <input type="number" id="stock"
                                name="stock" class="form-control"
                                placeholder="{{ __('Total in stock') }}" value="{{$item->stock}}" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sku">{{ __('SKU') }} *</label>
                        <input type="text" name="sku" class="form-control"
                            id="sku" placeholder="{{ __('Enter SKU') }}"
                            value="{{$item->sku}}" >
                    </div>
                    <div class="form-group">
                        <label for="video">{{ __('Video Link') }} </label>
                        <input type="text" name="video" class="form-control"
                            id="video" placeholder="{{ __('Enter Video Link') }}"
                            value="{{$item->video}}" >
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>
</div>
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
			{{ __('You are going to delete this image from gallery.') }} {{ __('Do you want to delete it?') }}
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

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Toggle bulk pricing section
    $('#enable_bulk_pricing').change(function() {
        if ($(this).is(':checked')) {
            $('#bulk-pricing-section').slideDown();
        } else {
            $('#bulk-pricing-section').slideUp();
        }
    });

    // Add bulk pricing tier
    $('#add-bulk-pricing').click(function() {
        var currencySign = '{{ $curr->sign }}';
        var newItem = `
            <div class="bulk-pricing-item border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label>{{ __('Quantity') }}</label>
                            <input type="number" class="form-control" name="bulk_quantity[]" placeholder="3" min="1" value="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label>{{ __('Price') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">${currencySign}</span>
                                </div>
                                <input type="text" class="form-control" name="bulk_price[]" placeholder="0.00" min="0" step="0.01" value="" style="max-width: none;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <button type="button" class="btn btn-danger btn-sm remove-bulk-pricing mt-3">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#bulk-pricing-items').append(newItem);
        updateRemoveButtons();
    });

    // Remove bulk pricing tier
    $(document).on('click', '.remove-bulk-pricing', function() {
        $(this).closest('.bulk-pricing-item').remove();
        updateRemoveButtons();
    });

    // Update remove buttons state
    function updateRemoveButtons() {
        var itemCount = $('.bulk-pricing-item').length;
        if (itemCount === 1) {
            $('.remove-bulk-pricing').prop('disabled', true);
        } else {
            $('.remove-bulk-pricing').prop('disabled', false);
        }
    }

    // Initialize
    updateRemoveButtons();

    // Related Products Search Functionality
    var selectedProductIds = [];
    var isSearching = false;
    
    // Initialize selected products from existing data
    $('.selected-product-item').each(function() {
        var productId = $(this).data('product-id');
        if (productId) {
            selectedProductIds.push(productId.toString());
        }
    });
    
    // Product search input handler
    $('#related_products_search').on('input', function() {
        var query = $(this).val();
        if (query.length >= 2) {
            searchRelatedProducts(query);
        } else if (query.length === 0) {
            $('#related_products_search_results').hide();
        }
    });
    
    // Show products on focus
    $('#related_products_search').on('focus', function() {
        if ($(this).val().length === 0) {
            searchRelatedProducts('');
        } else {
            searchRelatedProducts($(this).val());
        }
    });
    
    // Hide search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#related_products_search, #related_products_search_results').length) {
            $('#related_products_search_results').hide();
        }
    });
    
    function searchRelatedProducts(query) {
        if (isSearching) {
            return;
        }
        
        isSearching = true;
        
        $.ajax({
            url: '{{ route("admin.review.search-products") }}',
            method: 'GET',
            data: { q: query },
            beforeSend: function() {
                $('#related_products_search_results').html('<div class="dropdown-item text-muted">Loading...</div>').show();
            },
            success: function(response) {
                displayRelatedProductResults(response.products);
            },
            error: function(xhr, status, error) {
                $('#related_products_search_results').html('<div class="dropdown-item text-danger">Error loading products: ' + error + '</div>').show();
            },
            complete: function() {
                isSearching = false;
            }
        });
    }
    
    function displayRelatedProductResults(products) {
        var html = '';
        if (products.length === 0) {
            html = '<div class="dropdown-item text-muted">No products found</div>';
        } else {
            products.forEach(function(product) {
                if (selectedProductIds.indexOf(product.id.toString()) === -1) {
                    var thumbnail = product.thumbnail || '{{ asset("storage/images/placeholder.png") }}';
                    html += '<div class="dropdown-item related-product-item" data-product-id="' + product.id + '" style="cursor: pointer; padding: 10px;">';
                    html += '<div class="d-flex align-items-center">';
                    html += '<img src="' + thumbnail + '" alt="' + product.name + '" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px;">';
                    html += '<span>' + product.name + '</span>';
                    html += '</div>';
                    html += '</div>';
                }
            });
        }
        $('#related_products_search_results').html(html).show();
    }
    
    // Add product to selected list
    $(document).on('click', '.related-product-item', function() {
        var productId = $(this).data('product-id');
        var productName = $(this).find('span').text();
        var productThumbnail = $(this).find('img').attr('src');
        
        if (selectedProductIds.indexOf(productId.toString()) === -1) {
            selectedProductIds.push(productId.toString());
            
            var selectedHtml = '<div class="selected-product-item mb-2 p-2 border rounded d-flex align-items-center justify-content-between" data-product-id="' + productId + '">';
            selectedHtml += '<div class="d-flex align-items-center">';
            selectedHtml += '<img src="' + productThumbnail + '" alt="' + productName + '" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px;">';
            selectedHtml += '<span>' + productName + '</span>';
            selectedHtml += '</div>';
            selectedHtml += '<button type="button" class="btn btn-sm btn-danger remove-related-product" data-product-id="' + productId + '">';
            selectedHtml += '<i class="fas fa-times"></i>';
            selectedHtml += '</button>';
            selectedHtml += '<input type="hidden" name="related_products[]" value="' + productId + '">';
            selectedHtml += '</div>';
            
            $('#selected_related_products').append(selectedHtml);
            $('#related_products_search').val('');
            $('#related_products_search_results').hide();
        }
    });
    
    // Remove product from selected list
    $(document).on('click', '.remove-related-product', function() {
        var productId = $(this).data('product-id').toString();
        var index = selectedProductIds.indexOf(productId);
        if (index > -1) {
            selectedProductIds.splice(index, 1);
        }
        $(this).closest('.selected-product-item').remove();
    });
});
</script>
@endsection

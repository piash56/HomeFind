@extends('master.back')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h3 class="mb-0 bc-title"><b>{{ __('Edit Order') }}</b></h3>
                    <a class="btn btn-primary  btn-sm" href="{{ route('back.order.index') }}"><i
                            class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="row">

            <div class="col-xl-12 col-lg-12 col-md-12">

                <div class="card o-hidden border-0 shadow-lg">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row justify-content-center">
                            <div class="col-lg-10">
                                <div class="p-5">
                                    <form class="admin-form" action="{{ route('back.order.update', $order->id) }}"
                                        method="POST" enctype="multipart/form-data" id="orderEditForm">

                                        @csrf
                                        @include('alerts.alerts')

                                        <?php
                                        $billingInfo = json_decode($order->billing_info, true) ?: [];
                                        $shippingInfo = json_decode($order->shipping_info, true) ?: [];
                                        $cart = json_decode($order->cart, true) ?: [];
                                        $firstCartItem = !empty($cart) ? reset($cart) : null;
                                        $firstItemId = $firstCartItem ? array_key_first($cart) : null;
                                        ?>

                                        <!-- Order Basic Information -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="transaction_number">{{ __('Order ID') }} *</label>
                                                    <input type="text" name="transaction_number" class="form-control"
                                                        id="transaction_number" placeholder="{{ __('Enter Order ID') }}"
                                                        value="{{ $order->transaction_number }}" required>

                                                    @if ($firstItemId)
                                                        @php
                                                            $firstItem = \App\Models\Item::find($firstItemId);
                                                        @endphp
                                                        @if ($firstItem)
                                                            <small class="form-text text-muted mt-1">
                                                                {{ __('SKU') }}: <strong>#{{ $firstItem->sku }}</strong>
                                                            </small>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="order_date">{{ __('Order Date') }} *</label>
                                                    <input type="datetime-local" name="order_date" class="form-control"
                                                        id="order_date" value="{{ $order->created_at->format('Y-m-d\TH:i') }}" required>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer Information -->
                                        <h5 class="mb-3 mt-4">{{ __('Customer Information') }}</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bill_first_name">{{ __('Customer Name') }} *</label>
                                                    <input type="text" name="bill_first_name" class="form-control"
                                                        id="bill_first_name" placeholder="{{ __('Enter Customer Name') }}"
                                                        value="{{ $billingInfo['bill_first_name'] ?? '' }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bill_phone">{{ __('Customer Phone') }} *</label>
                                                    <input type="text" name="bill_phone" class="form-control"
                                                        id="bill_phone" placeholder="{{ __('Enter Customer Phone') }}"
                                                        value="{{ $billingInfo['bill_phone'] ?? '' }}" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bill_email">{{ __('Customer Email') }}</label>
                                                    <input type="email" name="bill_email" class="form-control"
                                                        id="bill_email" placeholder="{{ __('Enter Customer Email (Optional)') }}"
                                                        value="{{ $billingInfo['bill_email'] ?? '' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="bill_address1">{{ __('Customer Address') }} *</label>
                                                    <textarea name="bill_address1" class="form-control" id="bill_address1" 
                                                        placeholder="{{ __('Enter Customer Address') }}" rows="3" required>{{ $billingInfo['bill_address1'] ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Product Selection -->
                                        <h5 class="mb-3 mt-4">{{ __('Product Information') }}</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="product_search">{{ __('Product') }} *</label>
                                                    <div class="position-relative">
                                                        <input type="text" class="form-control" id="product_search" 
                                                               placeholder="{{ __('Search and select product') }}" autocomplete="off" required>
                                                        <input type="hidden" id="item_id" name="item_id" value="{{ $firstItemId }}" required>
                                                        <div id="product_search_results" class="dropdown-menu w-100" style="display: none; position: absolute; top: 100%; z-index: 1000; max-height: 300px; overflow-y: auto;">
                                                            <!-- Search results will appear here -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="product_price">{{ __('Product Price') }}</label>
                                                    <input type="number" step="0.01" class="form-control" id="product_price" 
                                                           readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Attributes Section -->
                                        <div class="row" id="attributes_section" style="display: none;">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>{{ __('Product Attributes') }}</label>
                                                    <div id="attributes_container">
                                                        <!-- Dynamic attributes will be loaded here -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quantity Section -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Quantity Options') }}</label>
                                                    <div id="quantity_section">
                                                        <!-- Dynamic quantity options will be loaded here -->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="total_price">{{ __('Total Price') }}</label>
                                                    <input type="number" step="0.01" class="form-control" id="total_price" 
                                                           readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hidden input for selected attributes -->
                                        <input type="hidden" id="selected_attributes" name="selected_attributes" value="">

                                        <div class="form-group text-center mt-4">
                                            <button type="submit" class="btn btn-primary btn-lg">{{ __('Update Order') }}</button>
                                            <a href="{{ route('back.order.index') }}" class="btn btn-secondary btn-lg ml-2">{{ __('Cancel') }}</a>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;
    let isSearching = false;
    let currentProduct = null;
    let currentQuantity = {{ $firstCartItem['qty'] ?? 1 }};
    let currentPrice = {{ $firstCartItem['main_price'] ?? 0 }};
    let currentAttributes = {};
    let currentAttributePrice = 0;
    
    // Initialize with current order data
    initializeOrderData();
    
    function initializeOrderData() {
        @if($firstCartItem && $firstItemId)
            // Load current product data
            loadProductData({{ $firstItemId }}, '{{ $firstCartItem['name'] ?? '' }}');
            
            // Load current attributes if any
            @if(isset($firstCartItem['attribute']) && !empty($firstCartItem['attribute']['option_name']))
                @php
                    $currentAttributes = [];
                    $attributeNames = $firstCartItem['attribute']['names'] ?? [];
                    $optionNames = $firstCartItem['attribute']['option_name'] ?? [];
                @endphp
                
                // Set current attributes from order data
                setTimeout(function() {
                    @foreach($attributeNames as $index => $attrName)
                        @if(isset($optionNames[$index]))
                            // Find and select the attribute option
                            $('.attribute-option').each(function() {
                                const $select = $(this);
                                const attributeName = $select.prev('label').text().trim();
                                if (attributeName === '{{ $attrName }}') {
                                    $select.find('option').each(function() {
                                        if ($(this).text().trim().includes('{{ $optionNames[$index] }}')) {
                                            $select.val($(this).val()).trigger('change');
                                            return false;
                                        }
                                    });
                                }
                            });
                        @endif
                    @endforeach
                }, 1000);
            @endif
        @endif
    }
    
    // Product search functionality
    $('#product_search').on('input', function() {
        const query = $(this).val();
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (query.length >= 1) {
                searchProducts(query);
            } else if (query.length === 0) {
                searchProducts('');
            }
        }, 200);
    });
    
    // Show products on focus
    $('#product_search').on('focus', function() {
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
        if (isSearching) {
            return;
        }
        
        isSearching = true;
        
        $.ajax({
            url: '{{ route("admin.review.search-products") }}',
            method: 'GET',
            data: { q: query },
            beforeSend: function() {
                $('#product_search_results').html('<div class="dropdown-item text-muted">Loading...</div>').show();
            },
            success: function(response) {
                displaySearchResults(response.products);
            },
            error: function(xhr, status, error) {
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
        
        loadProductData(productId, productName);
        $('#product_search_results').hide();
    });
    
    function loadProductData(productId, productName) {
        $.ajax({
            url: '{{ route("admin.order.get-product-data") }}',
            method: 'GET',
            data: { product_id: productId },
            success: function(response) {
                currentProduct = response.product;
                $('#item_id').val(productId);
                $('#product_search').val(productName);
                $('#product_price').val(response.product.discount_price);
                
                // Load attributes
                loadAttributes(response.product);
                
                // Load quantity options
                loadQuantityOptions(response.product);
                
                // Reset quantity to 1 when product changes
                currentQuantity = 1;
                updateTotalPrice();
            },
            error: function(xhr, status, error) {
                console.error('Error loading product data:', error);
            }
        });
    }
    
    function loadAttributes(product) {
        const attributesSection = $('#attributes_section');
        const attributesContainer = $('#attributes_container');
        
        if (product.attributes && product.attributes.length > 0) {
            attributesSection.show();
            attributesContainer.empty();
            
            let attributesHtml = '';
            product.attributes.forEach(function(attribute) {
                attributesHtml += `
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="attribute_${attribute.id}">${attribute.name}</label>
                            <select class="form-control attribute-option" id="attribute_${attribute.id}" 
                                    data-attribute-id="${attribute.id}">
                                <option value="">-- Select ${attribute.name} --</option>
            `;
                
                attribute.options.forEach(function(option) {
                    attributesHtml += `
                        <option value="${option.id}" data-price="${option.price}">${option.name}
                            ${option.price > 0 ? ' (+' + option.price + ')' : ''}
                        </option>
                    `;
                });
                
                attributesHtml += `
                            </select>
                        </div>
                    </div>
                `;
            });
            
            attributesContainer.html(attributesHtml);
            
            // Handle attribute selection
            $('.attribute-option').on('change', function() {
                const attributeId = $(this).data('attribute-id');
                const optionId = $(this).val();
                const optionPrice = parseFloat($(this).find(':selected').data('price')) || 0;
                
                if (optionId) {
                    currentAttributes[attributeId] = optionId;
                } else {
                    delete currentAttributes[attributeId];
                }
                
                updateAttributePrice();
                updateSelectedAttributes();
                updateTotalPrice();
            });
            
        } else {
            attributesSection.hide();
            currentAttributes = {};
            currentAttributePrice = 0;
            updateSelectedAttributes();
        }
    }
    
    function updateAttributePrice() {
        currentAttributePrice = 0;
        $('.attribute-option').each(function() {
            const optionPrice = parseFloat($(this).find(':selected').data('price')) || 0;
            currentAttributePrice += optionPrice;
        });
    }
    
    function updateSelectedAttributes() {
        $('#selected_attributes').val(JSON.stringify(currentAttributes));
    }
    
    function loadQuantityOptions(product) {
        const quantitySection = $('#quantity_section');
        quantitySection.empty();
        
        if (product.enable_bulk_pricing && product.bulk_pricing_data && product.bulk_pricing_data.length > 0) {
            // Show bulk pricing options
            let bulkOptions = '<div class="bulk-pricing-options">';
            bulkOptions += '<h6>{{ __("Bulk Pricing Options") }}</h6>';
            
            // Add single item option
            bulkOptions += `
                <div class="form-check mb-2">
                    <input class="form-check-input bulk-quantity-option" type="radio" name="quantity_option" id="qty_1" value="1" data-quantity="1" data-price="${product.discount_price}">
                    <label class="form-check-label" for="qty_1">
                        {{ __("Buy 1") }} - {{ __("৳") }}${product.discount_price}
                    </label>
                </div>
            `;
            
            // Add bulk options
            product.bulk_pricing_data.forEach(function(option) {
                const totalPrice = option.price;
                const pricePerUnit = (totalPrice / option.quantity).toFixed(2);
                bulkOptions += `
                    <div class="form-check mb-2">
                        <input class="form-check-input bulk-quantity-option" type="radio" name="quantity_option" id="qty_${option.quantity}" value="${option.quantity}" data-quantity="${option.quantity}" data-price="${pricePerUnit}" data-total="${totalPrice}">
                        <label class="form-check-label" for="qty_${option.quantity}">
                            {{ __("Buy") }} ${option.quantity} - {{ __("৳") }}${totalPrice} ({{ __("৳") }}${pricePerUnit} {{ __("each") }})
                        </label>
                    </div>
                `;
            });
            
            bulkOptions += '</div>';
            quantitySection.html(bulkOptions);
            
            // Select current quantity if it matches an option
            $(`input[name="quantity_option"][value="${currentQuantity}"]`).prop('checked', true);
            
        } else {
            // Show normal quantity input
            const normalQuantityHtml = `
                <div class="normal-quantity-input">
                    <label for="normal_quantity">{{ __("Quantity") }}</label>
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary" id="decrease_qty">-</button>
                        <input type="number" class="form-control text-center" id="normal_quantity" name="normal_quantity" value="${currentQuantity}" min="1" max="${product.stock}">
                        <button type="button" class="btn btn-outline-secondary" id="increase_qty">+</button>
                    </div>
                    <small class="text-muted">{{ __("Available Stock") }}: ${product.stock}</small>
                </div>
            `;
            quantitySection.html(normalQuantityHtml);
            
            // Handle quantity increase/decrease
            $('#decrease_qty').on('click', function() {
                const current = parseInt($('#normal_quantity').val());
                if (current > 1) {
                    $('#normal_quantity').val(current - 1);
                    currentQuantity = current - 1;
                    updateTotalPrice();
                }
            });
            
            $('#increase_qty').on('click', function() {
                const current = parseInt($('#normal_quantity').val());
                const max = parseInt($('#normal_quantity').attr('max'));
                if (current < max) {
                    $('#normal_quantity').val(current + 1);
                    currentQuantity = current + 1;
                    updateTotalPrice();
                }
            });
            
            $('#normal_quantity').on('change', function() {
                currentQuantity = parseInt($(this).val()) || 1;
                updateTotalPrice();
            });
        }
        
        // Handle bulk quantity selection
        $(document).on('change', '.bulk-quantity-option', function() {
            if ($(this).is(':checked')) {
                currentQuantity = parseInt($(this).data('quantity'));
                currentPrice = parseFloat($(this).data('price'));
                updateTotalPrice();
            }
        });
        
        updateTotalPrice();
    }
    
    function updateTotalPrice() {
        if (currentQuantity && currentPrice) {
            const total = currentQuantity * (currentPrice + currentAttributePrice);
            $('#total_price').val(total.toFixed(2));
        }
    }
});
</script>
@endsection

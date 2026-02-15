@extends('master.back')

@section('content')

<style>
/* Product image responsive styling */
.product-invoice-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    cursor: pointer;
    transition: all 0.3s ease;
}

.product-invoice-image:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.product-invoice-image-placeholder {
    width: 60px;
    height: 60px;
    background: #f5f5f5;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e0e0e0;
}

/* Make images 2x bigger on mobile */
@media (max-width: 768px) {
    .product-invoice-image {
        width: 120px;
        height: 120px;
    }
    
    .product-invoice-image-placeholder {
        width: 120px;
        height: 120px;
    }
    
    .product-invoice-image-placeholder i {
        font-size: 24px;
    }
}

/* Image Lightbox Modal */
.image-lightbox-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    animation: fadeIn 0.3s ease;
}

.image-lightbox-modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.lightbox-content {
    max-width: 90%;
    max-height: 90%;
    position: relative;
    animation: zoomIn 0.3s ease;
}

.lightbox-image {
    width: 100%;
    height: auto;
    max-height: 90vh;
    object-fit: contain;
    border-radius: 8px;
}

.lightbox-close-btn {
    position: absolute;
    top: -40px;
    right: 0;
    background: #fff;
    color: #333;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.lightbox-close-btn:hover {
    background: #f44336;
    color: #fff;
    transform: rotate(90deg);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes zoomIn {
    from { transform: scale(0.5); }
    to { transform: scale(1); }
}

@media (max-width: 768px) {
    .lightbox-close-btn {
        top: 10px;
        right: 10px;
        width: 50px;
        height: 50px;
        font-size: 28px;
    }
    
    .lightbox-content {
        max-width: 95%;
        max-height: 95%;
    }
}
</style>

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class=" mb-0">{{ __('Order Invoice') }} </h3>
                <div>
                    <a class="btn btn-primary btn-sm" href="{{route('back.order.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('back.order.print',$order->id) }}" target="_blank"><i class="fas fa-print"></i> {{ __('print') }}</a>
                </div>
                </div>
        </div>
    </div>
@php
    if($order->state){
        $state = json_decode($order->state,true);
    }else{
        $state = [];
    }
@endphp

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                            <div class="row">
                                <div class="col text-center">

                                <!-- Logo -->
                                <img class="img-fluid mb-5 mh-70" width="180" alt="Logo" src="{{asset('storage/images/'.$setting->logo)}}">

                            </div>
                            </div> <!-- / .row -->
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h2><b>{{__('Order Details :')}}</b></h2>
                                    <span class="text-muted">{{__('Order Id: ')}}</span>{{$order->transaction_number}}<br>
                                    <span class="text-muted">{{__('Order Date: ')}}</span>{{$order->created_at->format('M d, Y')}}<br>
                                    <span class="text-muted">{{__('Payment Method: ')}}</span>{{$order->payment_method }}<br>
                                </div>
                                <div class="col-12 col-md-6">
                                    <h2><b>{{__('Customer Information :')}}</b></h3>
                                    @php
                                        $bill = json_decode($order->billing_info,true);
                                    @endphp
                                    <span class="text-muted">{{__('Customer Name')}}: </span>{{$bill['bill_first_name']}} {{$bill['bill_last_name']}}<br>
                                    <span class="text-muted">{{__('Phone')}}: </span>{{$bill['bill_phone']}}<br>
                                    @if (isset($bill['bill_address1']))
                                    <span class="text-muted">{{__('Address')}}: </span>{{$bill['bill_address1']}}<br>
                                    @endif
                                </div>
                            </div>
                            @if(isset($bill['order_notes']) && !empty($bill['order_notes']))
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6 class="mb-2"><i class="fas fa-sticky-note"></i> <strong>{{__('Order Notes:')}}</strong></h6>
                                        <p class="mb-0">{{ $bill['order_notes'] }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-12">

                                <!-- Table -->
                                <div class="gd-responsive-table">
                                    <table class="table my-4">
                                    <thead>
                                        <tr>
                                        <th width="10%" class="px-0 bg-transparent border-top-0">
                                            <span class="h6">{{__('Image')}}</span>
                                        </th>
                                        <th width="30%" class="px-0 bg-transparent border-top-0">
                                            <span class="h6">{{__('Products')}}</span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0">
                                            <span class="h6">{{__('Attribute')}}</span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0 text-center">
                                            <span class="h6">{{__('Quantity')}}</span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0 text-right">
                                            <span class="h6">{{__('Net Price')}}</span>
                                        </th>
                                        <th class="px-0 bg-transparent border-top-0 text-right">
                                            <span class="h6">{{__('Total Price')}}</span>
                                        </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $option_price = 0;
                                            $total = 0;
                                        @endphp
                                    @foreach (json_decode($order->cart,true) as $itemKey => $item)
                                    @php
                                        // Use main_price directly as it already includes the final calculated price
                                        $netPrice = $item['main_price'];
                                        $itemTotal = $netPrice * $item['qty'];
                                        $total += $itemTotal;
                                        $grandSubtotal = $total;
                                        
                                        // Get product SKU
                                        $itemModel = \App\Models\Item::find($itemKey);
                                        $itemSku = $itemModel ? $itemModel->sku : null;
                                    @endphp
                                    <tr>
                                        <td class="px-0" style="vertical-align: middle;">
                                            @if(isset($item['photo']) && $item['photo'])
                                                <img src="{{asset('storage/images/' . $item['photo'])}}" 
                                                     alt="{{$item['name']}}" 
                                                     class="product-invoice-image"
                                                     onclick="openImageLightbox('{{asset('storage/images/' . $item['photo'])}}', '{{$item['name']}}')">
                                            @else
                                                <div class="product-invoice-image-placeholder">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-0" style="vertical-align: middle;">
                                            <strong>{{$item['name']}}</strong>
                                            @if($itemSku)
                                                <br><small class="text-muted">{{__('SKU')}}: #{{$itemSku}}</small>
                                            @endif
                                        </td>
                                        <td class="px-0" style="vertical-align: middle;">
                                            @if(isset($item['attribute']['option_name']) && $item['attribute']['option_name'])
                                            @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                                            <span class="d-block"><b>{{$item['attribute']['names'][$optionkey] ?? 'Option'}}:</b> {{$option_name}}</span>
                                            @endforeach
                                            @else
                                            --
                                            @endif
                                        </td>
                                        <td class="px-0 text-center" style="vertical-align: middle;">
                                            {{$item['qty']}}
                                        </td>
                                        <td class="px-0 text-right" style="vertical-align: middle;">
                                            @if ($setting->currency_direction == 1)
                                                {{$order->currency_sign}}{{round($netPrice,2)}}
                                            @else
                                                {{round($netPrice,2)}}{{$order->currency_sign}}
                                            @endif
                                        </td>
                                        <td class="px-0 text-right" style="vertical-align: middle;">
                                            @if ($setting->currency_direction == 1)
                                                {{$order->currency_sign}}{{round($itemTotal,2)}}
                                            @else
                                                {{round($itemTotal,2)}}{{$order->currency_sign}}
                                            @endif
                                        </td>
                                        </tr>
                                    @endforeach
                                        <tr>
                                        <td class="padding-top-2x" colspan="6">
                                        </td>
                                        </tr>
                                        {{-- Subtotal before discounts --}}
                                        <tr>
                                        <td class="px-0 border-top border-top-2" colspan="5">
                                        <strong class="text-muted">{{__('Subtotal')}}</strong>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2">
                                            <strong>
                                            @if ($setting->currency_direction == 1)
                                                {{$order->currency_sign}}{{round($grandSubtotal,2)}}
                                            @else
                                                {{round($grandSubtotal,2)}}{{$order->currency_sign}}
                                            @endif
                                            </strong>
                                        </td>
                                        </tr>
                                        @if($order->tax!=0)
                                        <tr>
                                        <td class="px-0 border-top border-top-2" colspan="5">
                                        <span class="text-muted">{{__('Tax')}}</span>
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2">
                                            <span>
                                            @if ($setting->currency_direction == 1)
                                                {{$order->currency_sign}}{{round($order->tax*$order->currency_value,2)}}
                                            @else
                                            {{round($order->tax*$order->currency_value,2)}}{{$order->currency_sign}}
                                            @endif
                                            </span>
                                        </td>
                                        </tr>
                                        @endif
                                        @if(json_decode($order->discount,true))
                                        @php
                                            $discount = json_decode($order->discount,true);
                                        @endphp
                                        <tr>
                                        <td class="px-0" colspan="5">
                                        <span class="text-muted">{{__('Coupon discount')}} ({{$discount['code']['code_name']}})</span>
                                        </td>
                                        <td class="px-0 text-right">
                                            <span class="text-danger">
                                            @if ($setting->currency_direction == 1)
                                                -{{$order->currency_sign}}{{round($discount['discount'],2)}}
                                            @else
                                                -{{round($discount['discount'],2)}}{{$order->currency_sign}}
                                            @endif
                                            </span>
                                        </td>
                                        </tr>
                                        @endif
                                        @if(json_decode($order->shipping,true))
                                        @php
                                            $shipping = json_decode($order->shipping,true);
                                            // Determine delivery area label from shipping title
                                            $deliveryLabel = __('Shipping');
                                            if (isset($shipping['title'])) {
                                                if (stripos($shipping['title'], 'Inside Dhaka') !== false) {
                                                    $deliveryLabel = __('Delivery Fee') . ' (' . __('Inside Dhaka') . ')';
                                                } elseif (stripos($shipping['title'], 'Outside Dhaka') !== false) {
                                                    $deliveryLabel = __('Delivery Fee') . ' (' . __('Outside Dhaka') . ')';
                                                } else {
                                                    $deliveryLabel = __('Delivery Fee');
                                                }
                                            }
                                        @endphp
                                        <tr>
                                        <td class="px-0" colspan="5">
                                        <span class="text-muted">{{ $deliveryLabel }}</span>
                                        </td>
                                        <td class="px-0 text-right">
                                            <span >
                                            @if ($setting->currency_direction == 1)
                                                {{$order->currency_sign}}{{round($shipping['price'],2)}}
                                            @else
                                                {{round($shipping['price'],2)}}{{$order->currency_sign}}
                                            @endif

                                            </span>
                                        </td>
                                        </tr>
                                        @endif
                                        @if(json_decode($order->state_price,true))
                                        <tr>
                                        <td class="px-0" colspan="5">
                                        <span class="text-muted">{{__('State Tax')}}</span>
                                        </td>
                                        <td class="px-0 text-right">
                                            <span >
                                            @if ($setting->currency_direction == 1)
                                            {{isset($state['type']) && $state['type'] == 'percentage' ?  ' ('.$state['price'].'%) ' : ''}}  {{$order->currency_sign}}{{round($order['state_price']*$order->currency_value,2)}}
                                            @else
                                            {{isset($state['type']) &&  $state['type'] == 'percentage' ?  ' ('.$state['price'].'%) ' : ''}}  {{round($order['state_price']*$order->currency_value,2)}}{{$order->currency_sign}}
                                            @endif

                                            </span>
                                        </td>
                                        </tr>
                                        @endif
                                        @if($order->delivery_cost_minus && $order->delivery_cost_minus > 0)
                                        <tr>
                                        <td class="px-0" colspan="5">
                                        <span class="text-muted">{{__('Delivery Cost Minus')}}</span>
                                        @if($order->order_status == 'Delivered')
                                        <button type="button" class="btn btn-sm btn-info ml-2" data-toggle="modal" data-target="#editDeliveryCostModal">
                                            <i class="fas fa-edit"></i> {{ __('Edit') }}
                                        </button>
                                        @endif
                                        </td>
                                        <td class="px-0 text-right">
                                            <span class="text-danger">
                                            @if ($setting->currency_direction == 1)
                                                -{{$order->currency_sign}}{{PriceHelper::testPrice($order->delivery_cost_minus)}}
                                            @else
                                                -{{PriceHelper::testPrice($order->delivery_cost_minus)}}{{$order->currency_sign}}
                                            @endif
                                            </span>
                                        </td>
                                        </tr>
                                        @elseif($order->order_status == 'Delivered')
                                        <tr>
                                        <td class="px-0" colspan="5">
                                        <span class="text-muted">{{__('Delivery Cost Minus')}}</span>
                                        <button type="button" class="btn btn-sm btn-success ml-2" data-toggle="modal" data-target="#editDeliveryCostModal">
                                            <i class="fas fa-plus"></i> {{ __('Add') }}
                                        </button>
                                        </td>
                                        <td class="px-0 text-right">
                                            <span class="text-muted">{{ __('Not set') }}</span>
                                        </td>
                                        </tr>
                                        @endif
                                        <tr>
                                        <td class="px-0 border-top border-top-2" colspan="5">

                                        @if ($order->payment_method == 'Cash On Delivery')
                                        <strong>{{__('Total amount')}}</strong>
                                        @else
                                        <strong>{{__('Total amount due')}}</strong>
                                        @endif
                                        </td>
                                        <td class="px-0 text-right border-top border-top-2">
                                            <span class="h3">
                                                @if ($setting->currency_direction == 1)
                                                {{$order->currency_sign}}{{PriceHelper::OrderTotal($order)}}
                                                @else
                                                {{PriceHelper::OrderTotal($order)}}{{$order->currency_sign}}
                                                @endif
                                            </span>
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </div>
                                </div>
                            </div> <!-- / .row -->
                    </div>
                </div>
            </div>
        </div>


</div>

{{-- EDIT DELIVERY COST MINUS MODAL --}}
@if($order->order_status == 'Delivered')
<div class="modal fade" id="editDeliveryCostModal" tabindex="-1" role="dialog" aria-labelledby="editDeliveryCostModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('back.order.update.delivery.cost', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="editDeliveryCostModalLabel">
                        {{ $order->delivery_cost_minus > 0 ? __('Edit Delivery Cost Minus') : __('Add Delivery Cost Minus') }}
                    </h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="form-group">
                        <label for="delivery_cost_minus_edit">{{ __('Delivery Cost Minus') }}</label>
                        <input type="number" step="0.01" min="0" class="form-control" 
                               id="delivery_cost_minus_edit" 
                               name="delivery_cost_minus" 
                               value="{{ $order->delivery_cost_minus ?? 0 }}"
                               placeholder="{{ __('Enter amount to deduct from order total') }}" 
                               required>
                        <small class="form-text text-muted">
                            {{ __('This amount will be deducted from the order total. Current total: ') }}
                            @if ($setting->currency_direction == 1)
                                {{$order->currency_sign}}{{PriceHelper::OrderTotal($order)}}
                            @else
                                {{PriceHelper::OrderTotal($order)}}{{$order->currency_sign}}
                            @endif
                        </small>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        {{ $order->delivery_cost_minus > 0 ? __('Update') : __('Add') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
{{-- EDIT DELIVERY COST MINUS MODAL ENDS --}}

{{-- IMAGE LIGHTBOX MODAL --}}
<div id="imageLightboxModal" class="image-lightbox-modal" onclick="closeImageLightbox(event)">
    <div class="lightbox-content">
        <button class="lightbox-close-btn" onclick="closeImageLightbox(event)">
            <i class="fas fa-times"></i>
        </button>
        <img id="lightboxImage" src="" alt="" class="lightbox-image">
    </div>
</div>
{{-- IMAGE LIGHTBOX MODAL ENDS --}}

<script>
function openImageLightbox(imageSrc, imageAlt) {
    const modal = document.getElementById('imageLightboxModal');
    const lightboxImage = document.getElementById('lightboxImage');
    
    lightboxImage.src = imageSrc;
    lightboxImage.alt = imageAlt;
    modal.classList.add('active');
    
    // Prevent body scroll when modal is open
    document.body.style.overflow = 'hidden';
}

function closeImageLightbox(event) {
    // Close only if clicking on the modal backdrop or close button
    if (event.target.id === 'imageLightboxModal' || 
        event.target.classList.contains('lightbox-close-btn') || 
        event.target.classList.contains('fa-times')) {
        
        const modal = document.getElementById('imageLightboxModal');
        modal.classList.remove('active');
        
        // Restore body scroll
        document.body.style.overflow = 'auto';
    }
}

// Close modal on Escape key press
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('imageLightboxModal');
        if (modal.classList.contains('active')) {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    }
});
</script>

@endsection

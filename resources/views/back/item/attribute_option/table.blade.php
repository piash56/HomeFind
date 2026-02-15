@foreach($datas as $data)
    <tr>
        <td>
            <div class="d-flex align-items-center">
                @if($data->image)
                    <img src="{{ asset('storage/images/' . $data->image) }}" alt="{{ $data->name }}" style="max-width: 40px; max-height: 40px; border: 1px solid #ddd; padding: 2px; border-radius: 4px; margin-right: 10px;">
                @elseif($data->color_code)
                    <span style="display: inline-block; width: 40px; height: 40px; background-color: {{ $data->color_code }}; border: 1px solid #ddd; border-radius: 4px; margin-right: 10px;"></span>
                @endif
                <span>{{ $data->name }}</span>
            </div>
        </td>
        <td>
            {{ $data->attribute }}
        </td>
        <td>
            @if($data->previous_price > 0 && $data->price > 0)
                {{-- Has both old and new price = REPLACEMENT mode --}}
                <span class="badge badge-primary badge-sm mb-1">{{ __('Replace') }}</span><br>
                <small><del class="text-muted">{{ $curr->sign }}{{ PriceHelper::setPrice($data->previous_price) }}</del></small><br>
                <strong>{{ $curr->sign }}{{ PriceHelper::setPrice($data->price) }}</strong>
            @elseif($data->price > 0)
                {{-- Only has new price = ADDITION mode --}}
                <span class="badge badge-success badge-sm mb-1">{{ __('Add') }}</span><br>
                <strong>+{{ $curr->sign }}{{ PriceHelper::setPrice($data->price) }}</strong>
            @else
                {{-- No price = Use main product price --}}
                <span class="badge badge-secondary badge-sm">{{ __('Main Price') }}</span>
            @endif
        </td>
        <td class="{{$data->stock < 10 && $data->stock != 'unlimited' ? 'bg-danger text-white'  :''}} ">
            @if ($data->stock == '0')
            {{__('Out of Stock')}}
            @else
            {{$data->stock}}
            @endif
        </td>
        <td>
            <div class="action-list">
                <a class="btn btn-secondary btn-sm "
                    href="{{ route('back.option.edit',[$item->id, $data->id]) }}">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>
                <a class="btn btn-danger btn-sm " data-toggle="modal"
                    data-target="#confirm-delete" href="javascript:;"
                    data-href="{{ route('back.option.destroy',[$item->id, $data->id]) }}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        </td>
    </tr>
@endforeach

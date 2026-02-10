@foreach($datas as $data)
    <tr>
        <td>
            {{ $data->title }}
        </td>
        <td>
            {{ $data->code_name }}
        </td>
        <td>
            @if($data->product_id && $data->product)
                <span class="badge badge-info">{{ $data->product->name }}</span>
            @else
                <span class="text-muted">{{ __('All Products') }}</span>
            @endif
        </td>
        <td>
            @if ($data->type == 'amount')
            {{ PriceHelper::adminCurrencyPrice($data->discount) }}
            @else
            {{ $data->discount }} %
            @endif
        </td>
        <td>
            @if($data->start_date || $data->end_date)
                <small>
                    @if($data->start_date)
                        <b>{{ __('From') }}:</b> {{ \Carbon\Carbon::parse($data->start_date)->format('M d, Y') }}<br>
                    @endif
                    @if($data->end_date)
                        <b>{{ __('To') }}:</b> {{ \Carbon\Carbon::parse($data->end_date)->format('M d, Y') }}
                    @endif
                </small>
            @else
                <span class="text-muted">{{ __('No limit') }}</span>
            @endif
        </td>
        <td>
            {{ $data->no_of_times }}
        </td>
        <td>

            <div class="dropdown">
                <button class="btn btn-{{  $data->status == 1 ? 'success' : 'danger'  }} btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  {{  $data->status == 1 ? __('Enabled') : __('Disabled')  }}
                </button>
                <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item" href="{{ route('back.code.status',[$data->id,1]) }}">{{ __('Enable') }}</a>
                  <a class="dropdown-item" href="{{ route('back.code.status',[$data->id,0]) }}">{{ __('Disable') }}</a>
                </div>
              </div>

            </div>

        </td>
        <td>
            <div class="action-list">
                <a class="btn btn-secondary btn-sm "
                    href="{{ route('back.code.edit',[$data->id]) }}">
                    <i class="fas fa-edit"></i>
                </a>
                <a class="btn btn-danger btn-sm " data-toggle="modal"
                    data-target="#confirm-delete" href="javascript:;"
                    data-href="{{ route('back.code.destroy',[$data->id]) }}">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        </td>
    </tr>
@endforeach

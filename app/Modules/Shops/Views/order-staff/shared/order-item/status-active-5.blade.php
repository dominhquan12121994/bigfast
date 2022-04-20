<table class="table table-responsive table-striped" id="table_orders">
    <thead>
    <tr>
        @if($statusActive && !isset($search['searchs']))
            <th class="pr-0"><input type="checkbox" id="selectall"/></th>
        @endif
        <th>STT</th>
        <th style="min-width: 130px">Nhân viên giao</th>
        <th style="min-width: 210px">Mã đơn</th>
        <th style="min-width: 260px; width: 100%;">Nơi gửi</th>
        <th class="text-right" style="min-width: 120px">Phí giao(vnđ)</th>
        <th class="text-right" style="min-width: 135px">Phí khác(vnđ)<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Bao gồm phí bảo hiểm, phí thu hộ"></i></th>
        <th class="text-right" style="min-width: 135px">Thu hộ(vnđ)<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Tổng tiền thu hộ"></i></th>
        <th class="text-right" style="min-width: 140px">Tổng thu(vnđ)<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Tổng tiền phải thu"></i></th>
        <th width="160px"></th>
    </tr>
    </thead>
    <tbody>
        @foreach($orders as $key => $order)
            @if (!isset(OrderConstant::status[$order->status])) @continue; @endif
            @if (!isset(OrderConstant::status[$order->status]['detail'][$order->status_detail])) @continue; @endif

            <tr>
                {{--@if($shop && $statusActive && !isset($search['searchs']))--}}
                @if($statusActive && !isset($search['searchs']))
                    <td class="pr-0"><input type="checkbox" class="singlechkbox" name="cbx_order_id[]" value="{{ $order->id }}"/></td>
                @endif
                <td>{{ $key + 1 }}</td>
                <td>
                    {!! isset($staffsInfo[$order->id]) ? $staffsInfo[$order->id] : '' !!}
                </td>
                <td>
                    {{ $order->lading_code }}<br>
                    @php
                        $status_detail = OrderConstant::status[$order->status]['detail'][$order->status_detail];
                    @endphp
                    <span class="badge badge-{{ isset($status_detail['color']) ? $status_detail['color'] : 'warning' }}">
                        {{ OrderConstant::status[$order->status]['detail'][$order->status_detail]['name'] }}
                    </span><br>
                    <span class="font-sm">{{ $order->extra->client_code }}</span>
                </td>
                <td>
                    {{ $order->sender->name }}<br>
                    {{ $order->sender->phone }} - {{ $order->sender->provinces->name }}<br>
                    <i>Thuộc Shop: {!! $order->shop->name !!}</i>
                </td>
                <td class="text-right">
                    @include('Shops::order-staff.shared.money.shipping_fee', compact('order', 'incurred_fee'))
                </td>
                <td class="text-right">@include('Shops::order-staff.shared.money.other_fee', compact('order', 'incurred_fee'))</td>
                <td class="text-right">@include('Shops::order-staff.shared.money.cod', compact('order', 'incurred_fee'))</td>
                <td class="text-right">@include('Shops::order-staff.shared.money.collect', compact('order', 'incurred_fee'))</td>
                <td class="box-actions">
                    @include('Shops::order-staff.shared.button-actions')
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

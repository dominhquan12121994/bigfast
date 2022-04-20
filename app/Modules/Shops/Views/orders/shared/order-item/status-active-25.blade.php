<table class="table table-responsive table-striped" id="table_orders">
    <thead>
    <tr>
        @if($statusActive && !isset($search['searchs']))
            <th class="pr-0"><input type="checkbox" id="selectall"/></th>
        @endif
        <th>STT</th>
        <th style="min-width: 210px">Mã đơn</th>
        <th style="min-width: 135px">Lưu kho</th>
        <th style="min-width: 260px; width: 100%;">Nơi nhận</th>
        <th style="min-width: 260px">Nơi gửi</th>
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
                    {{ $order->lading_code }}<br>
                    @php
                        $status_detail = OrderConstant::status[$order->status]['detail'][$order->status_detail];
                    @endphp
                    <span class="badge badge-{{ isset($status_detail['color']) ? $status_detail['color'] : 'warning' }}">
                        {{ OrderConstant::status[$order->status]['detail'][$order->status_detail]['name'] }}
                    </span><br>
                    <span class="font-sm">{{ $order->extra->client_code }}</span>
                </td>
                <td>{{ $storeDay[(int)$order->id] ?? 0 }} ngày</td>
                <td>
                    {{ $order->receiver->name }} - {{ $order->receiver->phone }}<br>
                    {{ $order->receiver->address }} <i style="color:red" data-toggle="tooltip" data-placement="bottom" title="{{ $order->receiver->wards->name }}, {{ $order->receiver->districts->name }}, {{ $order->receiver->provinces->name }}" class="cil-library"></i><br>
                    <i class="font-sm">Ngày tạo: {{ date('d-m-Y H:i', strtotime($order->created_at)) }}</i>
                </td>
                <td>
                    {{ $order->sender->name }}<br>
                    {{ $order->sender->phone }} - {{ $order->sender->provinces->name }}<br>
                    <i>Thuộc Shop: {!! $order->shop->name !!}</i>
                </td>
                <td class="text-right">
                    @include('Shops::orders.shared.money.shipping_fee', compact('order', 'incurred_fee'))
                </td>
                <td class="text-right">@include('Shops::orders.shared.money.other_fee', compact('order', 'incurred_fee'))</td>
                <td class="text-right">@include('Shops::orders.shared.money.cod', compact('order', 'incurred_fee'))</td>
                <td class="text-right">@include('Shops::orders.shared.money.collect', compact('order', 'incurred_fee'))</td>
                <td class="box-actions">
                    @include('Shops::orders.shared.button-actions')
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

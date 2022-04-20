@php
    $statusShipper = array();
    $user_roles = $currentUser->getRoleNames()->toArray();
    if (in_array('pickup', $user_roles)) $statusShipper += \App\Modules\Orders\Constants\OrderConstant::statusShipperPickup;
    if (in_array('shipper', $user_roles)) $statusShipper += \App\Modules\Orders\Constants\OrderConstant::statusShipperShip;
    if (in_array('refund', $user_roles)) $statusShipper += \App\Modules\Orders\Constants\OrderConstant::statusShipperRefund;
@endphp
<table class="table table-responsive table-striped" id="table_orders">
    <thead>
    <tr>
        @if($filter['status_detail'])
            @if(count($orders) > 0)
                @if( ( in_array($orders[0]->status_detail, array(12, 13, 23, 32)) || in_array($filter['status'], array(4)) ) && !empty($statusShipper[$filter['status']]['detail'][$filter['status_detail']]['next']) )
                    <th class="pr-0"><input type="checkbox" id="selectall"/></th>
                @else
                    <td class="pr-0"></td>
                @endif
            @endif
        @endif
        <th>STT</th>
        <th style="min-width: 210px">Mã đơn</th>
        <th style="min-width: 260px; width: 100%;">Nơi gửi</th>
        <th style="min-width: 260px">Bên nhận</th>
        <th class="text-right" style="min-width: 120px">Phí giao(vnđ)</th>
        <th class="text-right" style="min-width: 140px">Tổng phí(vnđ)</th>
        <th class="text-right" style="min-width: 135px">Thu hộ(vnđ)</th>
        <th class="text-right" style="min-width: 140px">Tổng thu(vnđ)</th>
    </tr>
    </thead>
    <tbody>
        @foreach($orders as $key => $order)

            <tr>
                @if($filter['status_detail'])
                    @if( ( in_array($orders[0]->status_detail, array(12, 13, 23, 32)) || in_array($filter['status'], array(4)) ) && !empty($statusShipper[$filter['status']]['detail'][$filter['status_detail']]['next']) )
                        <td class="pr-0"><input type="checkbox" class="singlechkbox" name="cbx_order_id[]" value="{{ $order->id }}"/></td>
                    @else
                        <td class="pr-0"></td>
                    @endif
                @endif
                <td>{{ $key + 1 }}</td>
                <td>
                    {{ $order->lading_code }}<br>
                    @php
                        $status_detail = OrderConstant::status[$order->status]['detail'][$order->status_detail];
                    @endphp
                    @if ( $filter['status'] === 2 && in_array('pickup', $user_roles))
                        <span class="badge badge-info">
                            {{ \App\Modules\Orders\Constants\OrderConstant::statusShipperPickup[2]['name'] }}
                        </span><br>
                    @elseif ( $filter['status'] === 5 && in_array('shipper', $user_roles))
                        <span class="badge badge-success">
                            {{ \App\Modules\Orders\Constants\OrderConstant::statusShipperShip[5]['name'] }}
                        </span><br>
                    @else
                        <span class="badge badge-{{ isset($status_detail['color']) ? $status_detail['color'] : 'warning' }}">
                            {{ OrderConstant::status[$order->status]['detail'][$order->status_detail]['name'] }}
                        </span><br>
                    @endif
                    <span class="font-sm">{{ $order->extra->client_code }}</span>
                </td>
                <td>
                    {{ $order->sender->name }}<br>
                    {{ $order->sender->phone }} - {{ $order->sender->provinces->name }}<br>
                    <i>Thuộc Shop: {!! $order->shop->name !!}</i>
                </td>
                <td>
                    {{ $order->receiver->name }} - {{ $order->receiver->phone }}<br>
                    {{ $order->receiver->address }} <i style="color:red" data-toggle="tooltip" data-placement="bottom" title="{{ $order->receiver->wards->name }}, {{ $order->receiver->districts->name }}, {{ $order->receiver->provinces->name }}" class="cil-library"></i><br>
                    <i class="font-sm">Ngày tạo: {{ date('d-m-Y H:i', strtotime($order->created_at)) }}</i>
                </td>
                <td class="text-right">
                    @include('Orders::orders.shared.money.shipping_fee', compact('order', 'incurred_fee'))
                </td>
                <td class="text-right">@include('Orders::orders.shared.money.total_fee', compact('order', 'incurred_fee'))</td>
                <td class="text-right">@include('Orders::orders.shared.money.cod', compact('order', 'incurred_fee'))</td>
                <td class="text-right">@include('Orders::orders.shared.money.collect', compact('order', 'incurred_fee'))</td>
            </tr>
        @endforeach
    </tbody>
</table>

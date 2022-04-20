@php
    $list_fee = array_merge( \App\Modules\Orders\Constants\OrderConstant::fee_types, \App\Modules\Orders\Constants\OrderConstant::order_fee_types );
@endphp
<table>
    <thead>
    <tr>
        <th>STT</th>
        <th>Ngày đối soát</th>
        <th>Mã vẫn đơn</th>
        <th>Mã đơn hàng riêng</th>
        <th>ID cửa hàng</th>
        <th>Tên cửa hàng</th>
        <th>Trạng thái</th>
        <th>Người gửi</th>
        <th>SĐT gửi</th>
        @foreach( $fee_type as $type )
            <th> {{ $list_fee[$type] }}  </th>
        @endforeach
        @if ( count($fee_type) >1 )
            <th>Tổng</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $stt => $order)
        @php
            $sum = 0;
        @endphp
        <tr>
            <td>{{ $stt + 1 }}</td>
            <td>{{ date('d-m-Y', strtotime($order->data->info->updated_at )) }}</td>
            <td>{{ $order->data->info->lading_code }}</td>
            <td>{{ $order->data->extra->client_code }}</td>
            <td>{{ $order->data->shop->id }}</td>
            <td>{!! $order->data->shop->name !!}</td>
            <td>{{ OrderConstant::status[$order->data->info->status]['detail'][$order->data->info->status_detail]['name'] }}</td>
            <td>{{ $order->data->sender->name }}</td>
            <td>{{ $order->data->sender->phone }}</td>
            @foreach( $fee_type as $type )
                @php
                    $value = null;
                    if ( $type === 'incurred_fee_transport' && $order->data->info->payfee === 'payfee_receiver' ) {
                        $value = null;
                    } else {
                        $value = $groupByOrder[$order->data->info->id]->filter(function ($value, $key) use ($type) {
                            return $value->fee_type == $type;
                        })->first();
                    }
                    $money = $value ? (int)$value->value : 0;
                    $sum+= $money;
                @endphp
                <td> {{ $money }} </td>
            @endforeach
            @if ( count($fee_type) >1 )
                <td>{{ $sum }}</td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>

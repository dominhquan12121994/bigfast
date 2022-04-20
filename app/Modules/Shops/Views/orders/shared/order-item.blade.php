<table class="table table-responsive table-striped" id="table_orders">
    <thead>
    <tr>
        @if($shop && $statusActive)
            <th class="pr-0"><input type="checkbox" id="selectall"/></th>
        @endif
        <th>STT</th>
        @if ( $staffType )
            <th style="min-width: 130px">{{ $staffType }}</th>
        @endif
        <th style="min-width: 210px">Mã đơn</th>
        <th style="min-width: 260px; width: 100%;">Nơi gửi</th>
        <th style="min-width: 260px">Bên nhận</th>
        <th width="120px" style="min-width: 135px">Phí khác<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Bao gồm phí bảo hiểm, phí thu hộ"></i></th>
        <th width="120px" style="min-width: 135px">Thu hộ<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Tổng tiền thu hộ"></i></th>
        <th width="120px" style="min-width: 140px">Phí giao hàng</th>
        <th width="120px" style="min-width: 135px">Tổng phí<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Bao gồm tất cả các loại phí (phí bảo hiểm, phí thu hộ, phí vận chuyển,...)"></i></th>
        <th width="160px"></th>
    </tr>
    </thead>
    <tbody>
        @foreach($orders as $key => $order)
            @php
                $incurred_total_cod = $incurred_fee[$order->id]['incurred_total_cod'] ?? 0;
                $incurred_fee_transport = $incurred_fee[$order->id]['incurred_fee_transport'] ?? 0;
                $incurred_fee_cod = $incurred_fee[$order->id]['incurred_fee_cod'] ?? 0;
            @endphp
            <tr>
                @if($shop && $statusActive)
                    <td class="pr-0"><input type="checkbox" class="singlechkbox" name="cbx_order_id[]" value="{{ $order->id }}"/></td>
                @endif
                <td>{{ $key + 1 }}</td>
                @if ( $staffType )
                    <td>
                        {!! isset($staffsInfo[$order->id]) ? $staffsInfo[$order->id] : '' !!}
                    </td>
                @endif
                <td>
                    {{ $order->lading_code }}<br>
                    @php
                        $status = $order->status;
                        $status_detail = $order->status_detail;
                        $status = ($status === 9) ? 5 : $status;
                        $status_detail = ($status_detail === 91) ? 51 : $status_detail;
                        $status_detail_style = OrderConstant::status[$status]['detail'][$status_detail];
                    @endphp
                    <span class="badge badge-{{ isset($status_detail_style['color']) ? $status_detail_style['color'] : 'warning' }}">
                        {{ OrderConstant::status[$status]['detail'][$status_detail]['name'] }}
                    </span><br>
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
                <td style="vertical-align: middle;">{{ number_format($order->total_fee - $order->transport_fee + $incurred_fee_cod ) . ' vnđ' }}</td>
                <td style="vertical-align: middle;">{{ number_format($order->cod + $incurred_total_cod ) . ' vnđ' }}</td>
                <td style="vertical-align: middle;">
                    <span class="badge badge-{{ array('payfee_sender' => 'danger', 'payfee_receiver' => 'info')[$order->payfee] }}">
                        {{ \App\Modules\Orders\Constants\OrderConstant::payfees[$order->payfee] }}
                    </span><br>
                    {{ number_format($order->transport_fee + $incurred_fee_transport ) . ' vnđ' }}
                </td>
                <td style="vertical-align: middle;">{{ number_format($order->total_fee + $incurred_fee_cod + $incurred_fee_transport ) . ' vnđ' }}</td>
                <td class="box-actions">
                    @include('Shops::orders.shared.button-actions')
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

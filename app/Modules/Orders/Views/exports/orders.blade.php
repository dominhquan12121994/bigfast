<table>
    <thead>
    <tr>
        <th>STT</th>
        <th>Mã đơn hàng</th>
        <th>ID cửa hàng</th>
        <th>Tên cửa hàng</th>
        <th>Trạng thái</th>
        <th>Người gửi</th>
        <th>SĐT gửi</th>
        <th>Địa chỉ gửi</th>
        <th>Người nhận</th>
        <th>SĐT nhận</th>
        <th>Địa chỉ nhận</th>
        @if (!empty($orders))
            @if ($orders[0]->data->shipper)
                <th>Người giao hàng</th>
                <th>Số điện thoại người giao hàng</th>
            @endif
        @endif
        <th>Gói dịch vụ</th>
        <th>Tổng phí dịch vụ</th>
        <th>Tiền COD</th>
        <th>Tuỳ chọn thanh toán</th>
        <th>Giá trị khai giá</th>
        <th>Khối lượng</th>
        <th>Rộng</th>
        <th>Dài</th>
        <th>Cao</th>
        <th>Mã đơn hàng riêng</th>
        <th>Tên hàng hoá</th>
        <th>Ghi chú thêm</th>
        <th>Ngày tạo đơn</th>
        <th>Ngày lấy dự kiến</th>
        <th>Ngày giao dự kiến</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $stt => $order)
        <tr>
            <td>{{ $stt + 1 }}</td>
            <td>{{ $order->data->info->lading_code }}</td>
            <td>{{ $order->data->shop->id }}</td>
            <td>{!! $order->data->shop->name !!}</td>
            <td>{{ OrderConstant::status[$order->data->info->status]['detail'][$order->data->info->status_detail]['name'] }}</td>
            <td>{{ $order->data->sender->name }}</td>
            <td>{{ $order->data->sender->phone }}</td>
            <td>{{ $order->data->sender->address }}</td>
            <td>{{ $order->data->receiver->name }}</td>
            <td>{{ $order->data->receiver->phone }}</td>
            <td>{{ $order->data->receiver->address }}</td>
            @if ($order->data->shipper)
                <td>{{ $order->data->shipper->name }}</td>
                <td>{{ $order->data->shipper->phone }}</td>
            @endif
            <td>{{ $order->data->info->service_type }}</td>
            <td>{{ $order->data->info->total_fee }}</td>
            <td>{{ $order->data->info->cod }}</td>
            <td>{{ OrderConstant::payfees[$order->data->info->payfee] }}</td>
            <td>{{ $order->data->info->insurance_value }}</td>
            <td>{{ $order->data->info->weight }}</td>
            <td>{{ $order->data->info->width }}</td>
            <td>{{ $order->data->info->length }}</td>
            <td>{{ $order->data->info->height }}</td>
            <td>{{ $order->data->extra->client_code }}</td>
            <td>
                @foreach($order->data->products as $product)
                    {!! str_replace('&', '&amp;', $product->name) !!} [{{ $product->quantity }} cái],
                @endforeach
            </td>
            <td>{{ OrderConstant::notes[$order->data->extra->note1] }}</td>
            <td>{{ date('d-m-Y H:i', strtotime($order->data->info->created_at)) }}</td>
            <td>{{ date('d-m-Y H:i', strtotime($order->data->extra->expect_pick)) }}</td>
            <td>{{ date('d-m-Y H:i', strtotime($order->data->extra->expect_receiver)) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

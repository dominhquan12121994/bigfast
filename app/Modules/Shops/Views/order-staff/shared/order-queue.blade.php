<div class="row m-0">
    <div class="col-auto mr-auto">
        <p>Tạo đơn vận chuyển theo <a href="{{ asset('excel/BigFast_MauDanhSachDonHang_2021.xlsx') }}" download>mẫu file excel</a>.<br>Cập nhật trạng thái đơn vận chuyển sau <span class="badge badge-warning" id="countdown"></span> giây</p>
    </div>
    <div class="col-auto form-inline">
        <select class="form-control form-control-sm mr-1" id="filter_limit" name="filter_limit">
            @foreach(config('options.page_limit') as $limit)
                <option value="{{ $limit }}" {{ $filter['limit'] == $limit ? 'selected="selected"' : '' }}>{{ $limit }}</option>
            @endforeach
        </select>
        <label for="filter_limit">kết quả một trang</label>
    </div>
</div>
<table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table_orders">
    <thead>
    <tr>
        <th>STT</th>
        <th>Mã ĐH tự quản</th>
        <th>Tên người nhận</th>
        <th>Số điện thoại</th>
        <th>Địa chỉ nhận</th>
        <th width="120px">Thu hộ</th>
        <th>Trạng thái</th>
        <th>Lý do</th>
        <th width="140px">Thời gian tạo</th>
    </tr>
    </thead>
    <tbody>
        @foreach($orders as $key => $order)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $order->client_cod ?: 'N/A' }}</td>
                <td>{{ $order->receiver_name }}</td>
                <td>{{ $order->receiver_phone }}</td>
                <td>{{ $order->receiver_address }}</td>
                <td style="vertical-align: middle;">{{ number_format($order->cod) . ' vnđ' }}</td>
                <td>
                    <span class="badge badge-{{ \App\Modules\Orders\Constants\OrderConstant::status_queue[$order->status]['color'] }}">
                        {{ \App\Modules\Orders\Constants\OrderConstant::status_queue[$order->status]['name'] }}
                    </span><br>
                </td>
                <td>{{ $order->reason }}</td>
                <td>{{ date('d-m-Y H:i', strtotime($order->created_at)) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

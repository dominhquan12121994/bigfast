@extends('layouts.baseShop')

@section('content')
    @php
        $incurred_total_cod = $incurred_fee[$order->id]['incurred_total_cod'] ?? 0;
        $incurred_fee_transport = $incurred_fee[$order->id]['incurred_fee_transport'] ?? 0;
        $incurred_fee_cod = $incurred_fee[$order->id]['incurred_fee_cod'] ?? 0;
        $transport = ($order->payfee === 'payfee_receiver') ? $order->transport_fee + $incurred_fee_transport : 0;
    @endphp
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-body p-1">
                            <form class="form-inline" method="GET">
                                <div class="form-group">
                                    <label class="col-form-label">Mã vận đơn tra cứu &nbsp;</label>
                                    <div class="col-form-label">
                                        <input class="form-control form-control-sm" type="text"
                                               placeholder="Nhập mã vận đơn cần tra cứu"
                                               name="lading_code" id="lading_code" value="{{ $order->lading_code }}"
                                               maxlength="12" required autofocus style="width: 220px">
                                    </div>
                                </div>&nbsp;
                                <button class="btn btn-sm btn-success" type="submit">Tìm kiếm</button>&nbsp;
                                <a href="{{ route('shop.orders.index') }}" class="btn btn-sm btn-primary">Quay lại</a>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                    <div class="card">
                        <div class="card-header"><strong>Thông tin đơn hàng</strong></div>
                        <div class="card-body">
                            <form class="form-horizontal">
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Mã vận đơn:</label>
                                    <div class="col-md-9">
                                        <b>{{ $order->lading_code }}</b>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Ngày lấy dự kiến:</label>
                                    <div class="col-md-9">
                                        <b>{{ $order->extra->expect_pick }}</b>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Ngày giao dự kiến:</label>
                                    <div class="col-md-9">
                                        <b>{{ $order->extra->expect_receiver }}</b>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    @php
                                        $status = ($order->status === 9) ? 5 : $order->status;
                                        $status_detail = ($order->status_detail === 91) ? 51 : $order->status_detail;
                                        $status_detail_style = OrderConstant::status[$status]['detail'][$status_detail];
                                    @endphp
                                    <label class="col-md-3">Trạng thái hiện tại:</label>
                                    <div class="col-md-9">
                                        <span class="badge badge-{{ isset($status_detail_style['color']) ? $status_detail_style['color'] : 'warning' }}">
                                            {{ OrderConstant::status[$status]['detail'][$status_detail]['name'] }}
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                    <div class="card">
                        <div class="card-header"><strong>Thông tin chi tiết</strong></div>
                        <div class="card-body">
                            <form class="form-horizontal">
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Sản phẩm:</label>
                                    <div class="col-md-9">
                                        @for ($i = 0; $i < $countProduct; $i++)
                                            <b>{!! $products[$i]->name !!} [{{ number_format($products[$i]->price) }} vnđ]
                                                [{{ number_format($products[$i]->quantity) }} cái]</b><br>
                                        @endfor
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Cân nặng:</label>
                                    <div class="col-md-9">
                                        <b>{{ $order->weight }} gram</b>
                                    </div>
                                </div>
                                {{--<div class="form-group row mb-0">--}}
                                {{--<label class="col-md-3">Khai giá hàng hóa:</label>--}}
                                {{--<div class="col-md-9">--}}
                                {{--<p class="form-control-static">Username</p>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Tiền thu hộ (COD):<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Tổng tiền thu hộ"></i></label>
                                    <div class="col-md-9">
                                        <b>{{ number_format($order->cod + $incurred_total_cod ) }} đ</b>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Tổng thu:<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Tổng tiền phải thu"></i></label>
                                    <div class="col-md-9">
                                        <b>{{ number_format($order->cod + $incurred_total_cod + $transport ) }}
                                            đ</b>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Lưu ý giao hàng:</label>
                                    <div class="col-md-9">
                                        <b>{{ \App\Modules\Orders\Constants\OrderConstant::notes[$order->extra->note1] }}</b>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                    <div class="card">
                        <div class="card-header"><strong>Thông tin người nhận</strong></div>
                        <div class="card-body">
                            <form class="form-horizontal">
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Họ và tên:</label>
                                    <div class="col-md-9">
                                        <b>{{ $order->receiver->name }}</b>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Điện thoại:</label>
                                    <div class="col-md-9">
                                        <b>{{ $order->receiver->phone }}</b>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Địa chỉ:</label>
                                    <div class="col-md-9">
                                        <b>{{ $order->receiver->address }}</b>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                    <div class="card">
                        <div class="card-header"><strong>Chi phí</strong></div>
                        <div class="card-body">
                            <form class="form-horizontal">
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Tổng phí:<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Bao gồm tất cả các loại phí (phí bảo hiểm, phí thu hộ, phí vận chuyển,...)"></i></label>
                                    <div class="col-md-9">
                                        <b>{{ number_format($order->total_fee + $incurred_fee_cod + $incurred_fee_transport ) }} đ</b>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Phí giao hàng:<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Bao gồm phí bảo hiểm, phí thu hộ"></i></label>
                                    <div class="col-md-9">
                                        <b>{{ number_format($order->transport_fee + $incurred_fee_transport ) }} đ</b>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-md-3">Người trả phí giao hàng:</label>
                                    <div class="col-md-9">
                                        <b>{{ \App\Modules\Orders\Constants\OrderConstant::payfees[$order->payfee] }}</b>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header"><strong>Lịch sử đơn hàng</strong></div>
                        <div class="card-body">
                            @foreach($logs as $ngay => $logsByDate)
                                <table class="table table-responsive-sm table-responsive-md table-responsive-lg">
                                    <thead>
                                    <tr>
                                        <th style="border: none; background: #ebedef; width: 300px">{{ $ngay }}</th>
                                        <th style="border: none; background: #ebedef">Mô tả</th>
                                        <th style="border: none; background: #ebedef">Chi tiết</th>
                                        <th style="border: none; background: #ebedef">Thời gian</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($logsByDate as $log)
                                        @if($log->log_type !== 'collect_money')
                                        <tr>
                                            <td>{{ \App\Modules\Orders\Constants\OrderConstant::actions[$log->log_type] }}</td>
                                            <td>{{ $log->note1 }}</td>
                                            <td>{{ $log->note2 }}</td>
                                            <td>{{ date('H:i', strtotime($log->timer)) }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            {{--<div class="row">--}}
            {{--<div class="col">--}}
            {{--<div class="card">--}}
            {{--<div class="card-header"><strong>Nhật ký người dùng</strong></div>--}}
            {{--<div class="card-body">--}}
            {{--<form class="form-horizontal">--}}
            {{--<div class="form-group row mb-0">--}}
            {{--<label class="col-md-3">Static</label>--}}
            {{--<div class="col-md-9">--}}
            {{--<p class="form-control-static">Username</p>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</form>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
        </div>
    </div>

@endsection


@section('javascript')
    <script type="application/javascript">
        $(document).ready(function () {
            timeout();
            document.getElementById("lading_code").focus();
            $("#lading_code").on("paste", function (e) {
                let clipboardData = e.clipboardData || e.originalEvent.clipboardData || window.clipboardData;
                this.value = clipboardData.getData('text');
            });

        });

        function timeout() {
            setTimeout(function () {
                document.getElementById("lading_code").focus();
                timeout();
            }, 1000);
        }
    </script>
@endsection

@extends('layouts.base')

@section('css')
    <link href="{{ asset('libs/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style type="text/css">
        select + .select2-container {
            width: 200px !important;
        }

        @media screen and (max-width: 575px) {
            .sepa_icon {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-header d-block d-sm-flex justify-content-between">
                            <div class="flex">
                                <i class="fa fa-align-justify"></i>
								<a href="{{ route('admin.assign-ship.show' ) }}">Gán Ship theo khu vực</a>
								&nbsp;<span class="sepa_icon">|</span>&nbsp;
								<b>Quét mã vạch vận đơn</b>
                            </div>
                            <div class="flex text-right">
                                @if ($type == 'pickup')
                                    <span class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline">
										&nbsp;<b>Hàng nhập kho</b>&nbsp;<span class="sepa_icon">|</span>&nbsp;</span>
                                @else
                                    <a class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline"
                                       href="{{ route('admin.assign-ship.scan-barcode', array('type' => 'pickup') ) }}"> Hàng nhập kho&nbsp;<span
                                                class="sepa_icon">|</span>&nbsp;</a>
                                @endif
                                @if ($type == 'shipper')
                                    <span class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline">
										&nbsp;<b>Gán ship giao hàng</b>&nbsp;<span class="sepa_icon">|</span>&nbsp;</span>
                                @else
                                    <a class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline"
                                       href="{{ route('admin.assign-ship.scan-barcode', array('type' => 'shipper') ) }}"> Gán ship giao hàng&nbsp;<span
                                                class="sepa_icon">|</span>&nbsp;</a>
                                @endif
                                @if ($type == 'refund')
                                    <span class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline">
										&nbsp;<b>Gán ship chuyển hoàn</b></span>
                                @else
                                    <a class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline"
                                       href="{{ route('admin.assign-ship.scan-barcode', array('type' => 'refund') ) }}"> Gán ship chuyển hoàn</a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto mr-auto">
                                    <form class="form-inline" id="frm_filter_orders" action="" method="get" autocomplete="off">
                                        <input type="hidden" name="type" value="{{ $type }}">
                                        <label class="mr-2">
                                            <i class="c-icon c-icon-xl cil-list-filter"></i>
                                        </label>
                                        @if ($type == 'pickup')
                                        <div class="form-group mr-3">
                                            <label class="mr-1" for="filter_user">Bưu cục</label>
                                            <select class="form-control frm-select2 form-control-sm" id="filter_offices" name="office">
                                                @foreach ($postOffices as $office)
                                                    <option value="{{ $office->id }}" {{ ($office_selected == $office->id) ? 'selected="selected"' : '' }}>{{ $office->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif
                                        <div class="form-group mr-3">
                                            <label class="mr-1" for="filter_user">{{ $type == 'shipper' ? 'Shipper' : 'Nhân viên kho' }}</label>
                                            <select class="form-control frm-select2 form-control-sm" id="filter_user" name="user">
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" {{ ($user_selected == $user->id) ? 'selected="selected"' : '' }}>{{ $user->name . ' (' . $user->phone . ')' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mr-3">
                                            <label class="mr-1" for="filter_lading_code">Mã vận đơn</label>
                                            <input name="lading_code" id="filter_lading_code" class="form-control form-control-sm"
                                                   type="text" required placeholder="Ví dụ: B17052199994" autofocus
                                                   value="{{ $lading_code }}">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @if (Session::has('message') && $lading_code)
                                <div class="alert {{ Session::get('alert-class', 'alert-info') }} mt-2">{{ Session::get('message') }}</div>
                            @endif
                            @if($order)
                            <div class="row mt-2">
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
                                                        <b>{{ OrderConstant::weekday[strtolower(date('l', strtotime($order->extra->expect_pick)))] . ' ' . date('d-m-Y H:i', strtotime($order->extra->expect_pick)) }}</b>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <label class="col-md-3">Ngày giao dự kiến:</label>
                                                    <div class="col-md-9">
                                                        <b>{{ OrderConstant::weekday[strtolower(date('l', strtotime($order->extra->expect_receiver)))] . ' ' . date('d-m-Y H:i', strtotime($order->extra->expect_receiver)) }}</b>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    @php
                                                        $status_detail = OrderConstant::status[$order->status]['detail'][$order->status_detail];
                                                    @endphp
                                                    <label class="col-md-3">Trạng thái hiện tại:</label>
                                                    <div class="col-md-9">
                                        <span class="badge badge-{{ isset($status_detail['color']) ? $status_detail['color'] : 'warning' }}">
                                            {{ OrderConstant::status[$order->status]['detail'][$order->status_detail]['name'] }}
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
                                                            <b>{!! $products[$i]->name !!} [{{ number_format($products[$i]->price) }} vnđ] [{{ number_format($products[$i]->quantity) }} cái]</b><br>
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
                                                        <b>{{ number_format($order->cod) }} đ</b>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <label class="col-md-3">Tổng thu:<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Tổng tiền phải thu"></i></label>
                                                    <div class="col-md-9">
                                                        <b>{{ number_format($order->cod + (($order->payfee === 'payfee_receiver') ? $order->transport_fee : 0)) }} đ</b>
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
                                                    <label class="col-md-3">Địa chỉ chi tiết:</label>
                                                    <div class="col-md-9">
                                                        <b>{{ $order->receiver->address }}</b>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <label class="col-md-3">Địa chỉ:</label>
                                                    <div class="col-md-9">
                                                        <b>{{ $order->receiver->wards->name }}</b>,
                                                        <b>{{ $order->receiver->districts->name }}</b>,
                                                        <b>{{ $order->receiver->provinces->name }}</b>
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
                                                    <label class="col-md-3">Gói cước:</label>
                                                    <div class="col-md-9">
                                                        <b>{{ isset($order->servicetype) ? $order->servicetype->name : '' }}</b>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <label class="col-md-3">Phí giao hàng:</label>
                                                    <div class="col-md-9">
                                                        <b>{{ number_format($order->transport_fee) }} đ</b>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-0">
                                                    <label class="col-md-3">Phí khác:<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Bao gồm phí bảo hiểm, phí thu hộ"></i></label>
                                                    <div class="col-md-9">
                                                        <b>{{ number_format($order->total_fee - $order->transport_fee) }} đ</b>
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript" src="{{ asset('libs/scannerdetection/jquery.scannerdetection.js') }}"></script>
    <script type="application/javascript">
        $(document).ready( function () {
            document.getElementById("filter_lading_code").focus();
            timeout();
        }).scannerDetection({
            //https://github.com/kabachello/jQuery-Scanner-Detection
            timeBeforeScanTest: 200, // wait for the next character for upto 200ms
            avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
            preventDefault: true,
            endChar: [13],
            onComplete: function(barcode){
                document.getElementById("filter_lading_code").value = barcode;
                document.getElementById("frm_filter_orders").submit();
            },
            onError: function(string) {
                document.getElementById("filter_lading_code").value = string;
                document.getElementById("frm_filter_orders").submit();
            }
        });

		function timeout() {
			setTimeout(function () {
				document.getElementById("filter_lading_code").focus();
				timeout();
			}, 1000);
		}
    </script>
@endsection

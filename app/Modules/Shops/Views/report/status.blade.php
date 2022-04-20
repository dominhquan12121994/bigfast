@extends('layouts.baseShop')

@section('css')
    <link href="{{ asset('libs/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pages/shops/report/status.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <form id="frm_filter_orders" class="form-inline float-right" method="GET" action="">
                            <input id="shop_selected" type="hidden" value="{{ isset($shopId) ? $shopId : 0 }}">
                            <input type="text" class="form-control form-control-sm frm_filter_orders mr-2" id="filter_daterange" name="filter_daterange">
                            <button class="btn btn-sm btn-success button-header mr-2" type="button" onclick="submitFilterOrders();">Xem báo cáo</button>
                            <a class="btn btn-sm btn-info button-header mr-2" href="{{ route('shop.reports') }}">Làm lại</a>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="row ">
                            <div class="col-md-4 ">
                                <b>Bảng báo cáo trạng thái</b>
                                <table id="table1" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Trạng thái</th>
                                            <th style="width: 110px;">Số đơn hàng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ( $status as $item)
                                        <tr>
                                            <td>{{ $item['name'] }}</td>
                                            <td>{{ $item['val'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <b>Bảng báo cáo quận/huyện</b>
                                <div class="tableFixHead">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Quận/huyện</th>
                                                <th style="width: 110px;">Số đơn hàng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $dataDistric = $zone->result['districs']['list'];
                                        @endphp
                                        @if(count($dataDistric) > 0)
                                            @foreach( $dataDistric as $key => $val )
                                                <tr>
                                                    <td>{{ $key }}</td>
                                                    <td>{{ $val }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="2">Chưa có dữ liệu quận/huyện nào</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <b>Bảng báo cáo tỉnh/thành</b>
                                <div class="tableFixHead">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tỉnh/thành</th>
                                                <th style="width: 110px;">Số đơn hàng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $dataZone = $zone->result['provinces']['list'];
                                        @endphp
                                        @if(count($dataZone) > 0)
                                            @foreach( $dataZone as $key => $val )
                                                <tr>
                                                    <td>{{ $key }}</td>
                                                    <td>{{ $val }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="2">Chưa có dữ liệu tỉnh/thành nào</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="c-chart-wrapper">
                                    <canvas id="districs"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="c-chart-wrapper">
                                    <canvas id="provinces"></canvas>
                                </div>
                            </div>
                        </div>

                        {{-- <!-- dev_quanmd add report -->--}}
                        <div class="row pt-4">
                            <div class="col-12" id="cod_tendency_height">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <strong class="align-middle">Báo cáo doanh thu tiền thu hộ (COD)</strong>
                                            </div>
                                            <div class="col-6">
                                                <div class="btn-group float-right" role="group">
                                                    <button id="select_day" type="button" class="btn btn-success btn-sm mx-1" name="date_type" value="day">Ngày</button>
                                                    <button id="select_week" type="button" class="btn btn-success btn-sm mx-1" name="date_type" value="week">Tuần</button>
                                                    <button id="select_month" type="button" class="btn btn-success btn-sm mx-1" name="date_type" value="month">Tháng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Thời gian</th>
                                                    <th>Tiền thu hộ (vnđ)</th>
                                                    <th>Tiền bồi hoàn (vnđ)</th>
                                                    <th>Tiền trả shop (vnđ)</th>
                                                    <th>Tiền dịch vụ (vnđ)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cod_report_table" data-current-display="{{ $dateType }}">
                                                @foreach($codReports as $key => $value)
                                                    @if($value['totalCod'] !== 0 || $value['totalFee'] !== 0 || $value['giveShop'] !== 0 || $value['moneyIndemnify'] !== 0)
                                                    <tr>
                                                        <td>{{ $key }}</td>
                                                        <td style="text-align: right;">{{ number_format($value['totalCod']) }}</td>
                                                        <td style="text-align: right;">{{ number_format($value['moneyIndemnify']) }}</td>
                                                        <td style="text-align: right;">{{ number_format($value['giveShop']) }}</td>
                                                        <td style="text-align: right;">{{ number_format($value['totalFee']) }}</td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card" id="cod_tendency">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <strong class="align-middle">Xu hướng doanh thu tiền thu hộ (COD)</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <div class="c-chart-wrapper" id="cod_chart">
                                                    <canvas id="cod_report" height="350"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                        {{-- <!-- end of dev_quanmd add report -->--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('javascript')
    <script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/daterangepicker/daterangepicker.min.css') }}" />
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script src="{{ asset('js/coreui-chartjs.bundle.js') }}"></script>
    <script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>

    <script>
        let dataDistrics = `{{ implode(",", $zone->result["districs"]["dataChart"]) }}`;
        let labelsDistrics = `{{ implode(",", array_keys($zone->result["districs"]["dataChart"])) }}`;
        let colorsDistrics = `{{ implode(",", $zone->result["districs"]["color"]) }}`;
        let startRangeFilter = `{{ $filter['created_range'][0] }}`;
		let endRangeFilter = `{{ $filter['created_range'][1] }}`;
        let dataProvinces = `{{ implode(",", $zone->result["provinces"]["dataChart"]) }}`;
        let labelsProvinces = `{{ implode(",", array_keys($zone->result["provinces"]["dataChart"])) }}`;
        let colorsProvinces = `{{ implode(",", $zone->result["provinces"]["color"]) }}`;
        let routeShopReports = `{{ route('shop.reports') }}`;
        let headerAuthorization = `Bearer {{ $currentUser->passport_token }}`;
        let apiCodReportIndex = `{{ route('api.cod-report.index') }}`;
    </script>

    <script type="text/javascript" src="{{ asset('js/pages/shops/report/status.min.js') }}"></script>

    <script>
        // dev_quanmd add codReport
        $(document).ready(function() {
            // equal height between table and chart
            // tendencyHeight = $('#cod_tendency').css('height');
            // console.log(tendencyHeight);
            // $('#cod_tendency_height').css('height', tendencyHeight);
            // $('#cod_tendency_height').css('overflow', 'auto');

            // spline chart
            if ( lineChart != null) {
                lineChart.destroy();
            }
            lineChart = new Chart(document.getElementById('cod_report'), {
                type: {{ count($codReports) === 1 ? '`bar`' : '`line`' }},
                data: {
                    labels: [
                        @foreach($codReports as $key => $value)
                            {{ '`' . $key . '`,' }}
                        @endforeach
                    ]
                    ,
                    datasets: [
                    {
                        label: 'Tiền thu hộ (vnđ)',
                        backgroundColor: 'rgba(110, 110, 110, 0.2)',
                        borderColor: 'rgba(34, 69, 180, 1)',
                        pointBackgroundColor: 'rgba(34, 69, 180, 1)',
                        pointBorderColor: '#fff',
                        data: [
                            @foreach($codReports as $key => $value)
                                {{ $value['totalCod'] . ', ' }}
                            @endforeach
                        ]
                    },
                    {
                        label: 'Tiền bồi hoàn (vnđ)',
                        backgroundColor: 'rgba(0, 0, 0, 0.2)',
                        borderColor: 'rgba(0, 0, 0, 1)',
                        pointBackgroundColor: 'rgba(0, 0, 0, 1)',
                        pointBorderColor: '#fff',
                        data: [
                            @foreach($codReports as $key => $value)
                                {{ $value['moneyIndemnify'] . ', ' }}
                            @endforeach
                        ]
                    },
                    {
                        label: 'Tiền trả shop (vnđ)',
                        backgroundColor: 'rgba(151, 187, 205, 0.2)',
                        borderColor: 'rgba(46, 184, 92, 1)',
                        pointBackgroundColor: 'rgba(46, 184, 92, 1)',
                        pointBorderColor: '#fff',
                        data: [
                            @foreach($codReports as $key => $value)
                                {{ $value['giveShop'] . ', ' }}
                            @endforeach
                        ]
                    },
                    {
                        label: 'Tiền dịch vụ (vnđ)',
                        backgroundColor: 'rgba(255, 2, 2, 0.2)',
                        borderColor: 'rgba(255, 2, 2, 1)',
                        pointBackgroundColor: 'rgba(255, 2, 2, 1)',
                        pointBorderColor: '#fff',
                        data: [
                            @foreach($codReports as $key => $value)
                                {{ $value['totalFee'] . ', ' }}
                            @endforeach
                        ]
                    }
                    ]
                },
                options: {
                    responsive: true,
                    tooltips: {
                        mode: 'label',
                        label: 'mylabel',
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }, },
                    },
                    scales: {
                        yAxes: [{
                            mode: 'label',
                            label: 'mylabel',
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    return tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                },
                            },
                            ticks: {
                                callback: function(label, index, labels) {
                                    return label.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                },
                                beginAtZero:true,
                            },
                        }],
                    }
                }
            });
        }); 
    </script>
@endsection

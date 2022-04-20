@extends('layouts.base')

@section('css')
    <link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">

    <style type="text/css">
        select + .select2-container {
            width: 200px !important;
        }
        .scroll {
            overflow: auto;
            max-height: 350px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-12 col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            @if($shopInfo)
                                <div class="float-left">
                                    <span class="badge badge-success ">Shop</span> {{ $shopInfo->name }} - {{ $shopInfo->phone }}<br>
                                    {{ $shopInfo->address }}
                                </div>
                            @else
                                <select name="shop_id" id="select_shop" class="form-control float-left" onchange="shopSelectedChange()" ></select>&nbsp;
                            @endif
                            <form id="frm_filter_orders" class="form-inline float-right" method="GET" action="">
                                <input id="shop_selected" type="hidden" value="{{ $shopId }}">
                                <input type="text" class="form-control form-control-sm frm_filter_orders" id="date_range" name="date_range">&nbsp;
                                <button id="btn_filter_submit" class="btn btn-sm btn-success" type="button">Xem báo cáo</button>&nbsp;
                                <a class="btn btn-sm btn-info" href="{{ route('admin.reports.cod-report') }}">Làm lại</a>&nbsp;
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="row">
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
                                                        <th>Tiền thu hộ (vnđ)<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Tổng tiền thu hộ"></i></th>
                                                        <th>Tiền bồi hoàn (vnđ)<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Tiền bồi thường đơn hàng bị thất lạc, hư hỏng"></i></th>
                                                        <th>Tiền trả shop (vnđ)<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Tiền hoàn shop sau khi trừ các loại phí"></i></th>
                                                        <th>Tiền dịch vụ (vnđ)<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Bao gồm tổng loại phí sau nếu có (phí vận chuyển, phí bảo hiểm, phí thu hộ, phí chuyển hoàn, phí lưu kho, phí thay đổi thông tin, phí chuyển khoản)"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="cod_report_table" data-current-display="{{ $dateType }}">
                                                    @foreach($codReports as $key => $value)
                                                        @if($value['totalCod'] !== 0 || $value['totalFee'] !== 0 || $value['giveShop'] !== 0)
                                                        <tr>
                                                            @if (!empty($value['dateRange']))
                                                                <td>{{ $key . ' ' . $value['dateRange'] }}</td>
                                                            @else
                                                                <td>{{ $key}}</td>
                                                            @endif
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
                                                    <div class="c-chart-wrapper">
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
<script src="{{ asset('libs/loading/js/HoldOn.js') }}"></script>

<script>
    // find shop information
    let routeApi = '{{ route('api.shops.find-by-name') }}';
    var $selectShop = $('#select_shop');
    let lineChart = null;
    $selectShop.select2({
        theme: "classic",
        placeholder: 'Nhập tên Shop để lên đơn hàng',
        allowClear: true,
        ajax: {
            delay: 300,
            url: routeApi,
            dataType: 'json',
            data: function (params) {
                return {
                    search: params.term, // search term
                };
            },
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                let resData = [];
                if (data.status_code === 200) {
                    resData = data.data;
                    console.log(data.data);
                }
                return {
                    results: resData
                };
            },
            cache: true
        },
        width: 'resolve',
        minimumInputLength: 3,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection,
        language: {
            inputTooShort: function() {
                return 'Nhập thông tin tìm kiếm';
            }
        }
    });

    // template for result
    function formatRepo (repo) {
        if (repo.loading) {
            return repo.text;
        }
        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__name'></div>" +
            "<div class='select2-result-repository__phone'></div>" +
            "<div class='select2-result-repository__address'></div>" +
            "</div>" +
            "</div>" +
            "</div>"
        );
        $container.find(".select2-result-repository__name").text('Tên shop: '+ repo.name);
        $container.find(".select2-result-repository__phone").text('Sđt: ' + repo.phone);
        $container.find(".select2-result-repository__address").text('Địa chỉ: '+ repo.address);
        return $container;
    }

    // template for selection
    function formatRepoSelection (repo) {
        if (repo.id === '') return 'Tìm kiếm Shop cần quản lý';
        return repo.name || repo.phone;
    }

    // do something after change shop
    function shopSelectedChange() {
        dateType = $('#cod_report_table').attr('data-current-display');
        $('#shop_selected').val($('#select_shop').val());
        console.log($('#shop_selected').val());
        submitFilterReports(dateType);
    }

    // preparing uri
    function reFormatUriParam(route = '', params = '') {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const newParams = new URLSearchParams(params);
        const newKeys = newParams.keys();
        for (const key of newKeys) {
            urlParams.set(key, newParams.get(key));
        }
        return route + '?' + urlParams.toString();
    }

    // submit filter have shop_id, date_type, date_range
    function submitFilterReports() {
        // sk-rect, sk-dot, sk-cube, sk-bounce, sk-circle
        dateType = $('#cod_report_table').attr('data-current-display');
        HoldOn.open({theme:"sk-rect"});
        let shopId = $('#shop_selected').val();
        let route = '{{ route('admin.reports.cod-report') }}';
        let newParams = '&shop_id=' + shopId;
        $('#shop_selected, #date_range, #cod_report_table').each(
            function(index) {
                var input = $(this);
                if (input.attr('name') === 'date_range') {
                    let dateRange = $('#date_range').val();
                    newParams += '&begin=' + dateRange.substr(0, 10) + '&end=' + dateRange.substr(dateRange.length - 10, 10);
                }
            });
        newParams += '&date_type=' + dateType;
        console.log(newParams);
        window.location = reFormatUriParam(route, newParams);
    }

    $(document).ready(function() {
        console.log('loaded');
        // set event when click 'xem bao cao'
        $('#btn_filter_submit').click(function() {
            submitFilterReports();
        });
        // input date by daterangepicker
        $('input[name="date_range"]').daterangepicker({
            startDate: moment(`{{ $filter['date_range'][0] }}`, 'DD-MM-YYYY'),
            endDate: moment(`{{ $filter['date_range'][1] }}`, 'DD-MM-YYYY'),
            maxDate: moment(),
            dateLimit: { days: 60 },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Hôm nay': [moment(), moment()],
                'Hôm qua': [moment().subtract('days', 1), moment().subtract('days', 1)],
                '7 ngày trước': [moment().subtract('days', 6), moment()],
                '30 ngày trước': [moment().subtract('days', 29), moment()],
                'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                'Tháng trước': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            opens: 'left',
            buttonClasses: ['btn btn-default'],
            applyClass: 'btn-small btn-primary',
            cancelClass: 'btn-small',
            separator: ' to ',
            locale: {
                format: 'DD-MM-YYYY',
                applyLabel: 'Apply',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Tuỳ chọn',
                daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6','T7'],
                monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                firstDay: 1
            }
        },
        function(start, end) {
            startDateFilter = start;
            endDateFilter = end;
        });
        // equal height between table and chart
        // tendencyHeight = $('#cod_tendency').css('height')
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

        $('#select_day').click(function() {
            $('#cod_report_table').attr('data-current-display', 'day');
            let shopId = $('#shop_selected').val();
            let dateType = 'day';
            let dateRange = $('#date_range').val();
            console.log('day'+ '|' + shopId + '|' + dateType + '|' + dateRange);
            let routeApi = '{{ route('api.cod-report.index') }}';
            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: routeApi,
                method: "POST",
                data: {
                    "shop_id": shopId,
                    "date_type": dateType,
                    "date_range": dateRange,
                },
                success: function(response){
                    if (response.status_code === 200) {
                        let arrDay = [], arrTotalCod = [], arrTotalFee = [], arrGiveShop = [], arrMoneyIndemnify = [];
                        let i = 0;
                        for (const [key, value] of Object.entries(response.data)) {
                            arrDay[i] = key;
                            arrTotalCod[i] = value.totalCod;
                            arrMoneyIndemnify[i] = value.moneyIndemnify;
                            arrTotalFee[i] = value.totalFee;
                            arrGiveShop[i] = value.giveShop;
                            i++;
                        }
                        let chartType = 'line';
                        if (arrDay.length == 1) {
                            chartType = 'bar';
                        }
                        if ( lineChart != null) {
                            lineChart.destroy();
                        }
                        $('.c-chart-wrapper').html('<canvas id="cod_report" height="350"></canvas>');
                        lineChart = new Chart(document.getElementById('cod_report'), {
                            type: chartType,
                            data: {
                                labels: arrDay,
                                datasets: [
                                {
                                    label: 'Tiền thu hộ (vnđ)',
                                    backgroundColor: 'rgba(110, 110, 110, 0.2)',
                                    borderColor: 'rgba(34, 69, 180, 1)',
                                    pointBackgroundColor: 'rgba(34, 69, 180, 1)',
                                    pointBorderColor: '#fff',
                                    data: arrTotalCod
                                },
                                {
                                    label: 'Tiền bồi hoàn (vnđ)',
                                    backgroundColor: 'rgba(0, 0, 0, 0.2)',
                                    borderColor: 'rgba(0, 0, 0, 1)',
                                    pointBackgroundColor: 'rgba(0, 0, 0, 1)',
                                    pointBorderColor: '#fff',
                                    data: arrMoneyIndemnify
                                },
                                {
                                    label: 'Tiền trả shop (vnđ)',
                                    backgroundColor: 'rgba(151, 187, 205, 0.2)',
                                    borderColor: 'rgba(46, 184, 92, 1)',
                                    pointBackgroundColor: 'rgba(46, 184, 92, 1)',
                                    pointBorderColor: '#fff',
                                    data: arrGiveShop
                                },
                                {
                                    label: 'Tiền dịch vụ (vnđ)',
                                    backgroundColor: 'rgba(255, 2, 2, 0.2)',
                                    borderColor: 'rgba(255, 2, 2, 1)',
                                    pointBackgroundColor: 'rgba(255, 2, 2, 1)',
                                    pointBorderColor: '#fff',
                                    data: arrTotalFee
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
                        let newContent = '';
                        for (let i = 0; i < arrDay.length; i++) {
                            if (arrTotalCod[i] != 0 || arrTotalFee[i] != 0 || arrGiveShop[i] != 0 || arrMoneyIndemnify[i] != 0) {
                                newContent += `<tr>
                                        <td>` + arrDay[i] +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrTotalCod[i]) +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrMoneyIndemnify[i]) +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrGiveShop[i]) +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrTotalFee[i]) +  `</td>
                                    </tr>`;
                            }
                        }
                        $('#cod_report_table').html(newContent);
                    }
                }
            });
        });

        $('#select_week').click(function() {
            $('#cod_report_table').attr('data-current-display', 'week');
            let shopId = $('#shop_selected').val();
            let dateType = 'week';
            let dateRange = $('#date_range').val();
            console.log('week' + '|' + shopId + '|' + dateType + '|' + dateRange);
            let routeApi = '{{ route('api.cod-report.index') }}';
            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: routeApi,
                method: "POST",
                data: {
                    "shop_id": shopId,
                    "date_type": dateType,
                    "date_range": dateRange,
                },
                success: function(response){
                    if (response.status_code === 200) {
                        let arrWeek = [], arrTotalCod = [], arrTotalFee = [], arrGiveShop = [], arrMoneyIndemnify = [];
                        let i = 0;
                        for (const [key, value] of Object.entries(response.data)) {
                            arrWeek[i] = key + ' ' + value.dateRange;
                            arrTotalCod[i] = value.totalCod;
                            arrMoneyIndemnify[i] = value.moneyIndemnify;
                            arrTotalFee[i] = value.totalFee;
                            arrGiveShop[i] = value.giveShop;
                            i++;
                        }
                        let chartType = 'line';
                        if (arrWeek.length == 1) {
                            chartType = 'bar';
                        }
                        if ( lineChart != null) {
                            lineChart.destroy();
                        }
                        $('.c-chart-wrapper').html('<canvas id="cod_report" height="350"></canvas>');
                        lineChart = new Chart(document.getElementById('cod_report'), {
                            type: chartType,
                            data: {
                                labels: arrWeek,
                                datasets: [
                                {
                                    label: 'Tiền thu hộ (vnđ)',
                                    backgroundColor: 'rgba(110, 110, 110, 0.2)',
                                    borderColor: 'rgba(34, 69, 180, 1)',
                                    pointBackgroundColor: 'rgba(110, 110, 110, 0.2)',
                                    pointBorderColor: '#fff',
                                    data: arrTotalCod
                                },
                                {
                                    label: 'Tiền bồi hoàn (vnđ)',
                                    backgroundColor: 'rgba(0, 0, 0, 0.2)',
                                    borderColor: 'rgba(0, 0, 0, 1)',
                                    pointBackgroundColor: 'rgba(0, 0, 0, 0.2)',
                                    pointBorderColor: '#fff',
                                    data: arrMoneyIndemnify
                                },
                                {
                                    label: 'Tiền trả shop (vnđ)',
                                    backgroundColor: 'rgba(151, 187, 205, 0.2)',
                                    borderColor: 'rgba(46, 184, 92, 1)',
                                    pointBackgroundColor: 'rgba(151, 187, 205, 0.2)',
                                    pointBorderColor: '#fff',
                                    data: arrGiveShop
                                },
                                {
                                    label: 'Tiền dịch vụ (vnđ)',
                                    backgroundColor: 'rgba(255, 2, 2, 0.2)',
                                    borderColor: 'rgba(255, 2, 2, 1)',
                                    pointBackgroundColor: 'rgba(255, 2, 2, 0.2)',
                                    pointBorderColor: '#fff',
                                    data: arrTotalFee
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
                        let newContent = '';
                        for (let i = 0; i < arrWeek.length; i++) {
                            if (arrTotalCod[i] != 0 || arrTotalFee[i] != 0 || arrGiveShop[i] != 0 || arrMoneyIndemnify[i] != 0) {
                                newContent += `<tr>
                                        <td>` + arrWeek[i] +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrTotalCod[i]) +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrMoneyIndemnify[i]) +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrGiveShop[i]) +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrTotalFee[i]) +  `</td>
                                    </tr>`;
                            }
                        }
                        $('#cod_report_table').html(newContent);
                    }
                }
            });
        });

        $('#select_month').click(function() {
            $('#cod_report_table').attr('data-current-display', 'month');
            let shopId = $('#shop_selected').val();
            let dateType = 'month';
            let dateRange = $('#date_range').val();
            console.log('month' + '|' + shopId + '|' + dateType + '|' + dateRange);
            let routeApi = '{{ route('api.cod-report.index') }}';
            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: routeApi,
                method: "POST",
                data: {
                    "shop_id": shopId,
                    "date_type": dateType,
                    "date_range": dateRange,
                },
                success: function(response){
                    if (response.status_code === 200) {
                        let arrMonth = [], arrTotalCod = [], arrTotalFee = [], arrGiveShop = [], arrMoneyIndemnify = [];
                        let i = 0;
                        for (const [key, value] of Object.entries(response.data)) {
                            arrMonth[i] = key;
                            arrTotalCod[i] = value.totalCod;
                            arrMoneyIndemnify[i] = value.moneyIndemnify;
                            arrTotalFee[i] = value.totalFee;
                            arrGiveShop[i] = value.giveShop;
                            i++;
                        }
                        let chartType = 'line';
                        if (arrMonth.length == 1) {
                            chartType = 'bar';
                        }
                        if ( lineChart != null) {
                            lineChart.destroy();
                        }
                        $('.c-chart-wrapper').html('<canvas id="cod_report" height="350"></canvas>');
                        lineChart = new Chart(document.getElementById('cod_report'), {
                            type: chartType,
                            data: {
                                labels: arrMonth,
                                datasets: [
                                {
                                    label: 'Tiền thu hộ (vnđ)',
                                    backgroundColor: 'rgba(110, 110, 110, 0.2)',
                                    borderColor: 'rgba(34, 69, 180, 1)',
                                    pointBackgroundColor: 'rgba(34, 69, 180, 1)',
                                    pointBorderColor: '#fff',
                                    data: arrTotalCod
                                },
                                {
                                    label: 'Tiền bồi hoàn (vnđ)',
                                    backgroundColor: 'rgba(0, 0, 0, 0.2)',
                                    borderColor: 'rgba(0, 0, 0, 1)',
                                    pointBackgroundColor: 'rgba(0, 0, 0, 1)',
                                    pointBorderColor: '#fff',
                                    data: arrMoneyIndemnify
                                },
                                {
                                    label: 'Tiền trả shop (vnđ)',
                                    backgroundColor: 'rgba(151, 187, 205, 0.2)',
                                    borderColor: 'rgba(46, 184, 92, 1)',
                                    pointBackgroundColor: 'rgba(46, 184, 92, 1)',
                                    pointBorderColor: '#fff',
                                    data: arrGiveShop
                                },
                                {
                                    label: 'Tiền dịch vụ (vnđ)',
                                    backgroundColor: 'rgba(255, 2, 2, 0.2)',
                                    borderColor: 'rgba(255, 2, 2, 1)',
                                    pointBackgroundColor: 'rgba(255, 2, 2, 1)',
                                    pointBorderColor: '#fff',
                                    data: arrTotalFee
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
                        let newContent = '';
                        for (let i = 0; i < arrMonth.length; i++) {
                            if (arrTotalCod[i] != 0 || arrTotalFee[i] != 0 || arrGiveShop[i] != 0) {
                                newContent += `<tr>
                                        <td>` + arrMonth[i] +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrTotalCod[i]) +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrMoneyIndemnify[i]) +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrGiveShop[i]) +  `</td>
                                        <td style="text-align: right;">` + new Intl.NumberFormat().format(arrTotalFee[i]) +  `</td>
                                    </tr>`;
                            }
                        }
                        $('#cod_report_table').html(newContent);
                    }
                }
            });
        });
    });
    $('document').ready(function () {
        $(".cancelBtn").html("Huỷ bỏ");
        $(".applyBtn").html("Áp dụng");
    });
</script>
@endsection

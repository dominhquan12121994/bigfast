@extends('layouts.base')

@section('css')
    <link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">

    <style type="text/css">
        select + .select2-container {
            width: 200px !important;
        }
        .tableFixHead { overflow-y: auto; max-height: 350px; }
        .tableFixHead thead th { position: sticky; top: 0; background: white;  }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        @if($shop)
                            <div class="float-left">
                                <span class="badge badge-success ">Shop</span> {!! $shop->name !!} - {{ $shop->phone }}<br>
                                {{ $shop->address }}
                            </div>
                        @else
                            <select id="shopSelected" class="form-control float-left" onchange="shopSelectedChange()" ></select>&nbsp;
                        @endif
                        <form id="frm_filter_orders" class="form-inline float-right" method="GET" action="">
                            <input type="hidden" name="shop" value="{{ $filter['shop'] }}">

                            <input type="text" class="form-control form-control-sm frm_filter_orders mr-2" id="filter_daterange" name="filter_daterange">
                            <button class="btn btn-sm btn-success mr-2" type="button" onclick="submitFilterOrders();">Xem báo cáo</button>
                            <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.report.by-zone') }}">Làm lại</a>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="c-chart-wrapper">
                                    <canvas id="districs"></canvas>
                                </div>
                                <div class="tableFixHead">
                                    <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped">
                                        <thead>
                                            <tr>
                                                <th>Quận/huyện</th>
                                                <th>Số đơn hàng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $dataDistric = $data->result['districs']['list'];
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
                            <div class="col-md-6">
                                <div class="c-chart-wrapper">
                                    <canvas id="provinces"></canvas>
                                </div>
                                <div class="tableFixHead">
                                    <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped">
                                        <thead>
                                            <tr>
                                                <th>Tỉnh/thành</th>
                                                <th>Số đơn hàng</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $dataZone = $data->result['provinces']['list'];
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

    <script>
        let dataDistrics = '{{ implode(",", $data->result["districs"]["dataChart"]) }}';
        let labelsDistrics = '{{ implode(",", array_keys($data->result["districs"]["dataChart"])) }}';
        let colorsDistrics = '{{ implode(",", $data->result["districs"]["color"]) }}';
        dataDistrics = dataDistrics.split(',');
        labelsDistrics = labelsDistrics.split(',');
        colorsDistrics = colorsDistrics.split(',');
        let districsChart = new Chart(document.getElementById('districs'), {
            type: 'pie',
            data: {
                labels: labelsDistrics,
                datasets: [{
                label: 'Top 20 Quận/huyện',
                data: dataDistrics,
                backgroundColor: colorsDistrics,
                hoverBackgroundColor: colorsDistrics
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Biểu đồ đơn theo Quận/huyện',
                    position: 'top'
                },
                tooltips: {
                    mode: 'label',
                    label: 'mylabel',
                    callbacks: {
                        label: function(tooltipItem, data) {
                            let indexDistrict = tooltipItem.index;
                            let sumDistrict = data.datasets[0].data.reduce(function (accumulator, currentValue) {
                                return parseInt(accumulator) + parseInt(currentValue);
                            }, 0);
                            return data.labels[indexDistrict] + ' :' + (parseInt(data.datasets[0].data[indexDistrict]) / parseInt(sumDistrict) * 100).toFixed(1)+"%";
                        },
                    },
                },
                legend: {
                    position: 'right'
                }
            }
        });
        let dataProvinces = '{{ implode(",", $data->result["provinces"]["dataChart"]) }}';
        let labelsProvinces = '{{ implode(",", array_keys($data->result["provinces"]["dataChart"])) }}';
        let colorsProvinces = '{{ implode(",", $data->result["provinces"]["color"]) }}';
        dataProvinces = dataProvinces.split(',');
        labelsProvinces = labelsProvinces.split(',');
        colorsProvinces = colorsProvinces.split(',');
        let provincesChart = new Chart(document.getElementById('provinces'), {
            type: 'pie',
            data: {
                labels: labelsProvinces,
                datasets: [{
                label: 'Top 20 Quận/huyện',
                data: dataProvinces,
                backgroundColor: colorsProvinces,
                hoverBackgroundColor: colorsProvinces
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Biểu đồ đơn theo Tỉnh/thành',
                    position: 'top'
                },
                tooltips: {
                    mode: 'label',
                    label: 'mylabel',
                    callbacks: {
                        label: function(tooltipItem, data) {
                            let indexProvinces = tooltipItem.index;
                            let sumProvinces = data.datasets[0].data.reduce(function (accumulator, currentValue) {
                                return parseInt(accumulator) + parseInt(currentValue);
                            }, 0);
                            return data.labels[indexProvinces] + ' :' + (parseInt(data.datasets[0].data[indexProvinces]) / parseInt(sumProvinces) * 100).toFixed(1)+"%";
                        },
                    },
                },
                legend: {
                    position: 'right'
                }
            }
        });
        $('input[name="filter_daterange"]').daterangepicker({
            startDate: moment(`{{ $filter['created_range'][0] }}`),
            endDate: moment(`{{ $filter['created_range'][1] }}`),
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
            // $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
            startDateFilter = start;
            endDateFilter = end;
        });

        let routeApi = '{{ route('api.shops.find-by-name') }}';
        var $shopSelected = $('#shopSelected');
        $shopSelected.select2({
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
                },
                noResults: function() { return 'Không có kết quả phù hợp'; }
            }
        });
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
        function formatRepoSelection (repo) {
            if (repo.id === '') return 'Tìm kiếm Shop cần quản lý';
            return repo.name || repo.phone;
        }
        function shopSelectedChange() {
            let shopSelected = document.getElementById('shopSelected').value;
            let route = '{{ route('admin.report.by-zone') }}';
            const queryString = window.location.search;
            if (queryString === '') route += '?';
            window.location = route + queryString + '&shop=' + shopSelected;
        }

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

        function submitFilterOrders() {
            // sk-rect, sk-dot, sk-cube, sk-bounce, sk-circle
            HoldOn.open({theme:"sk-rect"});
            let route = '{{ route('admin.report.by-zone') }}';
            let newParams = '';
            $('#frm_filter_orders input, #frm_filter_orders select').each(
                function(index) {
                    var input = $(this);
                    if (input.attr('name') === 'shop') {
                        newParams += '&shop=' + input.val();
                    }
                    if (input.attr('name') === 'filter_daterange') {
                        let dateRange = input.val();
                        newParams += '&begin=' + dateRange.substr(0, 10) + '&end=' + dateRange.substr(dateRange.length - 10, 10);
                    }
                });
            window.location = reFormatUriParam(route, newParams);
        }
        $('document').ready(function () {
            $(".cancelBtn").html("Huỷ bỏ");
            $(".applyBtn").html("Áp dụng");
        });
    </script>
@endsection

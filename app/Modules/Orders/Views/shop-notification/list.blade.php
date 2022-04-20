@extends('layouts.base')

@section('css')
    <link href="{{ asset('libs/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style type="text/css">
        select + .select2-container {
            width: 200px !important;
        }
        .scroll {
            overflow: auto;
            max-height: 350px;
        }

        .tooltip-inner {
            max-width: 350px;
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
                            <div>
                                @if($shopInfo)
                                    <div class="float-left mr-3">
                                        <span class="badge badge-success ">Shop</span> {{ $shopInfo->name }} - {{ $shopInfo->phone }}<br>
                                        {{ $shopInfo->address }}
                                    </div>
                                @else
                                    <select name="shop_id" id="select_shop" class="form-control float-left" onchange="shopSelectedChange()" ></select>&nbsp;
                                @endif
                                <form id="frm_filter_orders" class="form-inline float-right" method="GET" action="">
                                    <input id="shop_selected" type="hidden" value="{{ isset($shopInfo->id) ? $shopInfo->id : 0 }}">
                                    <input type="text" class="form-control form-control-sm frm_filter_orders"
                                           id="date_range" name="date_range">&nbsp;
                                    <button id="btn_filter_submit" class="btn btn-sm btn-success" type="button">Xem thông
                                        báo
                                    </button>&nbsp;
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.shop-notification.index') }}">Làm lại</a>
                                </form>
                            </div>
                            <div>
                                <a class="btn btn-sm btn-primary" href="{{ route('admin.shop-notification.create') }}">Thêm
                                    mới thông báo</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row" id="tbl-reconcile">
                                <div class="col pt-3">
                                    <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table_shops">
                                        <thead>
                                        <th>STT</th>
                                        <th style="min-width:160px">Nội dung thông báo</th>
                                        <th style="min-width:100px">Đường dẫn</th>
                                        <th style="min-width:105px">Người nhận</th>
                                        <th style="min-width:90px">Thời gian</th>
                                        <th class="text-center" style="min-width:95px">Tiêu chí<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Bao gồm: tiêu chí, mục đích sử dụng, quy mô (nếu có) của thông báo"></i></th>
                                        </thead>
                                        <tbody>
                                        @if($shopInfo)
                                            @foreach($arrNotification as $key => $notification)
                                                <tr>
                                                    <td>{{ number_format($key + 1) }}</td>
                                                    <td data-toggle="tooltip" title="{{ strlen($notification->content) > 90 ? $notification->content : '' }}">{{ strlen($notification->content) > 90 ? substr($notification->content, 0, strrpos(substr($notification->content, 0, 90), " ")) . "..." : $notification->content }}</td>
                                                    <td>
                                                        <a target="_blank" href="{{ $notification->notification->link }}">{{ strlen($notification->notification->link) > 50 ? substr($notification->notification->link, 0, 50) . '...' :  $notification->notification->link }}</a>
                                                    </td>
                                                    <td>{{ $notification['receiver_name'] ? $notification['receiver_name'] : $notification->notification->receiver_quantity . ' Shops' }}</td>
                                                    <td>{{ date('d-m-Y H:i', strtotime($notification->notification->created_at)) }}</td>
                                                    <td class="text-center"><span data-html="true" title="{{ "<h5>Tiêu chí:</h5><p>Mục đích sử dụng: " . $notification->notification->selected_purpose . "</p><p>Quy mô: " . $notification->notification->selected_scale . "</p><p>Ngành hàng: " . $notification->notification->selected_branch . "</p>" }}" data-toggle="tooltip" class="badge badge-info">Xem</span></td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach($arrNotification as $key => $notification)
                                                <tr>
                                                    <td>{{ number_format($key + 1) }}</td>
                                                    <td data-toggle="tooltip" title="{{ strlen($notification->content) > 90 ? $notification->content : '' }}">{{ strlen($notification->content) > 90 ? substr($notification->content, 0, strrpos(substr($notification->content, 0, 90), " ")) . "..." : $notification->content }}</td>
                                                    <td>
                                                        <a target="_blank" href="{{ $notification->link }}">{{ strlen($notification->link) > 50 ? substr($notification->link, 0, 50) . '...' :  $notification->link }}</a>
                                                    </td>
                                                    <td>{{ $notification->receiveNotification[0]['receiver_name'] ? $notification->receiveNotification[0]['receiver_name'] : $notification->receiver_quantity . ' Shops' }}</td>
                                                    <td>{{ date('d-m-Y H:i', strtotime($notification->created_at)) }}</td>
                                                    <td class="text-center"><span data-html="true" title="{{ $notification->selected_purpose || $notification->selected_scale || $notification->selected_branch ? "<h5>Tiêu chí:</h5><p>Mục đích sử dụng: " . $notification->selected_purpose . "</p><p>Quy mô: " . $notification->selected_scale . "</p><p>Ngành hàng: " . $notification->selected_branch . "</p>" : '<p>Không chọn theo tiêu chí</p>' }}" data-toggle="tooltip" class="badge badge-info">Xem</span></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end">
                                        {{ $arrNotification->withQueryString()->links() }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
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
    <script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/daterangepicker/daterangepicker.min.css') }}"/>

    <script>
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

        // do something after change shop
        function shopSelectedChange() {
            $('#shop_selected').val($('#select_shop').val());
            console.log($('#shop_selected').val());
            submitFilterReports();
        }

        // submit filter have shop_id, date_type, date_range
        function submitFilterReports() {
            // sk-rect, sk-dot, sk-cube, sk-bounce, sk-circle
            HoldOn.open({theme: "sk-rect"});
            let shopId = $('#shop_selected').val();
            let route = '{{ route('admin.shop-notification.index') }}';
            let newParams = 'shop_id=' + $('#shop_selected').val();
            $('#date_range').each(
                function (index) {
                    var input = $(this);
                    if (input.attr('name') === 'date_range') {
                        let dateRange = $('#date_range').val();
                        newParams += '&begin=' + dateRange.substr(0, 10) + '&end=' + dateRange.substr(dateRange.length - 10, 10);
                    }
                });
            console.log(newParams);
            window.location = reFormatUriParam(route, newParams);
        }
    </script>

    <script type="application/javascript">
        $(document).ready(function () {
            // input date range
            $('input[name="date_range"]').daterangepicker({
                    // startDate: moment(),
                    startDate: moment(`{{ $filter['date_range'][0] }}`, 'DD-MM-YYYY'),
                    // endDate: moment(),
                    endDate: moment(`{{ $filter['date_range'][1] }}`, 'DD-MM-YYYY'),
                    maxDate: moment(),
                    dateLimit: {days: 30},
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
                function (start, end) {
                    startDateFilter = moment(start).format('DD/MM/YYYY');
                    endDateFilter = moment(end).format('DD/MM/YYYY');
                });

            // when click "xem bao cao"
            $('#btn_filter_submit').click(function () {
                submitFilterReports();
            });
        });
        $('document').ready(function () {
            $(".cancelBtn").html("Huỷ bỏ");
            $(".applyBtn").html("Áp dụng");
        });
    </script>

    <script>
        // find shop information
        let routeApi = '{{ route('api.shops.find-by-name') }}';
        var $selectShop = $('#select_shop');
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
    </script>

    <script>
        $('[data-toggle="tooltip"]').each(function () {
            $(this).tooltip({
                html: true,
            });
        });
    </script>
@endsection

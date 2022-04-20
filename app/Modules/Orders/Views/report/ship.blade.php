@extends('layouts.base')

@section('css')
    <link href="{{ asset('libs/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">

    <style type="text/css">
        select + .select2-container {
            width: 200px !important;
        }
        .scroll {
            overflow: auto;
            max-height: 515px;
        }
        #table2_wrapper {
            display:none;
        }
        #table2 {
            width:100% !important;
        }
        .text-table2 {
            display:none;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <form id="frm_filter_orders" class="form-inline float-right" method="GET" action="">
                            <input type="text" class="form-control form-control-sm frm_filter_orders mr-2" id="filter_daterange" name="filter_daterange">
                            <button class="btn btn-sm btn-success mr-2" type="button" onclick="submitFilterOrders();">Xem báo cáo</button>
                            <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.report.by-ship') }}">Làm lại</a>
                        </form>
                        <div class="flex text-right">
                            @if( $filter['type'] != 'warehouser')
                                <a href="{{ route('admin.report.by-ship') }}?type=warehouser">Nhân viên kho</a>&nbsp;|&nbsp;
                            @else
                            Nhân viên kho&nbsp;|&nbsp;
                            @endif
                            @if( $filter['type'] != 'send')
                                <a href="{{ route('admin.report.by-ship') }}?type=send">Nhân viên ship</a>
                            @else
                            Nhân viên ship
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table id="table1" class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped">
                                    <thead>
                                        <tr>
                                            @if( $filter['type'] == 'send')
                                                <th>Nhân viên ship</th>
                                                <th>Tổng đơn</th>
                                                <th>Thành công</th>
                                                <th>Không thành công</th>
                                            @else
                                                <th>Nhân viên kho</th>
                                                <th>Tổng đơn</th>
                                                <th>Lấy thành công</th>
                                                <th>Lấy không thành công</th>
                                                <th>Hoàn thành công</th>
                                                <th>Hoàn không thành công</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if( $filter['type'] == 'send')
                                            @foreach ($data as $key => $item)
                                                @php
                                                    $total = array_sum(array_column($item, 'total'));
                                                    $success = array_sum(array_column($item, 'success'));
                                                    $fail = array_sum(array_column($item, 'fail'));
                                                    $name = array_column($item, 'name');
                                                    $name = array_shift($name);
                                                @endphp
                                                <tr>
                                                    <td><a href="javascript:void(0)" onclick="showDetail(`{{$key}}`,`{{$name}}`)">{{ $name }}</a></td>
                                                    <td>{{ $total }}</td>
                                                    <td>{{ $success }}</td>
                                                    <td>{{ $fail }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            @foreach ($data as $key => $item)
                                                @php
                                                    $total = array_sum(array_column($item, 'total'));
                                                    $success_pick = array_sum(array_column($item, 'success_pick'));
                                                    $fail_pick = array_sum(array_column($item, 'fail_pick'));
                                                    $success_refund = array_sum(array_column($item, 'success_refund'));
                                                    $fail_refund = array_sum(array_column($item, 'fail_refund'));
                                                    $name = array_column($item, 'name');
                                                    $name = array_shift($name);
                                                @endphp
                                                <tr>
                                                    <td><a href="javascript:void(0)" onclick="showDetail(`{{$key}}`,`{{$name}}`)">{{ $name }}</a></td>
                                                    <td>{{ $total }}</td>
                                                    <td>{{ $success_pick }}</td>
                                                    <td>{{ $fail_pick }}</td>
                                                    <td>{{ $success_refund }}</td>
                                                    <td>{{ $fail_refund }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12" style="margin-top: 20px">
                                <div class="text-table2"><b>Chi tiết đơn nhân viên </b><b class="user-table2"></b></div>
                                <table id="table2" class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped">
                                    <thead>
                                        @if( $filter['type'] == 'send')
                                            <tr>
                                                <th>Cửa hàng</th>
                                                <th>Tổng đơn</th>
                                                <th>Giao thành công</th>
                                                <th>Giao không thành công</th>
                                            </tr>
                                        @else
                                            <th>Cửa hàng</th>
                                            <th>Tổng đơn</th>
                                            <th>Lấy thành công</th>
                                            <th>Lấy không thành công</th>
                                            <th>Hoàn thành công</th>
                                            <th>Hoàn không thành công</th>
                                        @endif
                                    </thead>
                                    <tbody>

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

@endsection
@section('javascript')
    <script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/daterangepicker/daterangepicker.min.css') }}" />
    <script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>

    <script>
        let table2 = '';

        $(document).ready( function () {
            $('#table1').DataTable({
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ bản ghi mỗi trang",
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "info": "_PAGE_/_PAGES_ trang",
                    "infoEmpty": "Không tìm thấy dữ liệu",
                    "infoFiltered": "(tìm kiếm trong tổng số _MAX_ bản ghi)",
                    "decimal":        "",
                    "emptyTable":     "Không tìm thấy dữ liệu",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "loadingRecords": " tải...",
                    "processing":     " tải...",
                    "search":         "Tìm kiếm:",
                    "paginate": {
                        "first":      "Đầu",
                        "last":       "Cuối",
                        "next":       "Sau",
                        "previous":   "Trước"
                    },
                    "aria": {
                        "sortAscending":  ": xếp tăng dần",
                        "sortDescending": ": xếp giảm dần"
                    }
                },
                stateSave: true,
            });

            table2 = $('#table2').DataTable({
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ bản ghi mỗi trang",
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "info": "_PAGE_/_PAGES_ trang",
                    "infoEmpty": "Không tìm thấy dữ liệu",
                    "infoFiltered": "(tìm kiếm trong tổng số _MAX_ bản ghi)",
                    "decimal":        "",
                    "emptyTable":     "Không tìm thấy dữ liệu",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "loadingRecords": " tải...",
                    "processing":     " tải...",
                    "search":         "Tìm kiếm:",
                    "paginate": {
                        "first":      "Đầu",
                        "last":       "Cuối",
                        "next":       "Sau",
                        "previous":   "Trước"
                    },
                    "aria": {
                        "sortAscending":  ": xếp tăng dần",
                        "sortDescending": ": xếp giảm dần"
                    }
                },
                stateSave: true,
            });
        } );

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
            let route = '{{ route('admin.report.by-ship') }}';
            let newParams = '';
            $('#frm_filter_orders input, #frm_filter_orders select').each(
                function(index) {
                    var input = $(this);
                    if (input.attr('name') === 'filter_daterange') {
                        let dateRange = input.val();
                        newParams += '&begin=' + dateRange.substr(0, 10) + '&end=' + dateRange.substr(dateRange.length - 10, 10);
                    }
                });
            window.location = reFormatUriParam(route, newParams);
        }

        let listReport = '{{ json_encode($data) }}';
        let replaceList = listReport.replace(/&quot;/g,'"').replace(/&lt;/g, "<").replace(/&gt;/g, ">");
        let objReport = JSON.parse(replaceList);
        function showDetail(id, name) {
            let aryReport = Object.values(objReport);
            let detailReport = Object.values(aryReport[id]);
            let NewlyCreatedData = [];

            detailReport.forEach(function(item, key) {
                let aryReport = [];
                aryReport.push(item.shop);
                if (typeof item.total != "undefined") {
                    aryReport.push(item.total);
                } else {
                    aryReport.push(0);
                }
                @if( $filter['type'] == 'send')
                    if (typeof item.success != "undefined") {
                        aryReport.push(item.success);
                    } else {
                        aryReport.push(0);
                    }
                    if (typeof item.fail != "undefined") {
                        aryReport.push(item.fail);
                    } else {
                        aryReport.push(0);
                    }
                @else
                    if (typeof item.success_pick != "undefined") {
                        aryReport.push(item.success_pick);
                    } else {
                        aryReport.push(0);
                    }
                    if (typeof item.fail_pick != "undefined") {
                        aryReport.push(item.fail_pick);
                    } else {
                        aryReport.push(0);
                    }
                    if (typeof item.success_refund != "undefined") {
                        aryReport.push(item.success_refund);
                    } else {
                        aryReport.push(0);
                    }
                    if (typeof item.fail_refund != "undefined") {
                        aryReport.push(item.fail_refund);
                    } else {
                        aryReport.push(0);
                    }
                @endif
                NewlyCreatedData.push(aryReport);
            });
            table2.clear().draw();
            table2.rows.add(NewlyCreatedData);
            table2.columns.adjust().draw();
            $('#table2_wrapper').show();
            $('.text-table2').show();
            $('.user-table2').text(name);
        }
        $('document').ready(function () {
            $(".cancelBtn").html("Huỷ bỏ");
            $(".applyBtn").html("Áp dụng");
        });
    </script>
@endsection

@extends('layouts.base')

@section('css')
    <link href="{{ asset('libs/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css" rel="stylesheet">

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
                                <input id="shop_selected" type="hidden" value="{{ $filter['shop_id'] }}">
                                <input type="text" class="form-control form-control-sm frm_filter_orders" id="date_range" name="date_range">&nbsp;
                                <button id="btn_filter_submit" class="btn btn-sm btn-success" type="button">Xem báo cáo</button>&nbsp;
                                <a class="btn btn-sm btn-info" href="{{ route('admin.reports.reconcile-history') }}">Làm lại</a>&nbsp;
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="row" id="tbl-reconcile">
                                <div class="col pt-3">
                                    <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table_shops">
                                        <thead>
                                            <th>STT</th>
                                            <th>Shop</th>
                                            <th class="text-right" style="min-width:95px">Ngày đối soát</th>
                                            <th class="text-right text-danger" style="min-width:75px">Tổng phí<i data-toggle="tooltip" html="true" title="Tổng tiền phí dịch vụ Shop trả cho BigFast" class="fa fa-question-circle ml-1 text-danger"></i></th>
                                            <th class="text-right text-primary" style="min-width:85px">Tổng CoD<i data-toggle="tooltip" html="true" title="Tổng tiền thu hộ nhân viên BigFast thu từ khách hàng" class="fa fa-question-circle ml-1 text-danger"></i></th>
                                            <th class="text-right text-warning" style="min-width:110px">Tiền bồi hoàn<i data-toggle="tooltip" html="true" title="Tiền BigFast trả lại Shop khi xảy ra hư hỏng, thất lạc hàng hoá" class="fa fa-question-circle ml-1 text-danger"></i></th>
                                            <th class="text-right text-success" style="min-width:75px">Tổng dư<i data-toggle="tooltip" html="true" title="Tiền BigFast trả Shop, tính bằng tổng tiền thu hộ cộng tiền bồi hoàn, sau đó trừ đi tổng phí" class="fa fa-question-circle ml-1 text-danger"></i></th>
                                            <th></th>
                                        </thead>
                                        <tbody>
                                        @foreach($arrShop as $key => $value)
                                            <tr>
                                                <td>{{ number_format($key + 1) }}</td>
                                                <td>{!! $value->shop->name !!}</td>
                                                <td class="text-right">{{ date('d-m-Y', strtotime($value->end_date)) }}</td>
                                                <td class="text-right">{{ number_format($value->total_fee) . ' vnđ' }}</td>
                                                <td class="text-right">{{ number_format($value->total_cod) . ' vnđ' }}</td>
                                                <td class="text-right">{{ number_format($value->money_indemnify) . ' vnđ' }}</td>
                                                <td class="text-right">{{ number_format($value->total_du) . ' vnđ' }}</td>
                                                <td class="text-right">
                                                    <a href="{{ route('admin.reports.reconcile-history-export', array(
                                                        'shopId' => $value->shop->id,
                                                        'timeBegin' => $value->begin_date,
                                                        'timeEnd' => $value->end_date,
                                                        'shopName' => $value->shop->name
                                                    )) }}">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
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
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>

<script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('libs/daterangepicker/daterangepicker.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('libs/daterangepicker/daterangepicker.min.css') }}" />

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
        let shopId = document.getElementById('select_shop').value;
        let route = '{{ route('admin.reports.reconcile-history') }}';
        const queryString = window.location.search;
        if (queryString === '') route += '?';
        window.location = route + queryString + '&shop_id=' + shopId;
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
        // dateType = $('#cod_report_table').attr('data-current-display');
        HoldOn.open({theme:"sk-rect"});
        let shopId = $('#shop_selected').val();
        let route = '{{ route('admin.reports.reconcile-history') }}';
        let newParams = '&shop_id=' + shopId;
        $('#shop_selected, #date_range, #cod_report_table').each(
            function(index) {
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
    $(document).ready(function() {
        // bảng hiển thị dữ liệu
        let table = $('#table_shops').DataTable({
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "dom": "Bfrtip",
                "buttons": [
					'pageLength',
                    'excel'
                ],
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
                "loadingRecords": "Đang tải...",
                "processing":     "Đang tải...",
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

        $('#table_shops tbody').on( 'click', 'tr', function () {
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
            }
            else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        } );

        // input date range
        $('input[name="date_range"]').daterangepicker({
            startDate: moment(`{{ $filter['date_range'][0] }}`).format('DD-MM-YYYY'),
            endDate: moment(`{{ $filter['date_range'][1] }}`).format('DD-MM-YYYY'),
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
            startDateFilter = moment(start).format('DD/MM/YYYY');
            endDateFilter = moment(end).format('DD/MM/YYYY');
        });

        // when click "xem bao cao"
        $('#btn_filter_submit').click(function() {
            submitFilterReports();
        });
    });
    $('document').ready(function () {
        $(".cancelBtn").html("Huỷ bỏ");
        $(".applyBtn").html("Áp dụng");
    });
</script>
@endsection

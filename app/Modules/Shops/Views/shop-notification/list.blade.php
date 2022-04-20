@extends('layouts.baseShop')

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
                                <p class="lead">Có <span class="text-danger font-weight-bolder">{{ $notifications["unread_notification"] }}</span> thông báo chưa đọc trong tổng số <span class="text-primary font-weight-bold">{{ $countAllNotification }}</span> thông báo</p>
                            </div>
                            <form id="frm_filter_orders" class="form-inline float-right" method="GET" action="">
                                <input id="shop_selected" type="hidden" value="{{ isset($shopId) ? $shopId : 0 }}">
                                <input type="text" class="form-control form-control-sm frm_filter_orders" id="date_range" name="date_range">&nbsp;
                                <button id="btn_filter_submit" class="btn btn-sm btn-success" type="button">Xem thông báo</button>&nbsp;
                            </form>
                        </div>
                        <form method="GET" action="{{ route('shop.shop-notification-read') }}">
                            <div class="card-body">
                                <div class="row" id="tbl-reconcile">
                                    <div class="col pt-3">
                                        <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table_shops">
                                            <thead>
                                                <th style="width: 100px;" class="pr-0"><input type="checkbox" id="select_all"/>&nbsp;</th>
                                                <th>Người gửi</th>
                                                <th>Nội dung thông báo</th>
                                                <th>Thời gian</th>
                                                <th>Chi tiết</th>
                                            </thead>
                                            <tbody>
                                                @foreach($arrNotification as $key => $notification)
                                                    <tr style="opacity: {{ $notification->is_read ? 0.6 : 1 }}">
                                                        @if($notification->is_read == false)
                                                            <td style="width: 100px; opacity: {{ $notification->is_read ? 0 : 1 }}" class="pr-0"><input type="checkbox" class="single_check_box" name="cbx_notification_id[]" value="{{ $notification->id }}"/>&nbsp;</td>
                                                        @else
                                                            <td style="width: 100px;"> </td>
                                                        @endif
                                                            <td>{{ $notification->notification->user->name }}</td>
                                                        @if($notification->notification->link)
                                                            <td><a target="_blank" href="{{ $notification->notification->link }}">{{ strlen($notification->notification->content) > 150 ? substr($notification->notification->content, 0, strrpos(substr($notification->notification->content, 0, 150), " ")) . "..." : $notification->notification->content }}</a></td>
                                                        @else
                                                            <td>{{ strlen($notification->notification->content) > 150 ? substr($notification->notification->content, 0, strrpos(substr($notification->notification->content, 0, 150), " ")) . "..." : $notification->notification->content }}</td>
                                                        @endif
                                                        <td>{{ date('d-m-Y H:i', strtotime($notification->notification->created_at)) }}</td>
                                                        <td class="text-center" data-toggle="modal" data-target="#modal_table_{{ $notification->id }}" style="cursor: pointer">
                                                            <span class="badge badge-success">Xem</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="modal_container">
                                            @foreach($arrNotification as $key => $notification)
                                                <div class="modal" id="modal_table_{{ $notification->id }}">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-center">Chi tiết thông báo</h5>
                                                                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>{{ $notification->notification->content }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            {{ $arrNotification->links() }}
                                        </div>
                                        <div class="row">
                                            <div class="col-auto mr-auto">
                                                <div id="notification_modal" style="display: none;">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1 ml-3 mr-5">
                                                            <b>Đã chọn</b><br>
                                                            <b id="count_shop_selected" style="font-size: 30px; color: orangered">0</b> Thông báo
                                                        </div>
                                                        <div class="d-flex align-items-end">
                                                            <button class="btn btn-pill btn-primary mr-2" type="submit" id="shop_reconcile">Đánh dấu là đã đọc</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
                        </form>
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
        let route = '{{ route('shop.shop-notification.index') }}';
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
        let route = '{{ route('shop.shop-notification.index') }}';
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
        let table = $('#table_shops_backup').DataTable({
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
            // startDate: moment(),
            startDate: moment(`{{ $filter['date_range'][0] }}`, 'DD-MM-YYYY'),
            // endDate: moment(),
            endDate: moment(`{{ $filter['date_range'][1] }}`, 'DD-MM-YYYY'),
            maxDate: moment(),
            dateLimit: { days: 30 },
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
</script>

<script>
    // check and uncheck shop
    let arrShop = [];

    // mảng chứa các id đã chọn
    let lastChecked = null;
    let checkboxList = $('.single_check_box');

    function logicOrderChecked(checked = false, shop_id = 0) {
        if (checked) {
            if (!arrShop.includes(shop_id)) {
                arrShop.push(shop_id);
            }
        } else {
            if (arrShop.includes(shop_id)) {
                const index = arrShop.indexOf(shop_id);
                if (index > -1) {
                    arrShop.splice(index, 1);
                }
            }
        }
        console.log(arrShop);

        // hiển thị modal
        shopSelectedLength = arrShop.length;
        document.getElementById("count_shop_selected").innerHTML = shopSelectedLength;
        if (shopSelectedLength > 0)
            $("#notification_modal").fadeIn();
        else
            $("#notification_modal").fadeOut();
    }

    // when click select all
    jQuery(function ($) {
        $('body').on('click', '#select_all', function () {
            let checked = this.checked;
            $('#table_shops input').each(
                function (index) {
                    var input = $(this);
                    if (input.attr('class') === 'single_check_box') {
                        logicOrderChecked(checked, input.val());
                    }
                });
            $('.single_check_box').prop('checked', this.checked);
            if (this.checked) {
                window.scrollTo(0, document.body.scrollHeight);
            }
        });

        // when click single checkbox
        $('body').on('click', '.single_check_box', function (e) {
            let input = $(this);
            let checked = input.is(':checked');
            logicOrderChecked(checked, input.val());
            if ($('.single_check_box').length == $('.single_check_box:checked').length) {
                $('#select_all').prop('checked', true);
            } else {
                $("#select_all").prop('checked', false);
            }

            // Checked by Shift - chọn danh sách dùng phím shift
            if (!lastChecked) {
                lastChecked = this;
                return;
            }
            if (e.shiftKey) {
                var start = checkboxList.index(this);
                var end = checkboxList.index(lastChecked);

                checkboxList.slice(Math.min(start, end), Math.max(start, end) + 1).prop('checked', lastChecked.checked);
            }
            lastChecked = this;
        });
    });
    // end of check and uncheck shop
    $('document').ready(function () {
        $(".cancelBtn").html("Huỷ bỏ");
        $(".applyBtn").html("Áp dụng");
    });
</script>
@endsection

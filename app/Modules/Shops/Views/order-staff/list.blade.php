@extends('layouts.baseShop')

@section('css')
    @if (isset($search['searchs']))
        <link href="{{ asset('libs/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">
    @endif
    <style type="text/css">
        select + .select2-container {
            width: 200px !important;
        }

        #modal_aside_left .modal-dialog {
            box-shadow: 0px 0px 20px #aaa;
        }
        .table-responsive {
            overflow: auto;
            max-height: 500px;
        }
    </style>
@endsection
@section('script-header')
    <link rel="stylesheet" href="{{ asset('libs/multiselect/css/bootstrap-multiselect.min.css') }}" type="text/css">
    <script type="text/javascript" src="{{ asset('libs/multiselect/js/bootstrap-multiselect.min.js') }}"></script>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        @include('Shops::order-staff.shared.header')
                        {{--@if($shop)--}}
                        <div class="card-body">

                            @include('Shops::order-staff.shared.count-status')

                            @include('Shops::order-staff.shared.filter')

                            @if ( in_array($filter['status_detail'], array(11, 35, 31, 36) ) )
                                @include('Shops::order-staff.shared.order-item.status-active-11')
                            @elseif ( $filter['status_detail'] === 12)
                                @include('Shops::order-staff.shared.order-item.status-active-12')
                            @elseif ( $filter['status_detail'] === 22)
                                @include('Shops::order-staff.shared.order-item.status-active-22')
                            @elseif ( $filter['status_detail'] === 23)
                                @include('Shops::order-staff.shared.order-item.status-active-23')
                            @elseif ( $filter['status_detail'] === 25)
                                @include('Shops::order-staff.shared.order-item.status-active-25')
                            @elseif ( $filter['status_detail'] === 32)
                                @include('Shops::order-staff.shared.order-item.status-active-32')
                            @elseif ( $filter['status_detail'] === 34)
                                @include('Shops::order-staff.shared.order-item.status-active-34')
                            @elseif ( $filter['status'] === 4)
                                @include('Shops::order-staff.shared.order-item.status-active-4')
                            @elseif ( $filter['status'] === 5)
                                @include('Shops::order-staff.shared.order-item.status-active-5')
                            @elseif ( in_array($filter['status'], array(9) ) || in_array($filter['status_detail'], array(81, 82) ) )
                                @include('Shops::order-staff.shared.order-item.status-active-9')
                            @elseif ( in_array($filter['status_detail'], array(83, 84, 73, 74) ) )
                                @include('Shops::order-staff.shared.order-item.status-active-83')
                            @elseif ( $filter['status'] === 6)
                                @include('Shops::order-staff.shared.order-item.status-active-6')
                            @elseif ( in_array($filter['status_detail'], array(71, 72) ) )
                                @include('Shops::order-staff.shared.order-item.status-active-71')
                            @else
                                @include('Shops::order-staff.shared.order-item')
                            @endif

                            @if (!isset($search['searchs']))
                            <div class="row">
                                <div class="col-auto mb-3">
                                    @include('Shops::order-staff.shared.order-actions')
                                </div>
                                <div class="col-auto ml-auto">
                                    {{ $orders->withQueryString()->links() }}
                                </div>
                            </div>
                            @endif

                            @include('Shops::order-staff.shared.right')

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('Shops::contacts-staff.shared.modal-create')

@endsection


@section('javascript')
    <script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/daterangepicker/daterangepicker.min.css') }}" />
    @if (isset($search['searchs']))
        <script type="text/javascript" src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>
    @endif

    <script type="application/javascript">
        let arrOrder = [];
        let lastChecked = null;
        let $chkboxes = $('.singlechkbox');
        let statusDetail = '{{ $filter['status_detail'] }}';
        let fileFails = '{{ $fileFails }}';

        if (fileFails !== '') {
	        window.open('{{ route('shop.orders.download') }}', '_blank');
        }

        jQuery(function($) {
            $('body').on('click', '#selectall', function() {
                let checked = this.checked;
                $('#table_orders input').each(
                    function(index){
                        var input = $(this);
                        if (input.attr('class') === 'singlechkbox') {
                            // logicCookieOrder(checked, input.val());
                            logicOrderChecked(checked, input.val());
                        }
                    });
                $('.singlechkbox').prop('checked', this.checked);
                if (this.checked) {
                    window.scrollTo(0,document.body.scrollHeight);
                }
            });

            $('body').on('click', '.singlechkbox', function(e) {
                let input = $(this);
                let checked = input.is(':checked');
                // logicCookieOrder(checked, input.val());
                logicOrderChecked(checked, input.val());
                if($('.singlechkbox').length == $('.singlechkbox:checked').length) {
                    $('#selectall').prop('checked', true);
                } else {
                    $("#selectall").prop('checked', false);
                }

                // Checked by Shift
                if (!lastChecked) {
                    lastChecked = this;
                    return;
                }
                if (e.shiftKey) {
                    var start = $chkboxes.index(this);
                    var end = $chkboxes.index(lastChecked);

                    $chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked);
                }
                lastChecked = this;
            });
        });

        $(document).ready(function() {
            let timeleft = 10;
            let startDateFilter;
            let endDateFilter;

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
                    }
                }
            });

	        $('#search_status').multiselect({
		        maxHeight : 400,
		        // enableFiltering: true,
		        enableClickableOptGroups: true,
		        nonSelectedText: 'Vui lòng chọn trạng thái',
		        onChange: function(option, checked) {
			        // Get selected options.
			        let statusDetailArr = [];
			        let selectedOptions = $('#search_status option:selected');
			        selectedOptions.each(function() {
				        statusDetailArr.push($(this).val());
			        });
			        document.getElementById("statusList").value = statusDetailArr.join();
		        },
		        buttonText: function(options, select) {
			        if (options.length === 0) {
				        return 'Vui lòng chọn trạng thái!';
			        }
			        else {
				        return 'Có ' + options.length + ' trạng thái được chọn!';
			        }
		        }
	        });

	        $('input[id="search_daterange"]').daterangepicker({
                        @if($search['created_range'][0])
				        startDate: moment(`{{ $search['created_range'][0] }}`),
			        endDate: moment(`{{ $search['created_range'][1] }}`),
                        @else
				        autoUpdateInput: false,
                        @endif
				        opens: 'left',
			        maxDate: moment(),
			        dateLimit: { days: 60 },
			        buttonClasses: ['btn btn-default'],
			        applyClass: 'btn-small btn-primary',
			        cancelClass: 'btn-small',
			        separator: ' to ',
			        locale: {
				        format: 'DD-MM-YYYY',
				        daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6','T7'],
				        monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
				        firstDay: 1
			        }
		        },
		        function(start, end) {
			        document.getElementById("search_created_from").value = start.format('DD-MM-YYYY');
			        document.getElementById("search_created_to").value = end.format('DD-MM-YYYY');
		        });

	        $('input[id="search_daterange2"]').daterangepicker({
                @if($search['send_success_range'][0])
		        startDate: moment(`{{ $search['send_success_range'][0] }}`),
		        endDate: moment(`{{ $search['send_success_range'][1] }}`),
                @else
		        autoUpdateInput: false,
                @endif
		        opens: 'left',
		        drops: 'up',
		        maxDate: moment(),
		        dateLimit: { days: 60 },
		        buttonClasses: ['btn btn-default'],
		        applyClass: 'btn-small btn-primary',
		        cancelClass: 'btn-small',
		        separator: ' to ',
		        locale: {
			        format: 'DD-MM-YYYY',
			        daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6','T7'],
			        monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
			        firstDay: 1
		        }
	        }).on('apply.daterangepicker', function(ev, picker) {
		        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
		        document.getElementById("search_send_success_from").value = picker.startDate.format('DD-MM-YYYY');
		        document.getElementById("search_send_success_to").value = picker.endDate.format('DD-MM-YYYY');
	        }).on('cancel.daterangepicker', function(ev, picker) {
		        $(this).val('');
		        document.getElementById("search_send_success_from").value = '';
		        document.getElementById("search_send_success_to").value = '';
	        });

	        $('input[id="search_daterange3"]').daterangepicker({
                @if($search['collect_money_range'][0])
		        startDate: moment(`{{ $search['collect_money_range'][0] }}`),
		        endDate: moment(`{{ $search['collect_money_range'][1] }}`),
                @else
		        autoUpdateInput: false,
                @endif
		        opens: 'left',
		        drops: 'up',
		        maxDate: moment(),
		        dateLimit: { days: 60 },
		        buttonClasses: ['btn btn-default'],
		        applyClass: 'btn-small btn-primary',
		        cancelClass: 'btn-small',
		        separator: ' to ',
		        locale: {
			        format: 'DD-MM-YYYY',
			        daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6','T7'],
			        monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
			        firstDay: 1
		        }
	        }).on('apply.daterangepicker', function(ev, picker) {
		        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
		        document.getElementById("search_collect_money_from").value = picker.startDate.format('DD-MM-YYYY');
		        document.getElementById("search_collect_money_to").value = picker.endDate.format('DD-MM-YYYY');
	        }).on('cancel.daterangepicker', function(ev, picker) {
		        $(this).val('');
		        document.getElementById("search_collect_money_from").value = '';
		        document.getElementById("search_collect_money_to").value = '';
	        });

	        $('input[id="search_daterange4"]').daterangepicker({
                @if($search['reconcile_send_range'][0])
		        startDate: moment(`{{ $search['reconcile_send_range'][0] }}`),
		        endDate: moment(`{{ $search['reconcile_send_range'][1] }}`),
                @else
		        autoUpdateInput: false,
                @endif
		        opens: 'left',
		        drops: 'up',
		        maxDate: moment(),
		        dateLimit: { days: 60 },
		        buttonClasses: ['btn btn-default'],
		        applyClass: 'btn-small btn-primary',
		        cancelClass: 'btn-small',
		        separator: ' to ',
		        locale: {
			        format: 'DD-MM-YYYY',
			        daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6','T7'],
			        monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
			        firstDay: 1
		        }
	        }).on('apply.daterangepicker', function(ev, picker) {
		        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
		        document.getElementById("search_reconcile_send_from").value = picker.startDate.format('DD-MM-YYYY');
		        document.getElementById("search_reconcile_send_to").value = picker.endDate.format('DD-MM-YYYY');
	        }).on('cancel.daterangepicker', function(ev, picker) {
		        $(this).val('');
		        document.getElementById("search_reconcile_send_from").value = '';
		        document.getElementById("search_reconcile_send_to").value = '';
	        });

	        $('input[id="search_daterange5"]').daterangepicker({
                @if($search['reconcile_refund_range'][0])
		        startDate: moment(`{{ $search['reconcile_refund_range'][0] }}`),
		        endDate: moment(`{{ $search['reconcile_refund_range'][1] }}`),
                @else
		        autoUpdateInput: false,
                @endif
		        opens: 'left',
		        drops: 'up',
		        maxDate: moment(),
		        dateLimit: { days: 60 },
		        buttonClasses: ['btn btn-default'],
		        applyClass: 'btn-small btn-primary',
		        cancelClass: 'btn-small',
		        separator: ' to ',
		        locale: {
			        format: 'DD-MM-YYYY',
			        daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6','T7'],
			        monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
			        firstDay: 1
		        }
	        }).on('apply.daterangepicker', function(ev, picker) {
		        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
		        document.getElementById("search_reconcile_refund_from").value = picker.startDate.format('DD-MM-YYYY');
		        document.getElementById("search_reconcile_refund_to").value = picker.endDate.format('DD-MM-YYYY');
	        }).on('cancel.daterangepicker', function(ev, picker) {
		        $(this).val('');
		        document.getElementById("search_reconcile_refund_from").value = '';
		        document.getElementById("search_reconcile_refund_to").value = '';
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

                HoldOn.open({theme:"sk-rect"});
                let route = '{{ route('shop.order-staff.index') }}';
                let newParams = '&begin=' + start.format('DD-MM-YYYY') + '&end=' + end.format('DD-MM-YYYY');
                window.location = reFormatUriParam(route, newParams);
            });

            $('#filter_limit').on('change', function() {
                HoldOn.open({theme:"sk-rect"});
                let route = '{{ route('shop.order-staff.index') }}';
                window.location = reFormatUriParam(route, '&limit=' + this.value);
            });

            $('#btnExportExcel').click(function () {
                console.log(arrOrder);
                let route = '{{ route('shop.order-staff.export') }}';
                window.location = reFormatUriParam(route, '&orders=' + arrOrder.toString());
                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
            })
        });

        function submitFilterOrders() {
            // sk-rect, sk-dot, sk-cube, sk-bounce, sk-circle
            HoldOn.open({theme:"sk-rect"});
            let route = '{{ route('shop.order-staff.index') }}';
            let newParams = '';
            $('#frm_filter_orders input, #frm_filter_orders select').each(
                function(index) {
                    var input = $(this);
                    if (input.attr('name') === 'filter_status_detail') {
                        if (parseInt(input.val()) > 0) {
                            newParams += '&status_detail=' + input.val();
                        } else {
                            newParams += '&status_detail=0';
                        }
                    }
                    if (input.attr('name') === 'filter_daterange') {
                        let dateRange = input.val();
                        newParams += '&begin=' + dateRange.substr(0, 10) + '&end=' + dateRange.substr(dateRange.length - 10, 10);
                    }
                });
            window.location = reFormatUriParam(route, newParams);
        }

        function logicOrderChecked(checked = false, order_id = 0) {
            if (checked) {
                if (!arrOrder.includes(order_id)) {
                    arrOrder.push(order_id);
                }
            } else {
                if (arrOrder.includes(order_id)) {
                    const index = arrOrder.indexOf(order_id);
                    if (index > -1) {
                        arrOrder.splice(index, 1);
                    }
                }
            }
            document.getElementById("countOrderSelected").innerHTML = arrOrder.length;

            if (arrOrder.length > 0)
                $("#boxActions").fadeIn();
            else
                $("#boxActions").fadeOut();
        }

        function logicCookieOrder(checked = false, order_id = 0) {
            let ordersCookie = $.cookie('ordersCookie');
            if (!ordersCookie) {
                ordersCookie = JSON.stringify([]);
                $.cookie('ordersCookie', ordersCookie);
            }

            let arrOrder = JSON.parse(ordersCookie);
            if (checked) {
                if (!arrOrder.includes(order_id)) {
                    arrOrder.push(order_id);
                    console.log('add ' + order_id);
                }
            } else {
                if (arrOrder.includes(order_id)) {
                    const index = arrOrder.indexOf(order_id);
                    if (index > -1) {
                        arrOrder.splice(index, 1);
                        console.log('del ' + order_id);
                    }
                }
            }
            $.cookie('ordersCookie', JSON.stringify(arrOrder));
            document.getElementById("countOrderSelected").innerHTML = arrOrder.length;

            if (arrOrder.length > 0)
                $("#boxActions").fadeIn();
            else
                $("#boxActions").fadeOut();
        }

        function reFormatUriParam(route = '', params = '') {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);

            const newParams = new URLSearchParams(params);
            const newKeys = newParams.keys();
            for (const key of newKeys) {
                if (parseInt(newParams.get(key)) !== -1) {
                    urlParams.set(key, newParams.get(key));
                }
            }
            return route + '?' + urlParams.toString();
        }

        function removeParam(keys) {
	        let sourceURL = window.location.href;
	        let rtn = sourceURL.split("?")[0],
		        param,
		        params_arr = [],
		        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
	        if (queryString !== "") {
		        params_arr = queryString.split("&");
		        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
			        param = params_arr[i].split("=")[0];
			        keys.forEach(function (key) {
				        if (param === key) {
					        params_arr.splice(i, 1);
				        }
			        })
		        }
		        if (params_arr.length) rtn = rtn + "?" + params_arr.join("&");
	        }
	        return rtn;
        }

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

        function shopSelectedChange() {
            HoldOn.open({theme:"sk-rect"});
            let shopSelected = document.getElementById('shopSelected').value;
            let route = '{{ route('shop.order-staff.index') }}';
            {{--let route = '{{ route('shop.order-staff.index', ":slug") }}';--}}
            // route = route.replace(':slug', shopSelected);
            const queryString = window.location.search;
            if (queryString === '') route += '?';
            window.location = route + queryString + '&shop=' + shopSelected;
        }

        function shopSelectedRedis(shopSelected) {
            HoldOn.open({theme:"sk-rect"});
            let route = '{{ route('shop.order-staff.index') }}';

            const queryString = window.location.search;
            if (queryString === '') route += '?';
            window.location = route + queryString + '&shop=' + shopSelected;
        }

        function formatRepoSelection (repo) {
            if (repo.id === '') return 'Tìm kiếm Shop cần quản lý';
            return repo.name || repo.phone;
        }
        function printListOrder(type) {
            window.open(
                '{{ route('shop.print-orders.print') }}?page_size=' + type + '&order_id=' + arrOrder.join(','),
                '_blank'
            );
        }
        function printOrder(id, type) {
            window.open(
                '{{ route('shop.print-orders.print') }}?page_size=' + type + '&order_id=' + id,
                '_blank'
            );
        }
        $('document').ready(function () {
            $(".cancelBtn").html("Huỷ bỏ");
            $(".applyBtn").html("Áp dụng");
        });
    </script>
@endsection


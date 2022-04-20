@extends('layouts.base')

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

        .order_status_container .btn-group {
            width: 100%;
        }

        .order_status_container .btn-group button {
            text-align: left;
        }

        @media screen and (max-width: 575px) {
            .order_navigation_container {
                display: block !important;
            }

            select + .select2-container {
                width: 100% !important;
            }

            .action_container button {
                width: 100%;
                margin: 8px 0px;
            }

            .action_container a {
                width: 100%;
                margin: 0px;
                padding: 0px;
            }

            .excel_img img {
                float: right;
            }

            .status_container a {
                width: 100%;
            }

            #frm_filter_orders {
                display: block;
            }

            #frm_filter_orders .icon_container {
                display: none;
            }

            #filter_status_detail,
            #filter_daterange,
            #btn_filter_submit {
                width: 100%;
            }

            #filter_limit {
                width: 60px;
            }

            .filter_container {
                display: block;
            }
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
                        @include('Orders::orders.shared.header')
                        {{--@if($shop)--}}
                        <div class="card-body">

                            @include('Orders::orders.shared.count-status')

                            @include('Orders::orders.shared.filter')

                            @if ( in_array($filter['status_detail'], array(11, 35, 31, 36) ) )
                                @include('Orders::orders.shared.order-item.status-active-11')
                            @elseif ( $filter['status_detail'] === 12)
                                @include('Orders::orders.shared.order-item.status-active-12')
                            @elseif ( $filter['status_detail'] === 22)
                                @include('Orders::orders.shared.order-item.status-active-22')
                            @elseif ( $filter['status_detail'] === 23)
                                @include('Orders::orders.shared.order-item.status-active-23')
                            @elseif ( $filter['status_detail'] === 25)
                                @include('Orders::orders.shared.order-item.status-active-25')
                            @elseif ( $filter['status_detail'] === 32)
                                @include('Orders::orders.shared.order-item.status-active-32')
                            @elseif ( $filter['status_detail'] === 34)
                                @include('Orders::orders.shared.order-item.status-active-34')
                            @elseif ( $filter['status'] === 4)
                                @include('Orders::orders.shared.order-item.status-active-4')
                            @elseif ( $filter['status'] === 5)
                                @include('Orders::orders.shared.order-item.status-active-5')
                            @elseif ( in_array($filter['status'], array(9) ) || in_array($filter['status_detail'], array(81, 82) ) )
                                @include('Orders::orders.shared.order-item.status-active-9')
                            @elseif ( in_array($filter['status_detail'], array(83, 84, 73, 74) ) )
                                @include('Orders::orders.shared.order-item.status-active-83')
                            @elseif ( $filter['status'] === 6)
                                @include('Orders::orders.shared.order-item.status-active-6')
                            @elseif ( in_array($filter['status_detail'], array(71, 72) ) )
                                @include('Orders::orders.shared.order-item.status-active-71')
                            @else
                                @include('Orders::orders.shared.order-item')
                            @endif 

                            @if (!isset($search['searchs']))
                            <div class="row">
                                <div class="col-auto mb-3">
                                    @include('Orders::orders.shared.order-actions')
                                </div>
                                <div class="col-auto ml-auto">
                                    {{ $orders->withQueryString()->links() }}
                                </div>
                            </div>
                            @endif

                            @include('Orders::orders.shared.right')

                            <div class="modal fade" id="orderActionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-info modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Thao tác <b id="countOrderSelectedModal" style="color: orangered">0</b> đơn hàng đơn được chọn</h4>
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                        </div>
                                        <div class="modal-body" id="modalBodyContent">
                                            @if($filter['status_detail'])
                                            <div class="nav-tabs-boxed nav-tabs-boxed-left">
                                                <ul class="nav nav-tabs" role="tablist" style="width: 20%">
                                                    @php
                                                    $count = 0;
                                                    @endphp
                                                    @foreach( $orderConstantStatus[$filter['status']]['detail'][$filter['status_detail']]['next'] as $key => $value)
                                                        <li class="nav-item">
                                                            <a class="nav-link {{ $count ? '' : 'active' }}" data-toggle="tab" href="#tab{{ $key  }}"
                                                               role="tab" aria-controls="tab{{ $key }}" aria-selected="{{ $count ? false : true }}">{{ $value }}</a></li>
                                                        @php
                                                            $count++;
                                                        @endphp
                                                    @endforeach
                                                </ul>
                                                <div class="tab-content" style="width: 80%">
                                                    @php
                                                        $count = 0;
                                                    @endphp
                                                    @foreach( $orderConstantStatus[$filter['status']]['detail'][$filter['status_detail']]['next'] as $key => $value)
                                                        <div class="tab-pane {{ $count ? '' : 'active' }}" id="tab{{ $key }}" role="tabpanel">
                                                            <div class="row justify-content-md-center">
                                                                <div class="col-10">
                                                                    @if($key===12)
                                                                        @if($filter['status_detail']==13)
                                                                            @include('Orders::orders.modal.re-shipper-pick')
                                                                        @else
                                                                            @include('Orders::orders.modal.assign-shipper-pick')
                                                                        @endif
                                                                    @endif
                                                                    @if($key===13)
                                                                        @include('Orders::orders.modal.pick-fail')
                                                                    @endif
                                                                    @if($key===21)
                                                                        @include('Orders::orders.modal.pick-success')
                                                                    @endif
                                                                    @if($key===22)
                                                                        @include('Orders::orders.modal.warehouse')
                                                                    @endif
                                                                    @if($key===23)
                                                                        @if($filter['status_detail']==34 || $filter['status_detail']==41)
                                                                            @include('Orders::orders.modal.re-shipper-send')
                                                                        @else
                                                                            @include('Orders::orders.modal.assign-shipper-send')
                                                                        @endif
                                                                    @endif
                                                                    @if($key===24)
                                                                        @include('Orders::orders.modal.send-fail')
                                                                    @endif
                                                                    @if($key===25)
                                                                        @include('Orders::orders.modal.store')
                                                                    @endif
                                                                    @if($key===31)
                                                                        @include('Orders::orders.modal.set-refund')
                                                                    @endif
                                                                    @if($key===32)
                                                                        @include('Orders::orders.modal.assign-shipper-refund')
                                                                    @endif
                                                                    @if($key===33)
                                                                        @include('Orders::orders.modal.refund-fail')
                                                                    @endif
                                                                    @if($key===34)
                                                                        @include('Orders::orders.modal.confirm-refund')
                                                                    @endif
                                                                    @if($key===35)
                                                                        @include('Orders::orders.modal.approval-refund')
                                                                    @endif
                                                                    @if($key===36)
                                                                        @include('Orders::orders.modal.warehouse-refund')
                                                                    @endif
                                                                    @if($key===41)
                                                                        @include('Orders::orders.modal.confirm-resend')
                                                                    @endif
                                                                    @if($key===51)
                                                                        @include('Orders::orders.modal.send-success')
                                                                    @endif
                                                                    @if($key===52)
                                                                        @include('Orders::orders.modal.refund-success')
                                                                    @endif
                                                                    @if($key===61)
                                                                        @include('Orders::orders.modal.cancel-orders')
                                                                    @endif
                                                                    @if($key===71)
                                                                        @include('Orders::orders.modal.missing')
                                                                    @endif
                                                                    @if($key===72)
                                                                        @include('Orders::orders.modal.damaged')
                                                                    @endif
                                                                    @if($key===73)
                                                                        @include('Orders::orders.modal.missing-confirm')
                                                                    @endif
                                                                    @if($key===74)
                                                                        @include('Orders::orders.modal.damaged-confirm')
                                                                    @endif
                                                                    @if($key===81)
                                                                        @include('Orders::orders.modal.reconcile-send')
                                                                    @endif
                                                                    @if($key===82)
                                                                        @include('Orders::orders.modal.reconcile-refund')
                                                                    @endif
                                                                    @if($key===83)
                                                                        @include('Orders::orders.modal.reconcile-missing')
                                                                    @endif
                                                                    @if($key===84)
                                                                        @include('Orders::orders.modal.reconcile-damaged')
                                                                    @endif
                                                                    @if($key===91)
                                                                        @include('Orders::orders.modal.collect-money')
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $count++;
                                                        @endphp
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif

                                        </div>
                                    </div>
                                    <!-- /.modal-content-->
                                </div>
                                <!-- /.modal-dialog-->
                            </div>

                        </div>
                        {{--@else--}}
                        {{--<div class="card-body">--}}
                        {{--Vui lòng chọn Shop để thực hiện thao tác--}}
                        {{--</div>--}}
                        {{--@endif--}}
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
    @if (isset($search['searchs']))
    <script type="text/javascript" src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>
    @endif

    <script type="application/javascript">
        let arrOrder = [];
        let lastChecked = null;
        let $chkboxes = $('.singlechkbox');
        let statusDetail = parseInt('{{ $filter['status_detail'] }}');
        let arrOrderStatus = JSON.parse('{!! json_encode($arrOrderStatus) !!}');
        let arrOrderStatusDetail = JSON.parse('{!! json_encode($arrOrderStatusDetail) !!}');
        let arrOrderConstantStatus = JSON.parse('{!! json_encode($arrStatusDetail) !!}');
        let orderConstantStatus = JSON.parse('{!! json_encode($orderConstantStatus) !!}');
        var orderTotalFee = JSON.parse('{!! json_encode($orderTotalFee) !!}');
        var orderTotalCod = JSON.parse('{!! json_encode($orderTotalCod) !!}');
        let user_id = '{{ $userId }}';
        let user_type = '{{ $userType }}';
        let fileFails = '{{ $fileFails }}';
        let total = '{{ $total }}';

        if (fileFails !== '') {
            window.open('{{ route('admin.orders.download') }}', '_blank');
        }

        jQuery(function($) {
            $('body').on('click', '#selectall', function() {
                let checked = this.checked;
                $('#table_orders input').each(
                    function(index){
                        var input = $(this);
                        if (input.attr('class') === 'singlechkbox') {
                            logicOrderChecked(checked, input.val());
                        }
                    });
                actionByOrderChecked();
                $('.singlechkbox').prop('checked', this.checked);
                if (this.checked) {
                    window.scrollTo(0,document.body.scrollHeight);
                }
            });

            $('body').on('click', '.singlechkbox', function(e) {
                let input = $(this);
                let checked = input.is(':checked');
                logicOrderChecked(checked, input.val());
                actionByOrderChecked();
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

            // let ordersCookie = $.cookie('ordersCookie');
            // if (ordersCookie) {
            //     let arrOrder = JSON.parse(ordersCookie);
            //     if (arrOrder.length > 0) {
            //         $("#boxActions").fadeIn();
            //         $('#table_orders input').each(
            //             function(index) {
            //                 var input = $(this);
            //                 if (input.attr('class') === 'singlechkbox') {
            //                     if (arrOrder.includes(input.val())) {
            //                         input.prop('checked', true);
            //                     }
            //                 }
            //             });
            //
            //         document.getElementById("countOrderSelected").innerHTML = arrOrder.length;
            //         if($('.singlechkbox').length == $('.singlechkbox:checked').length) {
            //             $('#selectall').prop('checked', true);
            //         } else {
            //             $("#selectall").prop('checked', false);
            //         }
            //     }
            // }

            $('#orderActionModal').on('show.coreui.modal', function (e) {
                if (statusDetail == '11') {
                    let routeApi = `{{ route('api.user.find-by-roles', array('roles' => 'pickup')) }}`;
                    $.ajax({
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        },
                        url: routeApi,
                        success: function(response){
                            if (response.status_code === 200) {
                                let html = '';
                                response.data.forEach(function (item) {
                                    html += '<option value="'+ item.id +'">'+ item.name + ' (' + item.email +')</option>';
                                });
                                document.getElementById("select_shipper_pick").innerHTML = html;
                            }
                        }
                    });
                }

                if (statusDetail == '36' || statusDetail == '31') {
                    let routeApi = `{{ route('api.user.find-by-roles', array('roles' => 'refund')) }}`;
                    $.ajax({
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        },
                        url: routeApi,
                        success: function(response){
                            if (response.status_code === 200) {
                                let html = '';
                                response.data.forEach(function (item) {
                                    html += '<option value="'+ item.id +'">'+ item.name + ' (' + item.email +')</option>';
                                });
                                document.getElementById("select_pickup_refund").innerHTML = html;
                            }
                        }
                    });
                }

                if (statusDetail == '22' || statusDetail == '25') {
                    let routeApi = `{{ route('api.user.find-by-roles', array('roles' => 'shipper')) }}`;
                    $.ajax({
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        },
                        url: routeApi,
                        success: function(response){
                            if (response.status_code === 200) {
                                let html = '';
                                response.data.forEach(function (item) {
                                    html += '<option value="'+ item.id +'">'+ item.name + ' (' + item.email +')</option>';
                                });
                                document.getElementById("select_shipper_send").innerHTML = html;
                            }
                        }
                    });
                }

                if (statusDetail == '12' || statusDetail == '22' || statusDetail == '33' || statusDetail == '35' || statusDetail == '41') {
                    let routeApi = `{{ route('api.post-offices.get-by-zone') }}`;
                    $.ajax({
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        },
                        url: routeApi,
                        success: function(response){
                            if (response.status_code === 200) {
                                let html = '';
                                response.data.forEach(function (item) {
                                    html += '<option value="'+ item.id +'">'+ item.name + '</option>';
                                });

                                if (statusDetail == '12') {
                                    document.getElementById("post_office").innerHTML = html;
                                }
                                if (statusDetail == '22') {
	                                document.getElementById("post_office").innerHTML = html;
                                    document.getElementById("post_office_store").innerHTML = html;
                                }
                                if (statusDetail == '33') {
	                                document.getElementById("post_office_store").innerHTML = html;
                                }
                                if (statusDetail == '35') {
                                    document.getElementById("post_office").innerHTML = html;
                                }
                                if (statusDetail == '41') {
                                    document.getElementById("post_office").innerHTML = html;
                                }
                            }
                        }
                    });

                    routeApi = `{{ route('api.user.find-by-roles', array('roles' => 'accountancy')) }}`;
                    $.ajax({
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        },
                        url: routeApi,
                        success: function(response){
                            if (response.status_code === 200) {
                                let html = '';
                                response.data.forEach(function (item) {
                                    html += '<option value="'+ item.id +'">'+ item.name + ' (' + item.email +')</option>';
                                });

                                if (statusDetail == '12') {
                                    document.getElementById("select_user_receiver").innerHTML = html;
                                }
                                if (statusDetail == '22') {
	                                document.getElementById("select_user_receiver").innerHTML = html;
                                    document.getElementById("select_user_receiver_store").innerHTML = html;
                                }
                                if (statusDetail == '33') {
	                                document.getElementById("select_user_receiver_store").innerHTML = html;
                                }
                                if (statusDetail == '35') {
                                    document.getElementById("select_user_receiver").innerHTML = html;
                                }
                                if (statusDetail == '41') {
                                    document.getElementById("select_user_receiver").innerHTML = html;
                                }
                            }
                        }
                    });
                }

            });
        });
        $(document).ready(function() {
            let timeleft = 10;
            let startDateFilter;
            let endDateFilter;

            @if (isset($search['searchs']))
            $('#table_orders').DataTable({
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ bản ghi mỗi trang",
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "info": "_PAGE_/_PAGES_ trang",
                    "infoEmpty": "Không tìm thấy dữ liệu",
                    "infoFiltered": "(tìm kiếm trong tổng số _MAX_ bản ghi)",
                    "decimal": "",
                    "emptyTable": "Không tìm thấy dữ liệu",
                    "infoPostFix": "",
                    "thousands": ",",
                    "loadingRecords": "Đang tải...",
                    "processing": "Đang tải...",
                    "search": "Tìm kiếm:",
                    "paginate": {
                        "first": "Đầu",
                        "last": "Cuối",
                        "next": "Sau",
                        "previous": "Trước"
                    },
                    "aria": {
                        "sortAscending": ": xếp tăng dần",
                        "sortDescending": ": xếp giảm dần"
                    }
                },
                stateSave: true,
            });
            @endif

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

            $('document').ready(function () {
                $(".cancelBtn").html("Huỷ bỏ");
                $(".applyBtn").html("Áp dụng");
            });

            @if(isset($filter['created_range']))
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
                HoldOn.open({theme:"sk-rect"});
                let route = '{{ route('admin.orders.index') }}';
                let newParams = '&begin=' + start.format('DD-MM-YYYY') + '&end=' + end.format('DD-MM-YYYY');
                window.location = reFormatUriParam(route, newParams);
            });
            @endif

            @if(isset($filter['created_store_range']))
            $('input[name="filter_store_daterange"]').daterangepicker({
                startDate: moment(`{{ $filter['created_store_range'][0] }}`),
                endDate: moment(`{{ $filter['created_store_range'][1] }}`),
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
                HoldOn.open({theme:"sk-rect"});
                let route = '{{ route('admin.orders.index') }}';
                let newParams = '&store_begin=' + start.format('DD-MM-YYYY') + '&store_end=' + end.format('DD-MM-YYYY');
                window.location = reFormatUriParam(route, newParams);
            });
            @endif

            function redirectNext(status, statusDetail) {
                if ( parseInt(total) == parseInt(arrOrder.length) ) {
                    let route = '{{ route('admin.orders.index') }}';
                    let newParams = '&status=' + status + '&status_detail=' + statusDetail;
                    window.location = reFormatUriParam(route, newParams);
                } else {
                    location.reload();
                }
            }

            $("#frm_store").on('submit', function(e){
                e.preventDefault();
	            //
	            let store_note = document.getElementById("store_note").value;
                let post_office = document.getElementById("post_office_store").value;
                let select_user_receiver = document.getElementById("select_user_receiver_store").value;

                let data = {"status_detail": 25, "orders": arrOrder, "post_office": post_office, "store_note": store_note, "select_user_receiver": select_user_receiver}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(2, 25) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_warehouse").on('submit', function(e){
                e.preventDefault();
                //
                let post_office = document.getElementById("post_office").value;
                let warehouse_note = document.getElementById("warehouse_note").value;
                let select_user_receiver = document.getElementById("select_user_receiver").value;

                let data = {"status_detail": 22, "orders": arrOrder, "post_office": post_office, "warehouse_note": warehouse_note, "select_user_receiver": select_user_receiver}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(2, 22) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_warehouse_refund").on('submit', function(e){
                e.preventDefault();
                //
                let post_office = document.getElementById("post_office").value;
                let warehouse_note = document.getElementById("warehouse_note").value;
                let select_user_receiver = document.getElementById("select_user_receiver").value;

                let data = {"status_detail": 36, "orders": arrOrder, "post_office": post_office, "warehouse_note": warehouse_note, "select_user_receiver": select_user_receiver}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(3, 36) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_confirm_resend").on('submit', function(e){
                e.preventDefault();
                //
                let data = {"status_detail": 41, "orders": arrOrder}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(4, 41) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_confirm_refund").on('submit', function(e){
                e.preventDefault();
                //
                let confirm_refund_reason = document.getElementById("confirm_refund_reason").value;
                let data = {"status_detail": 34, "orders": arrOrder, "confirm_refund_reason": confirm_refund_reason}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(3, 34) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_select_shipper_pick").on('submit', function(e){
                e.preventDefault();
                //
                var select_shipper = document.getElementById("select_shipper_pick").value;
                let data = {"status_detail": 12, "select_shipper": select_shipper, "orders": arrOrder}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(1, 12) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_re_shipper_pick").on('submit', function(e){
                e.preventDefault();
                //
                let data = {"status_detail": 12, "select_shipper": -1, "orders": arrOrder}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(1, 12) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_re_shipper_send").on('submit', function(e){
                e.preventDefault();
                //
                let data = {"status_detail": 23, "select_shipper": -1, "orders": arrOrder}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(2, 23) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_select_shipper_refund").on('submit', function(e){
                e.preventDefault();
                //
                var select_shipper = document.getElementById("select_pickup_refund").value;
                let data = {"status_detail": 32, "select_shipper": select_shipper, "orders": arrOrder}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(3, 32) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_select_shipper_send").on('submit', function(e){
                e.preventDefault();
                //
                var select_shipper = document.getElementById("select_shipper_send").value;
                let data = {"status_detail": 23, "select_shipper": select_shipper, "orders": arrOrder}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(2, 23) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_cancel_orders").on('submit', function(e){
                e.preventDefault();
                //
                let cancel_reason = document.getElementById("cancel_reason").value;
                let cancel_note = document.getElementById("cancel_note").value;

                let data = {"status_detail": 61, "orders": arrOrder, "cancel_reason": cancel_reason, "cancel_note": cancel_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(6, 61) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_set_refund").on('submit', function(e){
                e.preventDefault();
                //
                let refund_reason = document.getElementById("refund_reason").value;
                let refund_note = document.getElementById("refund_note").value;

                let data = {"status_detail": 31, "orders": arrOrder, "refund_reason": refund_reason, "refund_note": refund_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(3, 31) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_approval_refund").on('submit', function(e){
                e.preventDefault();
                //
                let data = {"status_detail": 35, "orders": arrOrder}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(3, 35) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_refund_fail").on('submit', function(e){
                e.preventDefault();
                //
                let fail_note = document.getElementById("fail_note").value;
                let data = {"status_detail": 33, "orders": arrOrder, "fail_note": fail_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(3, 33) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_send_fail").on('submit', function(e){
                e.preventDefault();
                //
                let fail_note = document.getElementById("fail_note").value;

                let data = {"status_detail": 24, "orders": arrOrder, "fail_note": fail_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(2, 24) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_pick_fail").on('submit', function(e){
                e.preventDefault();
                //
                let fail_note = document.getElementById("fail_note").value;

                let data = {"status_detail": 13, "orders": arrOrder, "fail_note": fail_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(1, 13) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_send_success").on('submit', function(e){
                e.preventDefault();
                //
                let data = {"status_detail": 51, "orders": arrOrder}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(5, 51) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_refund_success").on('submit', function(e){
                e.preventDefault();
                //
                let data = {"status_detail": 52, "orders": arrOrder}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(5, 52) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_pick_success").on('submit', function(e){
                e.preventDefault();
                //
                let data = {"status_detail": 21, "orders": arrOrder}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(2, 21) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_collect_money").on('submit', function(e){
                e.preventDefault();
                //
                let collect_money_note = document.getElementById("collect_money_note").value;
                let data = {"status_detail": 91, "orders": arrOrder, "collect_money_note": collect_money_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(9, 91) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_reconcile_send").on('submit', function(e){
                e.preventDefault();
                //
                let reconcile_note = document.getElementById("reconcile_note").value;
                let data = {"status_detail": 81, "orders": arrOrder, "reconcile_note": reconcile_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(8, 81) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_reconcile_refund").on('submit', function(e){
                e.preventDefault();
                //
                let reconcile_note = document.getElementById("reconcile_note").value;
                let data = {"status_detail": 82, "orders": arrOrder, "reconcile_note": reconcile_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(8, 82) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_reconcile_missing").on('submit', function(e){
                e.preventDefault();
                //
                let reconcile_missing_note = document.getElementById("reconcile_missing_note").value;
                let data = {"status_detail": 83, "orders": arrOrder, "reconcile_missing_note": reconcile_missing_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(8, 83) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_reconcile_damaged").on('submit', function(e){
                e.preventDefault();
                //
                let reconcile_damaged_note = document.getElementById("reconcile_damaged_note").value;
                let data = {"status_detail": 84, "orders": arrOrder, "reconcile_damaged_note": reconcile_damaged_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(8, 84) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_missing").on('submit', function(e){
                e.preventDefault();
                //
                let missing_note = document.getElementById("missing_note").value;
                let data = {"status_detail": 71, "orders": arrOrder, "missing_note": missing_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(7, 71) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_damaged").on('submit', function(e){
                e.preventDefault();
                //
                let damaged_note = document.getElementById("damaged_note").value;
                let data = {"status_detail": 72, "orders": arrOrder, "damaged_note": damaged_note}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(7, 72) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_missing_confirm").on('submit', function(e){
                e.preventDefault();
                //
                let missing_confirm_indemnify = document.getElementById("missing_confirm_indemnify").value;
                let missing_confirm_note = document.getElementById("missing_confirm_note").value;
                let data = {"status_detail": 73, "orders": arrOrder, "missing_confirm_note": missing_confirm_note, "missing_confirm_indemnify": missing_confirm_indemnify}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(7, 73) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            $("#frm_damaged_confirm").on('submit', function(e){
                e.preventDefault();
                //
                let damaged_confirm_indemnify = document.getElementById("damaged_confirm_indemnify").value;
                let damaged_confirm_note = document.getElementById("damaged_confirm_note").value;
                let data = {"status_detail": 74, "orders": arrOrder, "damaged_confirm_note": damaged_confirm_note, "damaged_confirm_indemnify": damaged_confirm_indemnify}
                $.ajax({
                    type: 'PUT',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    },
                    url: '{{ route('api.orders.update-status-order') }}',
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function () {
                    $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                    setTimeout(function(){ redirectNext(7, 74) }, 1000);
                }).fail(function (msg) {
                    $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                });
            });

            {{--$("#btn_filter_submit").click(function() {--}}
                {{--let route = '{{ route('admin.orders.index') }}';--}}
                {{--let newParams = '';--}}
                {{--$('#frm_filter_orders input, #frm_filter_orders select').each(--}}
                    {{--function(index) {--}}
                        {{--var input = $(this);--}}
                        {{--if (input.attr('name') === 'filter_status_detail') {--}}
                            {{--newParams += '&status_detail=' + input.val();--}}
                        {{--}--}}
                        {{--if (input.attr('name') === 'filter_daterange') {--}}
                            {{--let dateRange = input.val();--}}
                            {{--newParams += '&begin=' + dateRange.substr(0, 10) + '&end=' + dateRange.substr(dateRange.length - 10, 10);--}}
                        {{--}--}}
                    {{--});--}}
                {{--window.location = reFormatUriParam(route, newParams);--}}
            {{--});--}}

            $('#filter_limit').on('change', function() {
                HoldOn.open({theme:"sk-rect"});
                let route = '{{ route('admin.orders.index') }}';
                window.location = reFormatUriParam(route, '&limit=' + this.value);
            });

            $('#btnExportExcel').click(function () {
                let route = '{{ route('admin.orders.export') }}';
                window.location = reFormatUriParam(route, '&orders=' + arrOrder.toString());
                $.Toast("Thành công", "Xuất file thành công!", "notice");
            })
        });

        function submitFilterOrders() {
            // sk-rect, sk-dot, sk-cube, sk-bounce, sk-circle
            HoldOn.open({theme:"sk-rect"});
            let route = '{{ route('admin.orders.index') }}';
            let newParams = '';
            $('#frm_filter_orders input, #frm_filter_orders select').each(
                function(index) {
                    var input = $(this);
                    if (input.attr('name') === 'filter_status_detail') {
                        if (parseInt(input.val()) > 0) {
                            newParams += '&status_detail=' + input.val() + '&page=1';
                        } else {
                            newParams += '&status_detail=0&page=1';
                        }
                    }
                    if (input.attr('name') === 'filter_pickup' || input.attr('name') === 'filter_shipper' || input.attr('name') === 'filter_refund') {
                        if (parseInt(input.val()) > 0) {
	                        newParams += '&user_selected=' + input.val();
                        } else {
	                        newParams += '&user_selected=0';
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
        }

        function actionByOrderChecked() {
            document.getElementById("countOrderSelected").innerHTML = arrOrder.length;
            document.getElementById("countOrderSelectedModal").innerHTML = arrOrder.length;

            @if (!$filter['status_detail'])
            // arrOrderStatus
            let arrStatus = [];
            if (arrOrder.length > 0) {
                arrOrder.forEach(function (order_id) {
                    let order_status_detail = arrOrderStatusDetail[order_id];
                    arrStatus.push(order_status_detail);
                })
            }

            let uniqueArray = arrStatus.filter(function(item, pos) {
                return arrStatus.indexOf(item) == pos;
            })

            if (uniqueArray.length === 1) {
                let statusDetail = uniqueArray[0];
                let status = arrOrderStatus[statusDetail];

                // document.getElementById("modalBodyContent").innerHTML = '';
                let routeApi = '{{ route('api.orders.get-modal-action') }}' + '?status=' + status + '&status_detail=' + statusDetail;
                $.ajax({
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                    },
                    url: routeApi,
                    success: function(response){
                        document.getElementById("modalBodyContent").innerHTML = response;

                        $("#frm_store").on('submit', function(e){
                            e.preventDefault();
                            //
                            let store_note = document.getElementById("store_note").value;
                            let post_office = document.getElementById("post_office_store").value;
                            let select_user_receiver = document.getElementById("select_user_receiver_store").value;

                            let data = {"status_detail": 25, "orders": arrOrder, "post_office": post_office, "store_note": store_note, "select_user_receiver": select_user_receiver}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_warehouse").on('submit', function(e){
                            e.preventDefault();
                            //
                            let post_office = document.getElementById("post_office").value;
                            let warehouse_note = document.getElementById("warehouse_note").value;
                            let select_user_receiver = document.getElementById("select_user_receiver").value;

                            let data = {"status_detail": 22, "orders": arrOrder, "post_office": post_office, "warehouse_note": warehouse_note, "select_user_receiver": select_user_receiver}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_warehouse_refund").on('submit', function(e){
                            e.preventDefault();
                            //
                            let post_office = document.getElementById("post_office").value;
                            let warehouse_note = document.getElementById("warehouse_note").value;
                            let select_user_receiver = document.getElementById("select_user_receiver").value;

                            let data = {"status_detail": 36, "orders": arrOrder, "post_office": post_office, "warehouse_note": warehouse_note, "select_user_receiver": select_user_receiver}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_confirm_resend").on('submit', function(e){
                            e.preventDefault();
                            //
                            let data = {"status_detail": 41, "orders": arrOrder}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_confirm_refund").on('submit', function(e){
                            e.preventDefault();
                            //
	                        let confirm_refund_reason = document.getElementById("confirm_refund_reason").value;
                            let data = {"status_detail": 34, "orders": arrOrder, "confirm_refund_reason": confirm_refund_reason}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_select_shipper_pick").on('submit', function(e){
                            e.preventDefault();
                            //
                            var select_shipper = document.getElementById("select_shipper_pick").value;
                            let data = {"status_detail": 12, "select_shipper": select_shipper, "orders": arrOrder}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_re_shipper_pick").on('submit', function(e){
                            e.preventDefault();
                            //
                            let data = {"status_detail": 12, "select_shipper": -1, "orders": arrOrder}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_re_shipper_send").on('submit', function(e){
                            e.preventDefault();
                            //
                            let data = {"status_detail": 23, "select_shipper": -1, "orders": arrOrder}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_select_shipper_refund").on('submit', function(e){
                            e.preventDefault();
                            //
                            var select_shipper = document.getElementById("select_pickup_refund").value;
                            let data = {"status_detail": 32, "select_shipper": select_shipper, "orders": arrOrder}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_select_shipper_send").on('submit', function(e){
                            e.preventDefault();
                            //
                            var select_shipper = document.getElementById("select_shipper_send").value;
                            let data = {"status_detail": 23, "select_shipper": select_shipper, "orders": arrOrder}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_cancel_orders").on('submit', function(e){
                            e.preventDefault();
                            //
                            let cancel_reason = document.getElementById("cancel_reason").value;
                            let cancel_note = document.getElementById("cancel_note").value;

                            let data = {"status_detail": 61, "orders": arrOrder, "cancel_reason": cancel_reason, "cancel_note": cancel_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_set_refund").on('submit', function(e){
                            e.preventDefault();
                            //
                            let refund_reason = document.getElementById("refund_reason").value;
                            let refund_note = document.getElementById("refund_note").value;

                            let data = {"status_detail": 31, "orders": arrOrder, "refund_reason": refund_reason, "refund_note": refund_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_approval_refund").on('submit', function(e){
                            e.preventDefault();
                            //
                            let data = {"status_detail": 35, "orders": arrOrder}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_refund_fail").on('submit', function(e){
                            e.preventDefault();
                            //
                            let fail_note = document.getElementById("fail_note").value;
                            let data = {"status_detail": 33, "orders": arrOrder, "fail_note": fail_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_send_fail").on('submit', function(e){
                            e.preventDefault();
                            //
                            let fail_note = document.getElementById("fail_note").value;

                            let data = {"status_detail": 24, "orders": arrOrder, "fail_note": fail_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_pick_fail").on('submit', function(e){
                            e.preventDefault();
                            //
                            let fail_note = document.getElementById("fail_note").value;

                            let data = {"status_detail": 13, "orders": arrOrder, "fail_note": fail_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_send_success").on('submit', function(e){
                            e.preventDefault();
                            //
                            let data = {"status_detail": 51, "orders": arrOrder}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_refund_success").on('submit', function(e){
                            e.preventDefault();
                            //
                            let data = {"status_detail": 52, "orders": arrOrder}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_pick_success").on('submit', function(e){
                            e.preventDefault();
                            //
                            let data = {"status_detail": 21, "orders": arrOrder}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_collect_money").on('submit', function(e){
                            e.preventDefault();
                            //
                            let collect_money_note = document.getElementById("collect_money_note").value;
                            let data = {"status_detail": 91, "orders": arrOrder, "collect_money_note": collect_money_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_reconcile_send").on('submit', function(e){
                            e.preventDefault();
                            //
                            let reconcile_note = document.getElementById("reconcile_note").value;
                            let data = {"status_detail": 81, "orders": arrOrder, "reconcile_note": reconcile_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_reconcile_refund").on('submit', function(e){
                            e.preventDefault();
                            //
                            let reconcile_note = document.getElementById("reconcile_note").value;
                            let data = {"status_detail": 82, "orders": arrOrder, "reconcile_note": reconcile_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_reconcile_missing").on('submit', function(e){
                            e.preventDefault();
                            //
                            let reconcile_missing_note = document.getElementById("reconcile_missing_note").value;
                            let data = {"status_detail": 83, "orders": arrOrder, "reconcile_missing_note": reconcile_missing_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_reconcile_damaged").on('submit', function(e){
                            e.preventDefault();
                            //
                            let reconcile_damaged_note = document.getElementById("reconcile_damaged_note").value;
                            let data = {"status_detail": 84, "orders": arrOrder, "reconcile_damaged_note": reconcile_damaged_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_missing").on('submit', function(e){
                            e.preventDefault();
                            //
                            let missing_note = document.getElementById("missing_note").value;
                            let data = {"status_detail": 71, "orders": arrOrder, "missing_note": missing_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_damaged").on('submit', function(e){
                            e.preventDefault();
                            //
                            let damaged_note = document.getElementById("damaged_note").value;
                            let data = {"status_detail": 72, "orders": arrOrder, "damaged_note": damaged_note}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_missing_confirm").on('submit', function(e){
                            e.preventDefault();
                            //
                            let missing_confirm_indemnify = document.getElementById("missing_confirm_indemnify").value;
                            let missing_confirm_note = document.getElementById("missing_confirm_note").value;
                            let data = {"status_detail": 73, "orders": arrOrder, "missing_confirm_note": missing_confirm_note, "missing_confirm_indemnify": missing_confirm_indemnify}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });

                        $("#frm_damaged_confirm").on('submit', function(e){
                            e.preventDefault();
                            //
                            let damaged_confirm_indemnify = document.getElementById("damaged_confirm_indemnify").value;
                            let damaged_confirm_note = document.getElementById("damaged_confirm_note").value;
                            let data = {"status_detail": 74, "orders": arrOrder, "damaged_confirm_note": damaged_confirm_note, "damaged_confirm_indemnify": damaged_confirm_indemnify}
                            $.ajax({
                                type: 'PUT',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                                    xhr.setRequestHeader ("X-CSRF-TOKEN", "{{ csrf_token() }}");
                                },
                                url: '{{ route('api.orders.update-status-order') }}',
                                contentType: 'application/json',
                                data: JSON.stringify(data), // access in body
                            }).done(function () {
                                $.Toast("Thành công", "Update trạng thái thành công!", "notice");
                                setTimeout(function(){ location.reload() }, 1000);
                            }).fail(function (msg) {
                                $.Toast("Thất bại", "Update trạng thái thất bại!", "error");
                            });
                        });
                    }
                });

                if ( orderConstantStatus[status]['detail'][statusDetail]['next'].length === 0 ) {
                    $('#boxActionBtn').css('display', 'none');
                } else {
                    $('#boxActionBtn').css('display', 'block');
                }

            } else {
                $('#boxActionBtn').css('display', 'none');
            }
            @else
                if ( arrOrderConstantStatus[statusDetail]['next'].length === 0 ) {
                    $('#boxActionBtn').css('display', 'none');
                } else {
                    $('#boxActionBtn').css('display', 'block');
                }
            @endif

	        let totalFee = 0;
	        let totalCod = 0;
	        if (arrOrder.length > 0) {
		        arrOrder.forEach(function (order_id) {
			        totalFee += orderTotalFee[order_id];
			        totalCod += orderTotalCod[order_id];
		        })
	        }
	        document.getElementById("txtTotalFee").innerHTML = String(totalFee).replace(/(.)(?=(\d{3})+$)/g,'$1,');
	        document.getElementById("txtTotalCod").innerHTML = String(totalCod).replace(/(.)(?=(\d{3})+$)/g,'$1,');

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
            document.getElementById("countOrderSelectedModal").innerHTML = arrOrder.length;

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
            let route = '{{ route('admin.orders.index') }}';
            {{--let route = '{{ route('admin.orders.index', ":slug") }}';--}}
            // route = route.replace(':slug', shopSelected);
            const queryString = window.location.search;
            if (queryString === '') route += '?';
            window.location = route + queryString + '&shop=' + shopSelected;
        }

        function shopSelectedRedis(shopSelected) {
            HoldOn.open({theme:"sk-rect"});
            let route = '{{ route('admin.orders.index') }}';

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
                '{{ route('admin.print-orders.print') }}?page_size=' + type + '&order_id=' + arrOrder.join(','),
                '_blank'
            );
        }
        function printOrder(id, type) {
            window.open(
                '{{ route('admin.print-orders.print') }}?page_size=' + type + '&order_id=' + id,
                '_blank'
            );
        }
    </script>
@endsection


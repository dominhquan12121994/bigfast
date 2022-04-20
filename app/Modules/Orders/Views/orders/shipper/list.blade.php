@extends('layouts.base')

@section('css')
    <style type="text/css">
        select + .select2-container {
            width: 200px !important;
        }
        .table-responsive {
            overflow: auto;
            max-height: 500px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        {{--@if($shop)--}}
                        <div class="card-body">

                            @include('Orders::orders.shared.count-status')

                            @include('Orders::orders.shipper.filter')

                            @include('Orders::orders.shipper.order-item')

                            <div class="row">
                                <div class="col-auto mb-3">
                                    @include('Orders::orders.shipper.order-actions')
                                </div>
                                <div class="col-auto ml-auto">
                                    {{ $orders->withQueryString()->links() }}
                                </div>
                            </div>

                            <div class="modal fade" id="orderActionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-info modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Thao tác <b id="countOrderSelectedModal" style="color: orangered">0</b> đơn hàng đơn được chọn</h4>
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                        </div>
                                        <div class="modal-body">
                                            @if($filter['status_detail'])
                                                <div class="nav-tabs-boxed nav-tabs-boxed-left">
                                                    <ul class="nav nav-tabs" role="tablist" style="width: 20%">
                                                        @php
                                                            $count = 0;
                                                            $statusShipper = array();
                                                            $user_roles = $currentUser->getRoleNames()->toArray();
                                                            if (in_array('pickup', $user_roles)) $statusShipper += \App\Modules\Orders\Constants\OrderConstant::statusShipperPickup;
                                                            if (in_array('shipper', $user_roles)) $statusShipper += \App\Modules\Orders\Constants\OrderConstant::statusShipperShip;
                                                            if (in_array('refund', $user_roles)) $statusShipper += \App\Modules\Orders\Constants\OrderConstant::statusShipperRefund;
                                                        @endphp
                                                        @foreach($statusShipper[$filter['status']]['detail'][$filter['status_detail']]['next'] as $key => $value)
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
                                                        @foreach($statusShipper[$filter['status']]['detail'][$filter['status_detail']]['next'] as $key => $value)
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
                                                                            @include('Orders::orders.modal.re-shipper-send')
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

    <script type="application/javascript">
        let arrOrder = [];
        let lastChecked = null;
        let $chkboxes = $('.singlechkbox');
        let statusDetail = '{{ $filter['status_detail'] }}';
        let user_id = '{{ $userId }}';
        let user_type = '{{ $userType }}';

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
                $('.singlechkbox').prop('checked', this.checked);
                if (this.checked) {
                    window.scrollTo(0,document.body.scrollHeight);
                }
            });

            $('body').on('click', '.singlechkbox', function(e) {
                let input = $(this);
                let checked = input.is(':checked');
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
                // $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
                startDateFilter = start;
                endDateFilter = end;

                HoldOn.open({theme:"sk-rect"});
                let route = '{{ route('admin.orders.index') }}';
                let newParams = '&begin=' + start.format('DD-MM-YYYY') + '&end=' + end.format('DD-MM-YYYY');
                window.location = reFormatUriParam(route, newParams);
            });
            @endif

            $("#frm_cancel_orders").on('submit', function(e){
                e.preventDefault();
                //
                let cancel_reason = document.getElementById("cancel_reason").value;
                let cancel_note = document.getElementById("cancel_note").value;

                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 61, "orders": arrOrder, "cancel_reason": cancel_reason, "cancel_note": cancel_note}
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
                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 33, "orders": arrOrder, "fail_note": fail_note}
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

                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 24, "orders": arrOrder, "fail_note": fail_note}
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

                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 13, "orders": arrOrder, "fail_note": fail_note}
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
                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 51, "orders": arrOrder}
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
                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 52, "orders": arrOrder}
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
                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 21, "orders": arrOrder}
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
                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 71, "orders": arrOrder, "missing_note": missing_note}
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
                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 72, "orders": arrOrder, "damaged_note": damaged_note}
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
                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 12, "select_shipper": -1, "orders": arrOrder}
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
                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 41, "orders": arrOrder}
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
                let data = {"user_id": user_id, "user_type": user_type, "status_detail": 23, "select_shipper": -1, "orders": arrOrder}
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

            $("#btn_filter_submit").click(function() {
                let route = '{{ route('admin.orders.index') }}';
                let newParams = '';
                $('#frm_filter_orders input, #frm_filter_orders select').each(
                    function(index) {
                        var input = $(this);
                        if (input.attr('name') === 'filter_status_detail') {
                            newParams += '&status_detail=' + input.val();
                        }
                        if (input.attr('name') === 'filter_daterange') {
                            let dateRange = input.val();
                            newParams += '&begin=' + dateRange.substr(0, 10) + '&end=' + dateRange.substr(dateRange.length - 10, 10);
                        }
                    });
                window.location = reFormatUriParam(route, newParams);
            });

            $('#filter_limit').on('change', function() {
                HoldOn.open({theme:"sk-rect"});
                let route = '{{ route('admin.orders.index') }}';
                window.location = reFormatUriParam(route, '&limit=' + this.value);
            });
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
        $('document').ready(function () {
            $(".cancelBtn").html("Huỷ bỏ");
            $(".applyBtn").html("Áp dụng");
        });
    </script>
@endsection


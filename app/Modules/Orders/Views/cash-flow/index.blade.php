@extends('layouts.base')

@section('css')
    <link href="{{ asset('libs/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <style>
        .tbl-css {
            cursor: pointer;
            min-width: 100px;
        }
        @media (min-width: 992px) {
            .col-half-offset {
                margin-left: 4.166666667%;
            }
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
                            <div class="flex">
                                <i class="fa fa-align-justify"></i> Theo dõi dòng tiền&nbsp;&nbsp;|&nbsp;&nbsp;
                                <a href="{{ route('admin.order-incurred-fee.index') }}">>> Chi phí phát sinh</a>
                            </div>
                            <div class="flex form-inline">
                                <div class="form-group">
                                    <label class="mr-1" for="filter_date">Thời gian </label>
                                    <input type="text" class="form-control form-control-sm" id="filter_date" name="filter_date">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 col-lg-2">
                                    <div class="c-callout c-callout-info tbl-reconcile tbl-css">
                                        <span class="text-muted">Shops</span>
                                        <div class="text-value-lg text-success">
                                            {{ number_format(count($arrDataCash)) }}
                                            <small>shops</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-2 col-half-offset">
                                    <div class="c-callout c-callout-danger tbl-fee tbl-css">
                                        <span class="text-muted">Tổng phí</span>
                                        <div class="text-value-lg">
                                            {{ number_format($arrDataCash->sum('total_fee')) }}
                                            <small>đ</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-2 col-half-offset">
                                    <div class="c-callout c-callout-primary tbl-fee tbl-css">
                                        <span class="text-muted">Tổng CoD</span>
                                        <div class="text-value-lg">
                                            {{ number_format($arrDataCash->sum('total_cod')) }}
                                            <small>đ</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-2 col-half-offset">
                                    <div class="c-callout c-callout-warning tbl-fee tbl-css">
                                        <span class="text-muted">Tiền bồi hoàn</span>
                                        <div class="text-value-lg">
                                            {{ number_format($arrDataCash->sum('money_indemnify')) }}
                                            <small>đ</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-2 col-half-offset">
                                    <div class="c-callout c-callout-success tbl-reconcile tbl-css">
                                        <span class="text-muted">Tổng dư</span>
                                        <div class="text-value-lg">
                                            {{ number_format($arrDataCash->sum('total_du')) }}
                                            <small>đ</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="tbl-reconcile">
                                <div class="col pt-3">
                                    <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl table-striped" id="table_shops">
                                        <thead>
                                        <tr>
                                            @if ( $currentUser->can('action_cash_flow_check') )
                                                <th class="pr-0"><input type="checkbox" id="select_all"/></th>
                                            @endif
                                            <th>STT</th>
                                            <th style="min-width:75px">Shop</th>
                                            <th style="min-width:75px">Ngân hàng</th>
                                            <th>Email</th>
                                            <th class="text-right" style="min-width:90px">Số điện thoại</th>
                                            <th class="text-right text-danger" style="min-width:75px">Tổng phí<i data-toggle="tooltip" html="true" title="Tổng tiền phí dịch vụ Shop trả cho BigFast" class="fa fa-question-circle ml-1 text-danger"></i></th>
                                            <th class="text-right text-primary" style="min-width:85px">Tổng CoD<i data-toggle="tooltip" html="true" title="Tổng tiền thu hộ nhân viên BigFast thu từ khách hàng" class="fa fa-question-circle ml-1 text-danger"></i></th>
                                            <th class="text-right text-warning" style="min-width:110px">Tiền bồi hoàn<i data-toggle="tooltip" html="true" title="Tiền BigFast trả lại Shop khi xảy ra hư hỏng, thất lạc hàng hoá" class="fa fa-question-circle ml-1 text-danger"></i></th>
                                            <th class="text-right text-success" style="min-width:75px">Số dư<i data-toggle="tooltip" html="true" title="Tiền BigFast trả Shop, tính bằng tổng tiền thu hộ cộng tiền bồi hoàn, sau đó trừ đi tổng phí" class="fa fa-question-circle ml-1 text-danger"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($arrDataCash as $key => $cash)
                                            <tr>
                                                @if ( $currentUser->can('action_cash_flow_check') )
                                                    <td class="pr-0"><input type="checkbox" data-fullinfo="{{ $cash->fullInfo }}" data-name="{!! $cash->shop->name !!}" class="single_check_box" name="cbx_shop_id[]" value="{{ $cash->shop->id }}"/></td>
                                                @endif
                                                <td>{{ number_format($key + 1) }}</td>
                                                <td>
                                                    <a href="{{ route('admin.reports.cash-flow-export', array(
                                                            'shopId' => $cash->shop->id,
                                                            'timeBegin' => $cash->timeRange[0],
                                                            'timeEnd' => $cash->timeRange[1],
                                                            'shopName' => $cash->shop->name,
                                                        )) }}">
                                                        {!! $cash->shop->name !!}
                                                    </a>
                                                </td>
                                                <td>{{ $cash->shop_bank->bank_name }}</td>
                                                <td>{{ $cash->shop->email }}</td>
                                                <td class="text-right">{{ $cash->shop->phone }}</td>
                                                <td class="text-right">
                                                    {{ number_format($cash->total_fee) . ' vnđ' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($cash->total_cod) . ' vnđ' }}
                                                </td>
                                                <td class="text-right">
                                                    {{ number_format($cash->money_indemnify) . ' vnđ' }}
                                                </td>
                                                <td class="text-right">{{ number_format($cash->total_du) . ' vnđ' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <div class="row">
                                        <div class="col-auto mr-auto">
                                            <div id="reconcile_modal" style="display: none;">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1 ml-3 mr-5">
                                                        <b>Đã chọn</b><br>
                                                        <b id="count_shop_selected" style="font-size: 30px; color: orangered">0</b> Shop
                                                    </div>
                                                    <div class="d-flex align-items-end">
                                                        <button class="btn btn-pill btn-primary mr-2" type="button" id="shop_reconcile">Đối soát</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
    <script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>

    <script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/daterangepicker/daterangepicker.min.css') }}" />
    <script type="text/javascript" src="{{ asset('js/tooltips.js') }}"></script>

    <script type="application/javascript">
        // check and uncheck shop not full info
        let arrShopMissInfo = [];

        $('#shop_reconcile').click(function() {
            if ( arrShopMissInfo.length > 0 ) {
                $.Toast("Thất bại", "Vui lòng cập nhật đầy đủ thông tin ngân hàng shop " + arrShopMissInfo.join(', '), "error");
                return;
            }
            var isConfirm = confirm('Xác nhận thực hiện đối soát?');
            if (!isConfirm) {
                return;
            }
            let dateReconcile = $('#filter_date').val();
            let userReconcile = '{{ $userReconcile }}';

            // send ajax
            let routeApi = '{{ route('api.shop-reconcile') }}';
            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: routeApi,
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "arrShop": arrShop,
                    "dateReconcile": dateReconcile,
                    "userReconcile": userReconcile
                },
                success: function(response){
                    if (response == 1) {
                        $.Toast("Thành công", "Đối soát thành công!", "notice");
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        $.Toast("Thất bại", "Đối soát thất bại!", "error");
                        setTimeout(function() {
                            $('#reconcile_notify').fadeOut();
                        }, 1500);
                    }
                },
                error : function() {
                    $.Toast("Thất bại", "Đối soát thất bại!", "error");
                    setTimeout(function() {
                        $('#reconcile_notify').fadeOut();
                    }, 1500);
                }
            });
        });

        function logicOrderChecked(checked = false, shop_id = 0, name = '', fullInfo = false) {
            if (checked) {
                if (!arrShop.includes(shop_id)) {
                    arrShop.push(shop_id);
                }
                if ( !fullInfo && !arrShopMissInfo.includes(name)) {
                    arrShopMissInfo.push(name);
                }
            } else {
                if (arrShop.includes(shop_id)) {
                    const index = arrShop.indexOf(shop_id);
                    if (index > -1) {
                        arrShop.splice(index, 1);
                    }
                }
                if ( !fullInfo && arrShopMissInfo.includes(name)) {
                    const indexMiss = arrShopMissInfo.indexOf(name);
                    if (indexMiss > -1) {
                        arrShopMissInfo.splice(indexMiss, 1);
                    }
                }
            }

            // hiển thị modal
            shopSelectedLength = arrShop.length;
            document.getElementById("count_shop_selected").innerHTML = shopSelectedLength;
            if (shopSelectedLength > 0)
                $("#reconcile_modal").fadeIn();
            else
                $("#reconcile_modal").fadeOut();
        }

        // check and uncheck shop
        let arrShop = [];
        // mảng chứa các id đã chọn
        let lastChecked = null;
        let checkboxList = $('.single_check_box');

        jQuery(function($) {
            $('body').on('click', '#select_all', function() {
                let checked = this.checked;
                $('#table_shops input').each(
                    function(index){
                        var input = $(this);
                        if (input.attr('class') === 'single_check_box') {
                            logicOrderChecked(checked, input.val(), input.data('name'), input.data('fullinfo') );
                        }
                    });
                $('.single_check_box').prop('checked', this.checked);
                if (this.checked) {
                    window.scrollTo(0,document.body.scrollHeight);
                }
            });

            $('body').on('click', '.single_check_box', function(e) {
                let input = $(this);
                let checked = input.is(':checked');
                logicOrderChecked(checked, input.val(), input.data('name'), input.data('fullinfo'));
                if($('.single_check_box').length == $('.single_check_box:checked').length) {
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

                    checkboxList.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked);
                }
                lastChecked = this;
            });
        });
        // end of check and uncheck shop
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
                "stateSave": true,
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

            $('input[name="filter_date"]').daterangepicker({
                startDate: moment(`{{ $calculatorDate }}`),
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 2020,
                maxYear: parseInt(moment().format('YYYY'),10),
                maxDate: moment(),
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
            }, function(start, end, label) {
                let router = '{{ route('admin.reports.cash-flow') }}';
                router += '?date=' + moment(start).format('DD-MM-YYYY');
                location.href = router;
            });
        });
        $('document').ready(function () {
            $(".cancelBtn").html("Huỷ bỏ");
            $(".applyBtn").html("Áp dụng");
        });
    </script>
@endsection

@extends('layouts.base')

@section('css')

@endsection

@section('content')
<div class="container-fluid">

    <div class="animated fadeIn">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <form method="POST" action="{{ route('admin.order-incurred-fee.update', $orderFee->id) }}">
                        <div class="card-header justify-content-between d-flex">
                            <div>
                                <i class="fa fa-align-justify"></i> <strong>{{ __('Cập nhật tiền phát sinh') }}</strong>
                            </div>
                            <div>
                                <button class="btn btn-success" type="submit">Cập nhật</button>
                                <a href="{{ url()->previous() }}" class="btn btn-primary">Quay lại</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            @method('PUT')
                            <div class="row">
                                <div class=" col-12">
                                    <div class="form-group row">
                                        <div class="col">
                                            <input type="text" class="form-control" name="shop_name" value="{!! $orderFee->shop->name !!}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col">
                                            <label class="required">Chọn ngày</label>
                                            <input type="text" class="form-control form-control-sm" id="filter_date" name="date">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col">
                                            <label class="required">Mã vận đơn</label>
                                            <input type="text" class="form-control form-control-sm" id="lading_code" name="lading_code" value="{{ $orderFee->order->lading_code }}" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col">
                                            <label for="order_fee_type" class="required">Loại tiền phát sinh</label>
                                            <select class="form-control" id="order_fee_type" name="fee_type"
                                                    title="Loại tiền phát sinh">
                                                @foreach ($orderFeeTypes as $key => $feeType)
                                                    <option value="{{ $key }}" {{ ($key == $orderFee['fee_type']) ? 'selected="selected"' : '' }}>
                                                        {{ $feeType }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col">
                                            <label class="required">Giá trị</label>
                                            <input class="form-control @error('name') is-invalid @enderror" type="number"
                                                    placeholder="Nhập giá trị tiền phát sinh" name="value" step="1000"
                                                    value="{{ $orderFee['value'] }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->any())
                                <br>
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </form>
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
<script>
    $(document).ready(function() {
        console.log('loaded');
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
        if (repo.id === '') return 'Tìm kiếm Shop cần thêm tiền phát sinh';
        return repo.name || repo.phone;
    }

    // do something after change shop
    function shopSelectedChange() {
        let shopId = document.getElementById('select_shop').value;
        console.log(shopId);
    }
</script>

<script>
    $('input[name="date"]').daterangepicker({
        startDate: moment(),
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
        console.log(start);
        console.log(end);
    });
    $('document').ready(function () {
        $(".cancelBtn").html("Huỷ bỏ");
        $(".applyBtn").html("Áp dụng");
    });
</script>

@endsection

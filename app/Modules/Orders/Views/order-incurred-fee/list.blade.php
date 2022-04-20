@extends('layouts.base')

@section('css')
<style type="text/css">
        select + .select2-container {
            width: 270px !important;
        }
        .scroll {
            overflow: auto;
            max-height: 350px;
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
                            <div>
                                <form class="form-inline" method="GET">
                                @if($shopInfo)
                                    <div class="mr-3">
                                        <span class="badge badge-success">Shop</span> {{ $shopInfo->name }} - {{ $shopInfo->phone }}<br>
                                        {{ $shopInfo->address }}
                                    </div>
                                @else
                                    <select id="select_shop" class="form-control float-left" onchange="shopSelectedChange()"></select>&nbsp;
                                @endif
                                    <input name="shop_id" id="shop_selected" type="hidden" value="{{ isset($conditions['shop_id']) ? $conditions['shop_id'] : '' }}">
                                    <select class="form-control" id="select_fee_type" name="fee_type" title="Loại tiền phát sinh">
                                        <option value="">Tất cả các loại tiền</option>
                                        @foreach ($orderFeeTypes as $key => $feeType)
                                        <option value="{{ $key }}" {{ (!is_array($conditions['fee_type']) && ($conditions['fee_type'] === $key)) ? 'selected="selected"' : '' }}>
                                            {{ $feeType }}
                                        </option>
                                        @endforeach
                                    </select>&nbsp;
                                    <button id="btn_filter_submit" class="btn btn-sm btn-success" type="button">Xem báo cáo</button>&nbsp;
                                    <a class="btn btn-sm btn-info" href="{{ route('admin.order-incurred-fee.index') }}">Làm lại</a>
                                </form>
                            </div>
                            <div>
                                @if($currentUser->can('action_order_fee_create'))
                                    <a href="{{ route('admin.order-incurred-fee.create') }}" class="btn btn-sm btn-primary">Thêm mới tiền phát sinh</a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table_order_fee">
                                <thead>
                                <tr>
                                    <th>STT</th>
                                    <th style="min-width:85px">Tên shop</th>
                                    <th style="min-width:105px">Mã vận đơn</th>
                                    <th style="min-width:145px">Loại phí phát sinh</th>
                                    <th style="min-width:65px">Giá trị</th>
                                    <th style="min-width:100px">Ngày thêm</th>
                                    <th style="width: 130px;">Sửa/xóa</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($arrOrderFee as $key => $fee)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><strong>{!! $fee->shop->name !!}</strong></td>
                                        <td><strong>{{ $fee->order->lading_code }}</strong></td>
                                        <td>{{ $orderFeeTypes[$fee['fee_type']] }}</td>
                                        <td>{{ number_format($fee['value']) }}</td>
                                        <td>{{ date('d-m-Y', strtotime($fee['date'])) }}</td>
                                        <td class="pl-0 pr-0 box-actions">
                                            @if($currentUser->can('action_order_fee_update'))
                                                <a href="{{ route('admin.order-incurred-fee.edit', $fee->id) }}" class="btn btn-sm btn-pill btn-primary {{ ($fee->date != date('Ymd')) ? 'disabled' : '' }}" title="Edit">Sửa</a>
                                            @endif
                                            @if($currentUser->can('action_order_fee_delete'))
                                                <form action="{{ route('admin.order-incurred-fee.destroy', $fee->id ) }}" method="POST" style="display: inline">
                                                    @method('DELETE')
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                    <button class="btn btn-sm btn-pill btn-danger" title="Delete" onclick="return confirm('Thao tác này không thể hoàn tác! Bạn có chắc chắn xoá?');" {{ ($fee->date != date('Ymd')) ? 'disabled' : '' }}>
                                                        Xoá
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $arrOrderFee->withQueryString()->links() }}
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

<script>
    $(document).ready(function() {
        console.log('loaded');
        $('#btn_filter_submit').click(function() {
            submitFilterReports();
        });
    });
</script>

<script>
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

    // function when click "xem bao cao"
    function submitFilterReports() {
        // sk-rect, sk-dot, sk-cube, sk-bounce, sk-circle
        HoldOn.open({theme:"sk-rect"});
        let shopId = $('#shop_selected').val();
        let feeType = $('#select_fee_type').val();
        let route = '{{ route('admin.order-incurred-fee.index') }}';
        let newParams = '&shop_id=' + shopId;
        newParams += '&fee_type=' + feeType;
        // console.log(reFormatUriParam(route, newParams));
        window.location = reFormatUriParam(route, newParams);
    }
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
        if (repo.id === '') return 'Tìm kiếm Shop cần quản lý';
        return repo.name || repo.phone;
    }

    // do something after change shop
    function shopSelectedChange() {
        let shopId = document.getElementById('select_shop').value;
        console.log(shopId);
        $('#shop_selected').val(shopId);
    }
</script>

<script>
    // find shop information
    var $selectFeeType = $('#select_fee_type');
    $selectFeeType.select2({
        theme: "classic",
        placeholder: 'Chọn loại tiền phát sinh',
        allowClear: true,
    });

</script>

@endsection


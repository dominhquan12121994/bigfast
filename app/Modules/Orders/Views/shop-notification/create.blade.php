@extends('layouts.base')

@section('css')
    <link rel="stylesheet" href="{{ asset('libs/multiselect/css/bootstrap-multiselect.min.css') }}" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/daterangepicker/daterangepicker.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/tagsinput/typeahead.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/tagsinput/bootstrap-tagsinput.css') }}"/>
    <style>
        .purpose_container .btn-group,
        .branch_container .btn-group,
        .scale_container .btn-group {
            display: block;
        }

        .purpose_container .dropdown-toggle,
        .branch_container .dropdown-toggle,
        .scale_container .dropdown-toggle {
            text-align: left;
        }

        .purpose_container .btn-group .dropdown-menu,
        .branch_container .btn-group .dropdown-menu,
        .scale_container .btn-group .dropdown-menu {
            width: 100%;
        }
    </style>


@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> <strong>{{ __('Thêm mới thông báo') }}</strong>
                        </div>
                        <div class="card-body">
                            <form id="filter_form" method="GET" action="{{ route('admin.shop-notification.create') }}">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col">
                                                <p><strong>Điều kiện áp dụng</strong></p>
                                                <label for="select_shop">Tìm kiếm shop ( Chỉ áp dụng cho những shop đã đăng ký nhận thông báo)</label>
                                                <input name="shop_name" id="shop_name" class="form-control form-rounded" type="text"
                                                       placeholder="Nhập tên shop cần tìm" value="{{ isset($filter) ? $filter['shop_name'] : '' }}" data-role="tagsinput">
                                            </div>
                                            <div class="col purpose_container">
                                                <p><strong>&nbsp;</strong></p>
                                                <label>Chọn mục đích sử dụng</label><br>
                                                <select name="shop_purpose[]" id="shop_purpose" class="form-control form-rounded" multiple>
                                                    @foreach ($purposes as $key => $purpose)
                                                        <option value="{{ $key }}" {{ isset($filter) && in_array($key, $filter['purposes']) ? "selected" : "" }}>{{ $purpose }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col branch_container">
{{--                                                <label>Chọn ngành hàng</label><br>--}}
{{--                                                <select name="shop_branch[]" id="shop_branch" class="form-control form-rounded" multiple>--}}
{{--                                                    @foreach ($branchs as $key => $branch)--}}
{{--                                                        <option value="{{ $key }}" {{ isset($filter) && in_array($key, $filter['branchs']) ? "selected" : "" }}>{{ $branch['name'] }}</option>--}}
{{--                                                    @endforeach--}}
{{--                                                </select>--}}
                                            </div>
                                            <div class="col scale_container">
                                                <label>Chọn quy mô vận chuyển</label><br>
                                                <select name="shop_scale[]" id="shop_scale" class="form-control form-rounded" multiple>
                                                    @foreach ($scales as $key => $scale)
                                                        <option value="{{ $key }}" {{ isset($filter) && in_array($key, $filter['scales']) ? "selected" : "" }}>{{ $scale }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-success" type="submit">Xem danh sách shop</button>
                                <a class="btn btn-primary" type="button" href="{{ route('admin.shop-notification.create') }}">Làm lại</a>
                            </form>

                            <form id="save_notification" method="POST" action="{{ route('admin.shop-notification.store') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <div class="row mt-3" id="tbl-reconcile">
                                    <div class="col">
                                        <p><strong>Danh sách shop áp dụng</strong></p>
                                        <table class="table table-responsive-sm table-striped" id="table_shops">
                                            <thead>
                                                <th class="pr-0"><input type="checkbox" id="select_all"/></th>
                                                <th>STT</th>
                                                <th>Tên shop</th>
                                                <th>Số điện thoại</th>
                                                <th>Email</th>
                                            </thead>
                                            <tbody id="shop_table">
                                            @if($arrShopSelected)
                                                @foreach($arrShopSelected as $key => $value)
                                                <tr>
                                                    <td class="pr-0">
                                                        <input type="checkbox" class="single_check_box" name="cbx_shop_id[]" value="{{ $value->id }}"/>
                                                        <input type="hidden" value="{{ $value->device_token }}" name="cbx_device_token[{{ $value->id }}]">
                                                    </td>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $value->name }}</td>
                                                    <td>{{ $value->phone }}</td>
                                                    <td>{{ $value->email }}</td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">Không có shop đáp ứng điều kiện, vui lòng chọn lại!</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col">
                                                <input type="hidden" name="shop_purpose" value="{{ implode(',', $filter['purposes']) }}">
{{--                                                <input type="hidden" name="shop_branch" value="{{ implode(',', $filter['branchs']) }}">--}}
                                                <input type="hidden" name="shop_scale" value="{{ implode(',', $filter['scales']) }}">
                                                <p><strong>Nội dung thông báo</strong></p>
                                                <textarea id="notification_content" name="notification_content"
                                                          class="form-control form-rounded" rows="5"
                                                          placeholder="Điền nội dung thông báo" required></textarea>
                                                <input id="notification_link" name="notification_link"
                                                          class="form-control form-rounded mt-3" placeholder="Điền URL thông báo"></input>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button id="submit_create_notification" class="btn btn-success" type="submit">Thêm mới</button>
                                    <a href="{{ route('admin.shop-notification.index') }}" class="btn btn-primary">Quay lại</a>
                                </div>
                                @if ($errors->any())
                                    <br>
                                    <div class="alert alert-warning">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript" src="{{ asset('libs/multiselect/js/bootstrap-multiselect.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/tagsinput/typeahead.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/tagsinput/bootstrap-tagsinput.js') }}"></script>

    <script>
        // multi select
        $('#shop_purpose').multiselect({
            nonSelectedText: 'Vui lòng chọn mục đích sử dụng',
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return 'Vui lòng chọn mục đích sử dụng!';
                }
                else {
                    return 'Có ' + options.length + ' mục đích sử dụng được chọn!';
                }
            }
        });
        $('#shop_branch').multiselect({
            nonSelectedText: 'Vui lòng chọn ngành hàng',
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return 'Vui lòng chọn ngành hàng!';
                }
                else {
                    return 'Có ' + options.length + ' ngành hàng được chọn!';
                }
            }
        });
        $('#shop_scale').multiselect({
            nonSelectedText: 'Vui lòng chọn quy mô',
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return 'Vui lòng chọn quy mô!';
                }
                else {
                    return 'Có ' + options.length + ' quy mô được chọn!';
                }
            }
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
            // console.log($('#notification_content').val());
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
    </script>

    <script>
        var countries = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: {
                url: '{{ route('api.shops.list-json') }}',
                filter: function(list) {
                    return $.map(list, function(name) {
                        return { name: name }; });
                }
            }
        });
        countries.initialize();

        $('#shop_name').tagsinput({
            typeaheadjs: {
                name: 'shops',
                displayKey: 'name',
                valueKey: 'name',
                source: countries.ttAdapter()
            }
        });
    </script>
@endsection

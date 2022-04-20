@extends('layouts.base')

@section('css')
    <link rel="stylesheet" href="{{ asset('libs/multiselect/css/bootstrap-multiselect.min.css') }}" type="text/css">
    <style>
        .multiselect {
            text-align: inherit;
        }
        .multiselect-container {
            width: 100% !important;
        }

        .copy_icon {
            color: black;
            top: 50%;
            right: 0;
            transform: translate(-50%, -50%);
            cursor: pointer;
        }

        @media screen and (max-width: 575px) {
            .update_btn, .return_btn, .create_btn {
                width: 100% !important;
                margin-top: 8px;
            }
        }
    </style>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                {{--<div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">--}}
                <div class="col-12">
                    <div class="card">
                        <form method="POST" action="{{ route('admin.shops.update', array('shop' => $shop->id)) }}{{ $createOrder ? '?create-order' : '' }}">
                            <div class="card-header justify-content-between d-block d-sm-flex">
                                <div>
                                    <i class="fa fa-align-justify"></i> Cập nhật: <strong>{!! $shop->name !!}</strong>
                                </div>
                                <div>
                                    <button class="btn btn-success update_btn" type="submit">Cập nhật</button>
                                    <a href="{{ route('admin.shops.index') }}" class="btn btn-primary return_btn">Trở về</a>
                                    <a href="{{ route('admin.orders.create', array('shop_id' => $shop->id)) }}" class="btn btn-info create_btn">Tạo đơn hàng</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <h4>Thông tin cơ bản - <span class="text-danger">ID Shop {{ $shop->id }}</span></h4>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required" for="name">Tên Shop</label>
                                                <input class="form-control @error('name') is-invalid @enderror"
                                                       type="text" value="{!! $shop->name !!}" placeholder="Nhập tên shop"
                                                       name="name" id="name" autofocus
                                                       maxlength="255" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label for="api_token">Token API<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Token truy cập Api tương ứng với tài khoản"></i></label>
                                                <div class="position-relative">
                                                    <input class="form-control" type="text" value="{{ $shop->api_token }}" name="api_token" id="api_token" readonly>
                                                    <span class="position-absolute copy_icon">
                                                        <i class="c-icon cil-copy" id="copy_tooltip" data-toggle="tooltip" title="Đã copy"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required">Số điện thoại</label>
                                                <input class="form-control @error('phone') is-invalid @enderror"

                                                       type="text" value="{{ $shop->phone }}" name="phone"
                                                       placeholder="{{ __('Nhập số điện thoại') }}" maxlength="255"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required">Email</label>
                                                <input class="form-control @error('email') is-invalid @enderror"
                                                       type="email" value="{{ $shop->email }}" name="email"
                                                       placeholder="{{ __('Nhập email') }}" maxlength="255" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required">Địa chỉ shop (in trên bill)</label>
                                                <input class="form-control @error('address') is-invalid @enderror"
                                                       type="text" value="{{ $shop->address }}"
                                                       placeholder="{{ __('Nhập địa chỉ shop') }}" maxlength="255"
                                                       name="address" required>
                                            </div>
                                        </div>

                                        @php
                                            $aryServices = explode(',', $shopBank->services);
                                        @endphp
                                        <div class="form-group row">
                                            <div class="col">
                                                <label for="services">Dịch vụ mặc định</label>
                                                <select class="form-control form-rounded form-control" id="services" name="services[]" multiple="multiple">
                                                    @foreach ($serviceList as $key=>$value)
                                                        <option value="{{ $key }}" {{ in_array($key, $aryServices) ? 'selected="selected"' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label for="cycle_cod">Lịch đối soát<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Lịch tự động đối soát các đơn hàng của shop"></i></label>
                                                <select class="form-control" id="cycle_cod" name="cycle_cod"
                                                        title="Chọn thời điểm đối soát"
                                                        value="{{ $shopBank->cycle_cod }}">
                                                    @foreach ($cycleCodList as $key=>$value)
                                                        <option value="{{ $key }}" {{ $key === $shopBank->cycle_cod ? 'selected="selected"' : '' }}>
                                                            {{ $value['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label for="purpose">Mục đích sử dụng</label>
                                                <select class="form-control form-rounded" id="purpose" name="purpose">
                                                    <option value="" disabled selected>Vui lòng chọn mục đích sử dụng</option>
                                                    @foreach ( \App\Modules\Orders\Constants\ShopConstant::purposes as $key => $purpose)
                                                    <option value="{{ $key }}" @if($shopBank->purpose == $key) selected @endif>{{ $purpose }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        @php
                                            $aryBranch = explode(',', $shopBank->branch);
                                        @endphp
                                        <div class="form-group row">
                                            <div class="col">
                                                <label for="branch">Ngành hàng</label>
                                                <select class="form-control form-rounded form-control" id="branch" name="branch[]" multiple="multiple">
                                                    @foreach ( \App\Modules\Orders\Constants\ShopConstant::branchs as $key => $branch)
                                                    <option value="{{ $key }}" @if( in_array($key, $aryBranch) ) selected @endif >{{ $branch['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label for="scale">Quy mô vận chuyển</label>
                                                <select class="form-control form-rounded" id="scale" name="scale">
                                                    <option value="" disabled selected>Vui lòng chọn quy mô vận chuyển</option>
                                                    @foreach ( \App\Modules\Orders\Constants\ShopConstant::scales as $key => $scale)
                                                    <option value="{{ $key }}" @if($shopBank->scale == $key) selected @endif>{{ $scale }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <br>
                                        <h4>Thông tin tài khoản</h4>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Ngân hàng</label>
                                                <input class="form-control @error('bank') is-invalid @enderror"
                                                       type="text" name="bank_name" value="{{ $shopBank->bank_name }}"
                                                       placeholder="{{ __('Chọn ngân hàng') }}" maxlength="255">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Chi nhánh</label>
                                                <input class="form-control @error('branch') is-invalid @enderror"
                                                       type="text" name="bank_branch"
                                                       value="{{ $shopBank->bank_branch }}"
                                                       placeholder="{{ __('Nhập chi nhánh') }}" maxlength="255">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Tên chủ tài khoản</label>
                                                <input class="form-control @error('stk_name') is-invalid @enderror"
                                                       type="text" name="stk_name" value="{{ $shopBank->stk_name }}"
                                                       placeholder="{{ __('Nhập tên chủ tài khoản') }}" maxlength="255">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Số tài khoản</label>
                                                <input class="form-control @error('stk') is-invalid @enderror"
                                                       type="text" name="stk" value="{{ $shopBank->stk }}"
                                                       placeholder="{{ __('Nhập số tài khoản') }}" maxlength="50">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <div class="user-details" id="list-address">
                                            @foreach($shopAddress as $key=>$address)
                                                <div class="user_data">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <h4>Địa chỉ lấy hàng mặc định - <span class="text-danger">ID Store {{ $address->id }} </span></h4>
                                                        </div>
                                                        <div class="ml-2">
                                                            <label class="c-switch c-switch-label c-switch-opposite-primary">
                                                                <input type="checkbox" class="c-switch-input input-set-add-default" name="addDefault" value="{{ $key }}"
                                                                       {{ $address->default ? 'checked' : '' }} onchange="setAddDefault(this, '{{ $key }}')">
                                                                <span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                                            </label>
                                                        </div>
                                                        <div class="ml-auto">
                                                            <button class="btn btn-sm btn-dark active remove-btn" type="button" aria-pressed="true" style="padding: 0px 2px">
                                                                <i class="c-icon cil-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-4">
                                                            <label class="required">Tên người liên hệ</label>
                                                            <input type="hidden" name="addIds[]" value="{{ $address->id }}">
                                                            <input name="addName[]" class="form-control" type="text"
                                                                   value="{{ $address->name }}" required>
                                                        </div>
                                                        <div class="col-4">
                                                            <label class="required">Số điện thoại</label>
                                                            <input name="addPhone[]" class="form-control  @error('addPhone.'.$key) is-invalid @enderror" type="text"
                                                                   value="{{ $address->phone }}" required>
                                                        </div>
                                                        <div class="col-4">
                                                            <label class="required">Địa chỉ lấy hàng</label>
                                                            <input name="addAddress[]" class="form-control" type="text"
                                                                   value="{{ $address->address }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-4">
                                                            <label class="required" for="add{{ $key }}-select1">Tỉnh/thành</label>
                                                            <select class="form-control frm-select2" id="add{{ $key }}-select1"
                                                                    name="addProvinces[]"
                                                                    onchange="changeProvinces('add{{ $key }}')">
                                                                @foreach ($provinces as $province)
                                                                    <option value="{{ $province->id }}" {{ ($address->p_id===$province->id) ? 'selected="selected"' : '' }}>{{ $province->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <label class="required" for="add{{ $key }}-select2">Quận/huyện</label>
                                                            <select class="form-control frm-select2" id="add{{ $key }}-select2"
                                                                    name="addDistricts[]"
                                                                    onchange="changeDistricts('add{{ $key }}')">
                                                                @foreach ($addDistricts[$key] as $district)
                                                                    <option value="{{ $district->id }}" {{ ($address->d_id===$district->id) ? 'selected="selected"' : '' }}>{{ $district->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-4">
                                                            <label class="required" for="add{{ $key }}-select3">Phường/xã</label>
                                                            <select class="form-control frm-select2" id="add{{ $key }}-select3"
                                                                    name="addWards[]">
                                                                @foreach ($addWards[$key] as $ward)
                                                                    <option value="{{ $ward->id }}" {{ ($address->w_id===$ward->id) ? 'selected="selected"' : '' }}>{{ $ward->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-group">
                                            <input value="Thêm địa chỉ" class="add_details" autocomplete="false"
                                                   type="button">
                                        </div>
                                    </div>
                                </div>
                                @if ($errors->any())
                                    <br>
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
    <script type="text/javascript" src="{{ asset('libs/multiselect/js/bootstrap-multiselect.min.js') }}"></script>

    <script type="application/javascript">
        $(document).ready(function() {
            $(".add_details").click(function () {
                let rand = makeid(5);
                $(".user-details").append(`<div class="user_data">
                                            <br>
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4>Địa chỉ lấy hàng</h4>
                                                </div>
                                                <div class="ml-auto">
                                                    <button class="btn btn-sm btn-dark active remove-btn" type="button" aria-pressed="true" style="padding: 0px 2px">
                                                        <i class="c-icon cil-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-4">
                                                    <label class="required">Tên người liên hệ</label>
                                                    <input name="addName[]" class="form-control" type="text" required>
                                                </div>
                                                <div class="col-4">
                                                    <label class="required">Số điện thoại</label>
                                                    <input name="addPhone[]" class="form-control" type="text" required>
                                                </div>
                                                <div class="col-4">
                                                    <label class="required">Địa chỉ lấy hàng</label>
                                                    <input name="addAddress[]" class="form-control" type="text" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-4">
                                                    <label class="required" for="` + rand + `-select1">Tỉnh/thành</label>
                                                    <select class="form-control frm-select2" id="` + rand + `-select1" name="addProvinces[]" onchange="changeProvinces('` + rand + `')">
                                                        @foreach ($provinces as $province)
                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                                        @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <label class="required" for="` + rand + `-select2">Quận/huyện</label>
                                                    <select class="form-control frm-select2" id="` + rand + `-select2" name="addDistricts[]" onchange="changeDistricts('` + rand + `')">
                                                    @foreach ($districts as $district)
                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                                    @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <label class="required" for="` + rand + `-select3">Phường/xã</label>
                                                         <select class="form-control frm-select2" id="` + rand + `-select3" name="addWards[]">
                                                            @foreach ($wards as $ward)
                    <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                                            @endforeach
                    </select>
                </div>
            </div>
        </div>`
                );
                $('.frm-select2').select2({theme: "classic"});
            });

            $("body").on("click", ".remove-btn", function (e) {
                $(this).parents('.user_data').remove();
            });

            $('#branch').multiselect({
                nonSelectedText: 'Vui lòng chọn ngành hàng',
                buttonWidth: '100%',
                buttonText: function(options, select) {
                    if (options.length === 0) {
                        return 'Vui lòng chọn ngành hàng!';
                    }
                    else {
                        return 'Có ' + options.length + ' ngành hàng được chọn!';
                    }
                }
            });

            $('#services').multiselect({
                nonSelectedText: 'Vui lòng chọn gói cước',
                buttonWidth: '100%',
                buttonText: function(options, select) {
                    if (options.length === 0) {
                        return 'Vui lòng chọn gói cước!';
                    }
                    else {
                        return 'Có ' + options.length + ' gói cước được chọn!';
                    }
                }
            });
        });

        function setAddDefault(cb, keyChecked) {
            //list-address
            if (cb.checked) {
                $('#list-address input.input-set-add-default').each(
                    function(index) {
                        let input = $(this);
                        if (index != keyChecked) {
                            input.prop('checked', false);
                        }
                    });
            }
        }

        function changeProvinces(randTxt) {
            let provinceID = document.getElementById(randTxt + '-select1').value;
            let routeApi = '{{ route('api.districts.get-by-province', ":slug") }}';
            routeApi = routeApi.replace(':slug', provinceID);

            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: routeApi,
                success: function (response) {
                    if (response.status_code === 200) {
                        let html = '';
                        response.data.forEach(function (item) {
                            html += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                        document.getElementById(randTxt + "-select2").innerHTML = html;
                        changeDistricts(randTxt);
                    }
                }
            });
        }

        function changeDistricts(randTxt) {
            let districtID = document.getElementById(randTxt + '-select2').value;
            let routeApi = '{{ route('api.wards.get-by-district', ":slug") }}';
            routeApi = routeApi.replace(':slug', districtID);

            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: routeApi,
                success: function (response) {
                    if (response.status_code === 200) {
                        let html = '';
                        response.data.forEach(function (item) {
                            html += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                        document.getElementById(randTxt + "-select3").innerHTML = html;
                    }
                }
            });
        }

        function makeid(length) {
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }
    </script>

    <script>
        $(document).ready(function() {
            $("#copy_tooltip").hover(function(){
                $("#copy_tooltip").tooltip('hide');
            });
            $("#copy_tooltip").click(function(){
                $("#copy_tooltip").tooltip('show');
                setTimeout(() => $("#copy_tooltip").tooltip('hide'), 500);
            });
            $('.copy_icon').click(function () {
                let apiToken = document.getElementById('api_token');
                apiToken.select();
                apiToken.setSelectionRange(0, 99999);
                document.execCommand("copy");
            });
        });
    </script>
@endsection

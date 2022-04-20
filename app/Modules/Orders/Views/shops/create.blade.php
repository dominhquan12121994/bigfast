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

        @media screen and (max-width: 575px) {
            .return_btn, .create_btn {
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
                        <form method="POST" action="{{ route('admin.shops.store') }}">
                            <div class="card-header justify-content-between d-sm-flex d-block">
                                <div>
                                    <i class="fa fa-align-justify"></i> <strong>{{ __('Tạo mới Shop') }}</strong>
                                </div>
                                <div>
                                    <button class="btn btn-success create_btn" type="submit">Thêm mới</button>
                                    <a href="{{ url()->previous() }}" class="btn btn-primary return_btn">Quay lại</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <h4>Thông tin cơ bản</h4>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required">Tên Shop</label>
                                                <input class="form-control @error('name') is-invalid @enderror" type="text"
                                                       placeholder="Nhập tên shop" name="name" maxlength="255"
                                                       value="{{ old('name') }}" required autofocus>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required">Số điện thoại</label>
                                                <input class="form-control @error('phone') is-invalid @enderror" type="number"
                                                       placeholder="{{ __('Nhập số điện thoại') }}" name="phone"
                                                       value="{{ old('phone') }}" maxlength="11" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required">Email</label>
                                                <input class="form-control @error('email') is-invalid @enderror" type="email"
                                                       placeholder="{{ __('Nhập email') }}" name="email"
                                                       value="{{ old('email') }}" minlength="4" maxlength="255" required autocomplete="none">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required">Mật khẩu</label>
                                                <div class="position-relative">
                                                    <input class="form-control @error('password') is-invalid @enderror" type="password" id="password"
                                                    placeholder="Nhập mật khẩu" name="password" value="{{ old('password') }}" minlength="6" required>
                                                    <svg style="position: absolute;top: 50%; right: 3%; transform: translate(-50%, -50%);" id="passwordToggle" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required">Xác nhận mật khẩu</label>
                                                <div class="position-relative">
                                                    <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" id="confirmPassword"
                                                           placeholder="Nhập lại mật khẩu" name="password_confirmation" value="{{ old('password_confirmation') }}" minlength="6" required>
                                                    <svg style="position: absolute;top: 50%; right: 3%; transform: translate(-50%, -50%);" id="confirmPasswordToggle" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required">Địa chỉ shop (in trên bill)</label>
                                                <input class="form-control @error('address') is-invalid @enderror" type="text"
                                                       placeholder="{{ __('Nhập địa chỉ shop') }}" maxlength="255" name="address"
                                                       value="{{ old('address') ?: '' }}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label for="cycle_cod">Lịch đối soát<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Lịch tự động đối soát các đơn hàng của shop"></i></label>
                                                <select class="form-control" id="cycle_cod" name="cycle_cod"
                                                        title="Chọn thời điểm đối soát">
                                                    @foreach ($cycleCodList as $key=>$value)
                                                        <option value="{{ $key }}">
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
                                                    <option value="{{ $key }}">{{ $purpose }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label for="branch">Ngành hàng</label>
                                                <select class="form-control form-rounded form-control" id="branch" name="branch[]" multiple="multiple">
                                                    @foreach ( \App\Modules\Orders\Constants\ShopConstant::branchs as $key => $branch)
                                                    <option value="{{ $key }}">{{ $branch['name'] }}</option>
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
                                                    <option value="{{ $key }}">{{ $scale }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <br>
                                        <h4>Thông tin tài khoản</h4>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Ngân hàng</label>
                                                <input class="form-control @error('bank_name') is-invalid @enderror" type="text"
                                                       placeholder="{{ __('Chọn ngân hàng') }}" name="bank_name"
                                                       value="{{ old('bank_name') ?: '' }}" >
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Chi nhánh</label>
                                                <input class="form-control @error('bank_branch') is-invalid @enderror" type="text"
                                                       placeholder="{{ __('Nhập chi nhánh') }}" maxlength="255" name="bank_branch"
                                                       value="{{ old('bank_branch') ?: '' }}" >
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Tên chủ tài khoản</label>
                                                <input class="form-control @error('stk_name') is-invalid @enderror" type="text"
                                                       placeholder="{{ __('Nhập tên chủ tài khoản') }}" maxlength="255" name="stk_name"
                                                       value="{{ old('stk_name') ?: '' }}" >
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Số tài khoản</label>
                                                <input class="form-control @error('stk') is-invalid @enderror" type="text"
                                                       placeholder="{{ __('Nhập số tài khoản') }}" maxlength="50" name="stk"
                                                       value="{{ old('stk') ?: '' }}" >
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <div class="user-details">
                                            <div class="user_data">
                                                <h4>Địa chỉ lấy hàng</h4>
                                                <div class="form-group row">
                                                    <div class="col-4">
                                                        <label class="required">Tên người liên hệ</label>
                                                        <input name="addName[]" class="form-control" type="text" required placeholder="Nhập tên người liên hệ">
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="required">Số điện thoại</label>
                                                        <input name="addPhone[]" class="form-control @error('addPhone.0') is-invalid @enderror" type="text" required placeholder="Nhập số điện thoại">
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="required">Địa chỉ lấy hàng</label>
                                                        <input name="addAddress[]" class="form-control" type="text" required placeholder="Nhập địa chỉ">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-4">
                                                        <label class="required" for="add1-select1">Tỉnh/thành</label>
                                                        <select class="form-control frm-select2" id="add1-select1" name="addProvinces[]" onchange="changeProvinces('add1')">
                                                            @foreach ($provinces as $province)
                                                                <option value="{{ $province->id }}">{{ $province->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="required" for="add1-select2">Quận/huyện</label>
                                                        <select class="form-control frm-select2" id="add1-select2" name="addDistricts[]" onchange="changeDistricts('add1')">
                                                            @foreach ($districts as $district)
                                                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="required" for="add1-select3">Phường/xã</label>
                                                        <select class="form-control frm-select2" id="add1-select3" name="addWards[]">
                                                            @foreach ($wards as $ward)
                                                                <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input value="Thêm địa chỉ" class="add_details" autocomplete="false" type="button">
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
    <script type="text/javascript" src="{{ asset('libs/multiselect/js/bootstrap-multiselect.min.js') }}"></script>

    <script type="application/javascript">
        $(document).ready(function() {

            const myInput1 = document.getElementById('password');
            myInput1.oncopy = e => e.preventDefault();

            const myInput2 = document.getElementById('confirmPassword');
            myInput2.onpaste = e => e.preventDefault();

            $(".add_details").click(function () {
                let rand = makeid(5);
                $(".user-details").append(`<div class="user_data">
                                            <br>
                                            <h4>
                                                Địa chỉ lấy hàng
                                                <button class="btn btn-sm btn-dark active remove-btn" type="button" aria-pressed="true" style="padding: 0px 2px">
                                                    <i class="c-icon cil-trash"></i>
                                                </button>
                                            </h4>
                                            <div class="form-group row">
                                                <div class="col-4">
                                                    <label class="required">Tên người liên hệ</label>
                                                    <input name="addName[]" class="form-control" type="text" required placeholder="Nhập tên người liên hệ">
                                                </div>
                                                <div class="col-4">
                                                    <label class="required">Số điện thoại</label>
                                                    <input name="addPhone[]" class="form-control" type="text" required placeholder="Nhập số điện thoại">
                                                </div>
                                                <div class="col-4">
                                                    <label class="required">Địa chỉ lấy hàng</label>
                                                    <input name="addAddress[]" class="form-control" type="text" required placeholder="Nhập địa chỉ">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-4">
                                                    <label class="required" for="`+rand+`-select1">Tỉnh/thành</label>
                                                    <select class="form-control frm-select2" id="`+rand+`-select1" name="addProvinces[]" onchange="changeProvinces('`+rand+`')">
                                                        @foreach ($provinces as $province)
                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                                        @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <label class="required" for="`+rand+`-select2">Quận/huyện</label>
                                                    <select class="form-control frm-select2" id="`+rand+`-select2" name="addDistricts[]" onchange="changeDistricts('`+rand+`')">
                                                        @foreach ($districts as $district)
                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                                        @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <label class="required" for="`+rand+`-select3">Phường/xã</label>
                                                    <select class="form-control frm-select2" id="`+rand+`-select3" name="addWards[]">
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

            $("body").on("click",".remove-btn",function(e){
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
        });

        function changeProvinces(randTxt) {
            let provinceID = document.getElementById(randTxt+'-select1').value;
            let routeApi = '{{ route('api.districts.get-by-province', ":slug") }}';
            routeApi = routeApi.replace(':slug', provinceID);

            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: routeApi,
                success: function(response){
                    if (response.status_code === 200) {
                        let html = '';
                        response.data.forEach(function (item) {
                            html += '<option value="'+ item.id +'">'+ item.name +'</option>';
                        });
                        document.getElementById(randTxt+"-select2").innerHTML = html;
                        changeDistricts(randTxt);
                    }
                }
            });
        }
        function changeDistricts(randTxt) {
            let districtID = document.getElementById(randTxt+'-select2').value;
            let routeApi = '{{ route('api.wards.get-by-district', ":slug") }}';
            routeApi = routeApi.replace(':slug', districtID);

            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: routeApi,
                success: function(response){
                    if (response.status_code === 200) {
                        let html = '';
                        response.data.forEach(function (item) {
                            html += '<option value="'+ item.id +'">'+ item.name +'</option>';
                        });
                        document.getElementById(randTxt+"-select3").innerHTML = html;
                    }
                }
            });
        }

        function makeid(length) {
            var result           = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for ( var i = 0; i < length; i++ ) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }

        $('#passwordToggle').click(function() {
            if ($('#password').attr('type') === 'password') {
                $('#password').attr('type', 'text');
                $('#passwordToggle').html(
                    `
                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299l.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                    <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884l-12-12 .708-.708 12 12-.708.708z"/>
                    `);
            } else {
                $('#password').attr('type', 'password');
                $('#passwordToggle').html(
                    `
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                    `);
            }
        });

        $('#confirmPasswordToggle').click(function() {
            if ($('#confirmPassword').attr('type') === 'password') {
                $('#confirmPassword').attr('type', 'text');
                $('#confirmPasswordToggle').html(`
                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299l.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                    <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884l-12-12 .708-.708 12 12-.708.708z"/>
                    `);
            } else {
                $('#confirmPassword').attr('type', 'password');
                $('#confirmPasswordToggle').html(`
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                    `);
            }
        });
    </script>
@endsection

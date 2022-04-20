@extends('layouts.authBase')

@section('script-header')
    <link rel="stylesheet" href="{{ asset('libs/multiselect/css/bootstrap-multiselect.min.css') }}" type="text/css">
    <script type="text/javascript" src="{{ asset('libs/multiselect/js/bootstrap-multiselect.min.js') }}"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="{{ asset('css/pages/shops/auth/login.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container register">
    <div class="row">
        <div class="col-md-3 register-left">
            <img src="https://image.ibb.co/n7oTvU/logo_white.png" alt=""/>
            <h3>Welcome to</h3>
            <h3><b>BIGFAST</b></h3>
            <p>Tận tâm với từng đơn hàng!</p>
        </div>
        <div class="col-md-9 register-right">
            <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link login-active {{ (old('name', null) == null && $tab_active == 'login') ? 'active' : '' }}" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="{{ $tab_active == 'login' ? 'true' : 'false' }}">Đăng nhập</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link register-active {{ (old('name', null) != null || $tab_active == 'register') ? 'active' : '' }}" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="{{ $tab_active == 'register' ? 'true' : 'false' }}">Đăng ký</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show {{ (old('name', null) == null && $tab_active == 'login') ? 'active' : '' }}" id="login" role="tabpanel" aria-labelledby="login-tab">
                    <div class="register-heading">
                        <h3 class="">Đăng nhập</h3>
                        <p class="description">Chào ngày mới, cùng chốt nhiều đơn hôm nay nhé!</p>
                    </div>
                    <div class="row register-form">
                        <div class="col-md-8 offset-md-2 input_area">
                            @if(old('email'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert" id="mess-error-login">
                                    Thông tin đăng nhập không chính xác.<br>Vui lòng kiểm tra lại!
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form method="post" action="/login">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <input type="hidden" id="device_token" name="device_token" value="">
                                <div class="form-group">
                                    <label class="required" for="email">Tài khoản</label>
                                    <input type="text" class="form-control form-rounded @error('email') is-invalid @enderror" id="email" name="email" placeholder="Nhập tài khoản" value="{{ old('email') }}" required
                                        oninvalid="this.setCustomValidity('Hãy nhập tài khoản đăng nhập!')" oninput="setCustomValidity('')"/>
                                </div>
                                <div class="form-group">
                                    <label class="required" for="email">Mật khẩu</label>
                                    <div class="position-relative">
                                        <input id="password" type="password" class="form-control form-rounded @error('password') is-invalid @enderror" name="password" placeholder="Nhập mật khẩu" value="" required
                                        oninvalid="this.setCustomValidity('Hãy nhập mật khẩu!')" oninput="setCustomValidity('')"/>
                                        <i id="show_password" class="cil-low-vision position-absolute"></i>
                                    </div>
                                </div>
                                <div class="form-check form-check-inline">
                                <input class="form-check-input" id="inline-checkbox1" type="checkbox" name="checkStaff">
                                <label class="form-check-label" for="inline-checkbox1">Bấm vào đây nếu bạn là nhân viên</label>
                                </div>
                                <input type="submit" class="btnRegister"  value="Đăng nhập"/>
                            </form>
                            <div class="register-footer">
                                <p>Bạn chưa có tài khoản <b class="register-active link-tabs">Đăng ký ngay</b> <b>/</b> <b class="forget-active link-tabs"> <a target="_blank" href="{{ route('shop.password.reset') }}"> Quên mật khẩu ?</a></b> </p>
                                <!-- <p>Nhân sự BigFast bấm <a class="link-tabs-2" href="/admin">vào đây</a> để đăng nhập</p> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show {{ (old('name', null) != null || $tab_active == 'register') ? 'active' : '' }}" id="register" role="tabpanel" aria-labelledby="register-tab">
                    <div class="register-heading">
                        <h3 class="">Tạo tài khoản BigFast</h3>
                        <p>BigFast luôn đồng hành cùng bạn.</p>

                        @if ($errors->any())
                            <div class="col-md-8 offset-md-2">
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li style="text-align: left">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    <form method="post" action="/register">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="row register-form">
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label for="purpose">Mục đích sử dụng</label>
                                    <select class="form-control form-rounded" id="purpose" name="purpose">
                                        <option value="" disabled selected>Vui lòng chọn mục đích sử dụng</option>
                                        @foreach ($purposes as $key => $purpose)
                                        <option value="{{ $key }}">{{ $purpose }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="required" for="name">Tên Shop</label>
                                    <input type="text" class="form-control form-rounded @error('name') is-invalid @enderror" id="name" name="name"
                                           placeholder="Nhập tên Shop" value="{{ old('name') }}" required
                                           oninvalid="this.setCustomValidity('Nhập tên Shop')" oninput="setCustomValidity('')"/>
                                </div>
                                <div class="form-group">
                                    <label class="required" for="email">Địa chỉ email</label>
                                    <input type="email" class="form-control form-rounded @error('email') is-invalid @enderror" id="email" name="email"
                                        placeholder="Ví dụ: nguyenvana@mail.com" value="{{ old('email') }}" required
                                        oninvalid="this.setCustomValidity('Vui lòng nhập địa chỉ email hợp lệ!')" oninput="setCustomValidity('')"/>
                                </div>

                                <div class="form-group">
                                    <label class="required" for="email">Mật khẩu</label>
                                    <div class="position-relative">
                                        <input id="new_password" type="password" class="form-control form-rounded @error('password') is-invalid @enderror" name="password"
                                        placeholder="Nhập mật khẩu" value="" required/>
                                        <i id="show_new_password" class="cil-low-vision position-absolute"></i>
                                    </div>
                                    <div class="invalid-feedback">
                                        Please choose a username.
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label for="branch">Ngành hàng</label>
                                    <div class="branch_pick">
                                        <select class="form-control form-rounded" id="branch" name="branch[]" multiple="multiple">
                                            @foreach ($branchs as $key => $branch)
                                            <option value="{{ $key }}">{{ $branch['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="scale">Quy mô vận chuyển</label>
                                    <select class="form-control form-rounded" id="scale" name="scale">
                                        <option value="" disabled selected>Vui lòng chọn quy mô vận chuyển</option>
                                        @foreach ($scales as $key => $scale)
                                        <option value="{{ $key }}">{{ $scale }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="required" for="phone">Số điện thoại</label>
                                    <input type="text" maxlength="11" minlength="10" id="phone" name="phone" class="form-control form-rounded @error('phone') is-invalid @enderror"
                                           placeholder="Nhập số điện thoại" value="{{ old('phone') }}" required
                                           oninvalid="this.setCustomValidity('Hãy nhập số điện thoại!')" oninput="setCustomValidity(''); this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"/>
                                </div>
                                <div class="form-group">
                                    <label class="required" for="email">Nhập lại mật khẩu</label>
                                    <input id="confirm_new_password" type="password" class="form-control form-rounded @error('password') is-invalid @enderror" name="password_confirmation"
                                        placeholder="Nhập lại mật khẩu để xác nhận" value="" required/>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 offset-md-3">
                                <div>
                                    <p class="mb-0 text-center">Bằng việc nhấn "Đăng ký", bạn đồng ý với
                                        <b>Điều khoản dịch vụ</b> và <b>Quy định Riêng tư Cá nhân</b>
                                        của chúng tôi.</p>
                                </div>
                                <input type="submit" onclick="return checkform()" class="btnRegister" value="Đăng ký"/>
                                <p class="text-center mt-4">Bạn đã có tài khoản? <b class="login-active link-tabs">Đăng nhập ngay</b></p>
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
    {{--<script src="{{ asset('libs/select2/select2.min.js') }}"></script>--}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/pages/shops/auth/login.min.js') }}"></script>

    {{--    // script firebase--------------------------------------------------}}
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <script src="{{ asset('/js/firebase.js') }}"></script>
    {{--    // ...script firebase--------------------------------------------------}}

    <script>
        // console.log('lấy device token từ firebase trong màn login');
        console.log('quyền thông báo hiện tại: ' + Notification.permission);
        firebase.initializeApp({
            apiKey: '{{ config('firebase.init.apiKey') }}',
            authDomain: '{{ config('firebase.init.authDomain') }}',
            projectId: '{{ config('firebase.init.projectId') }}',
            storageBucket: '{{ config('firebase.init.storageBucket') }}',
            messagingSenderId: '{{ config('firebase.init.messagingSenderId') }}',
            appId: '{{ config('firebase.init.appId') }}',
            measurementId: '{{ config('firebase.init.measurementId') }}',
        });
        const messaging = firebase.messaging();
        messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken({vapidKey: '{{ config('firebase.init.vapidKey') }}'})
            })
            .then(function (token) {
                // console.log('this is token: ' + token);
                document.getElementById('device_token').value = token;
                // console.log(document.getElementById('device_token'));
            })
            .catch(function (err) {
                console.log('đã xảy ra lỗi: ' + err);
            });
    </script>
@endsection

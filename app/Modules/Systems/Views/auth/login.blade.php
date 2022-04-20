@extends('layouts.authBase')

@section('script-header')
    <style>
        #show_password,
        #show_new_password {
            top: 50%;
            left: 95%;
            transform: translate(-50%, -50%);
            cursor: pointer;
        }
    </style>
@endsection

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card-group">
                    <div class="card p-4">
                        <div class="card-body">
                            <h1>Hệ thống quản trị BigFast</h1>
                            <p class="text-muted">Đăng nhập với tài khoản quản trị</p>
                            <form method="POST" name="frmAdminLogin" action="{{ route('login') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <input type="hidden" id="device_token" name="device_token" value="">
                                <div class="mb-3">
                                    <input class="form-control" type="text" placeholder="{{ __('Địa chỉ email') }}"
                                           name="email" value="{{ old('email') }}" required autofocus>
                                </div>
                                <div class="mb-3 position-relative">
                                    <input class="form-control" id="new_password" type="password" placeholder="{{ __('Mật khẩu') }}"
                                           name="password" required>
                                    <i id="show_new_password" class="cil-low-vision position-absolute input-group-addon"></i>
                                </div>
                                <div class="mb-2">
                                    <button class="btn btn-block btn-primary px-4" type="submit">{{ __('Đăng Nhập') }}</button>
                                </div>
                                <div class="float-right">
                                    <b class="forget-active link-tabs"> <a target="_blank" href="{{ route('password.reset', '') }}"> Quên mật khẩu ?</a></b>
                                </div>
                            </form>
                            @if ($errors->any())
                                <br>
                                <div class="alert alert-danger mb-0">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            {{--<div class="col-6 text-right">--}}
                                {{--<a href="{{ route('password.request') }}"--}}
                                   {{--class="btn btn-link px-0">{{ __('Forgot Your Password?') }}</a>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>
                {{--<div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">--}}
                {{--<div class="card-body text-center">--}}
                {{--<div>--}}
                {{--<h2>Sign up</h2>--}}
                {{--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>--}}
                {{--@if (Route::has('password.request'))--}}
                {{--<a href="{{ route('register') }}" class="btn btn-primary active mt-3">{{ __('Register') }}</a>--}}
                {{--@endif--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
    </div>

@endsection

@section('javascript')
    {{--    // script firebase--------------------------------------------------}}
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <script src="{{ asset('/js/firebase.js') }}"></script>
    {{--    // ...script firebase--------------------------------------------------}}

    <script>
        // console.log('lấy device token từ firebase trong màn login');
        // console.log('quyền thông báo hiện tại: ' + Notification.permission);
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
                // console.log('đã xảy ra lỗi: ' + err);
            });

        $('#show_new_password').click(function() {
            if ($("#new_password").attr('type') === 'password') {
                $("#new_password").attr('type', 'text');
                $(this).removeClass('cil-low-vision').addClass('fa fa-eye');
            } else {
                $("#new_password").attr('type', 'password');
                $(this).removeClass('fa fa-eye').addClass('cil-low-vision');
            }
        });
    </script>
@endsection

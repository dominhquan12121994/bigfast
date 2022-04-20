@extends('layouts.baseShop')

@section('css')
    <style>
        #show_password {
            width: 32px;
            position: relative;
            top: -28px;
            left: 100%;
            text-align: right;
            cursor: pointer;
        }
        .cil-low-vision {
            margin-left: -25px;
        }
        .fa.fa-eye {
            margin-left: -40px;
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
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }                       

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
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
                        <form method="POST" action="{{ route('shop.shop-staffs.update', array('shop_staff' => $staff->id)) }}">
                            <div class="card-header justify-content-between d-block d-sm-flex">
                                <div>
                                    <i class="fa fa-align-justify"></i> Cập nhật nhân viên: <strong>{{ $staff->name }}</strong>
                                </div>
                                <div>
                                    <button class="btn btn-success update_btn" type="submit">Cập nhật</button>
                                    <a href="{{ route('shop.shop-staffs.index') }}" class="btn btn-primary return_btn">Trở về</a></div>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <h4>Thông tin cơ bản</h4>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required" for="name">Tên nhân viên</label>
                                                <input class="form-control @error('name') is-invalid @enderror"
                                                       type="text" value="{{ $staff->name }}" placeholder="Nhập tên nhân viên"
                                                       name="name" id="name" autofocus
                                                       maxlength="255" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required">Số điện thoại</label>
                                                <input class="form-control @error('phone') is-invalid @enderror"

                                                       type="text" value="{{ $staff->phone }}" name="phone"
                                                       placeholder="{{ __('Nhập số điện thoại') }}" maxlength="255"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label class="required">Email</label>
                                                <input class="form-control @error('email') is-invalid @enderror"
                                                       type="email" value="{{ $staff->email }}" name="email"
                                                       placeholder="{{ __('Nhập email') }}" maxlength="255" required>
                                            </div>
                                        </div>

                                        <div class="form-group custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck1">
                                            <label class="custom-control-label" for="customCheck1">Đổi mật khẩu</label>
                                        </div>

                                        <div id="boxChangePassword"></div>
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
<script type="application/javascript">
    jQuery(function ($) {
        $('body').on('click', '#customCheck1', function () {
            if (this.checked) {
                document.getElementById("boxChangePassword").innerHTML = `<div class="form-group mb-0">
                            <label class="required">Mật khẩu</label>
                            <input class="form-control" type="password" placeholder="Nhập mật khẩu" id="password" name="password" required>
                            <i id="show_password" class="cil-low-vision"></i>
                        </div>
                        <div class="form-group mb-3">
                            <label class="required">Xác nhận mật khẩu</label>
                            <input class="form-control" type="password" placeholder="Nhập lại mật khẩu" id="password_confirmation" name="password_confirmation" required>
                        </div>`;
                $('#show_password').click(function () {
                    if ($("#password").attr('type') == 'password') {
                        $("#password").attr('type', 'text');
                        $(this).removeClass('cil-low-vision').addClass('fa fa-eye');
                    } else {
                        $("#password").attr('type', 'password');
                        $(this).removeClass('fa fa-eye').addClass('cil-low-vision');
                    }
                    if ($("#password_confirmation").attr('type') == 'password') {
                        $("#password_confirmation").attr('type', 'text');
                    } else {
                        $("#password_confirmation").attr('type', 'password');
                    }
                });
            } else {
                document.getElementById("boxChangePassword").innerHTML = ``;
            }
        });

    });
</script>
@endsection

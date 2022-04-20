@extends('layouts.base')

@section('css')
    <style>
        #show_password,#show_password_old {
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

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        @media screen and (max-width: 575px) {
            .update_btn, .return_btn {
                width: 100%;
                margin-top: 10px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-8 col-lg-6 col-xl-6">
                    <form method="POST" action="{{ route('admin.update.password') }}">
                        @method('PUT')
                        <div class="card">
                            <div class="card-header justify-content-between d-sm-flex d-block">
                                <div>
                                    <i class="fa fa-align-justify"></i> Đổi mật khẩu
                                </div>
                                <div>
                                    <button class="btn btn-success update_btn" type="submit">Cập nhật</button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary return_btn">Quay
                                        lại</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                                <div class="form-group mb-0">
                                    <label class="required">Mật khẩu hiện tại</label>
                                    <input class="form-control" type="password" placeholder="Nhập mật khẩu" id="password_old" name="password_old" required>
                                    <i id="show_password_old" class="cil-low-vision"></i>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="required">Mật khẩu mới</label>
                                    <input class="form-control" type="password" placeholder="Nhập mật khẩu" id="password" name="password" required>
                                    <i id="show_password" class="cil-low-vision"></i>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="required">Nhập lại mật khẩu mới</label>
                                    <input class="form-control" type="password" placeholder="Nhập lại mật khẩu" id="password_confirmation" name="password_confirmation" required>
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script type="application/javascript">
        $('#show_password_old').click(function () {
            if ($("#password_old").attr('type') == 'password') {
                $("#password_old").attr('type', 'text');
                $(this).removeClass('cil-low-vision').addClass('fa fa-eye');
            } else {
                $("#password_old").attr('type', 'password');
                $(this).removeClass('fa fa-eye').addClass('cil-low-vision');
            }
        });
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
    </script>
@endsection

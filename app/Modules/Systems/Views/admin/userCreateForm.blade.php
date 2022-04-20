@extends('layouts.base')

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

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        @media screen and (max-width: 575px) {
            .create_btn, .return_btn {
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
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        <div class="card">
                            <div class="card-header d-block d-sm-flex justify-content-between">
                                <div>
                                    <i class="fa fa-align-justify"></i> {{ __('Thêm mới nhân viên') }}
                                </div>
                                <div>
                                    <button class="btn btn-success create_btn" type="submit">{{ __('Thêm mới') }}</button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary return_btn">{{ __('Quay lại') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <br>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                @method('POST')
                                <div class="form-group">
                                    <label class="required" for="select1">Phân quyền</label><i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Vai trò của người dùng trong hệ thống"></i>
                                    <select class="form-control" id="select1" name="role[]" multiple="multiple">
                                        @foreach ($roles as $role)
                                            @if($role->name != 'superadmin' && $role->name != 'shop')
                                                <option value="{{ $role->name }}">
                                                    {{ \App\Modules\Systems\Constants\PermissionConstant::roles[$role->name]['name'] }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="required">Tên nhân viên</label>
                                    <input class="form-control" type="text" placeholder="Nhập tên nhân viên" name="name"
                                           value="{{ old('name') }}" required autofocus>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="required">Số điện thoại</label>
                                    <input class="form-control" type="number" placeholder="Nhập số điện thoại"
                                           name="phone"
                                           value="{{ old('phone') }}" maxlength="11" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="required">Email</label>
                                    <input class="form-control" type="email" placeholder="Địa chỉ Email" name="email" autocomplete="none"
                                           value="{{ old('email') }}" required>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="required">Mật khẩu</label>
                                    <input class="form-control" type="password" id="password" autocomplete="off"
                                           placeholder="Nhập mật khẩu"
                                           name="password" required>
                                    <i id="show_password" class="cil-low-vision"></i>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="required">Xác nhận mật khẩu</label>
                                    <input class="form-control" type="password" id="password_confirmation"
                                           placeholder="Nhập lại mật khẩu"
                                           name="password_confirmation" required>
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
    <script>
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

@extends('layouts.baseShop')

@section('css')
    <style>
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
                        <form method="POST" action="{{ route('shop.staff.update', array('staff' => $staff->id)) }}">
                            <div class="card-header justify-content-between d-block d-sm-flex">
                                <div>
                                    <i class="fa fa-align-justify"></i> Cập nhật nhân viên: <strong>{{ $staff->name }}</strong>
                                </div>
                                <div>
                                    <button class="btn btn-success update_btn" type="submit">Cập nhật</button>
                                </div>
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

@endsection

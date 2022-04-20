@extends('layouts.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <form method="POST" action="{{ route('admin.districts.store') }}">
                        <div class="card-header d-flex justify-content-between">
                            <div>
                                <i class="fa fa-align-justify"></i> Thêm mới Quận huyện
                            </div>
                            <div>
                                <button class="btn btn-success" type="submit">Thêm mới</button>
                                <a href="{{ url()->previous() }}" class="btn btn-primary">Quay lại</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <div class="form-group row">
                                <div class="col">
                                    <label class="required" for="select1">Tỉnh thành</label>
                                    <select class="form-control frm-select2" id="select1" name="p_id">
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required" for="select1">Loại</label>
                                    <select class="form-control" id="select1" name="type">
                                        <option value="noi">Nội thành</option>
                                        <option value="ngoai">Ngoại thành</option>
                                        <option value="huyen">Huyện/xã</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required">Mã</label>
                                    <input class="form-control @error('code') is-invalid @enderror" type="text" placeholder="Nhập mã quận huyện" name="code" maxlength="10" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required">Quận huyện</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="Nhập tên quận huyện" name="name" maxlength="255" required>
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

@endsection

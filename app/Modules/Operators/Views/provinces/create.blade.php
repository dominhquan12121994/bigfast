@extends('layouts.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <form method="POST" action="{{ route('admin.provinces.store') }}">
                        <div class="card-header d-flex justify-content-between">
                            <div>
                                <i class="fa fa-align-justify"></i> Thêm mới tỉnh thành
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
                                    <label class="required" for="select1">Khu vực</label>
                                    <select class="form-control" id="select1" name="zone">
                                        <option value="bac">Miền Bắc</option>
                                        <option value="trung">Miền Trung</option>
                                        <option value="nam">Miền Nam</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col">
                                    <label class="required">Mã</label>
                                    <input class="form-control @error('code') is-invalid @enderror" type="number" placeholder="Nhập mã tỉnh thành" name="code" value="{{ old('code') }}" maxlength="10" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required">Tỉnh thành</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="Nhập tên tỉnh thành" name="name" value="{{ old('name') }}" maxlength="255" required>
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

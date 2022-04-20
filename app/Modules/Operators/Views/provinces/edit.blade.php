@extends('layouts.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <form method="POST" action="{{ route('admin.provinces.update', array('province' => $province->id)) }}">
                        <div class="card-header d-flex justify-content-between">
                            <div>
                                <i class="fa fa-align-justify"></i> Cập nhật: {{ $province->name }}
                            </div>
                            <div>
                                <button class="btn btn-success" type="submit">Cập nhật</button>
                                <a href="{{ url()->previous() }}" class="btn btn-primary">Quay lại</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            @method('PUT')
                            <div class="form-group row">
                                <div class="col">
                                    <label class="required" for="select1">Khu vực</label>
                                    <select class="form-control" id="select1" name="zone" value="{{ $province->zone }}">
                                        <option value="bac">Miền Bắc</option>
                                        <option value="trung">Miền Trung</option>
                                        <option value="nam">Miền Nam</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required">Mã</label>
                                    <input class="form-control @error('code') is-invalid @enderror" type="number" placeholder="Nhập mã tỉnh thành" name="code" value="{{ old('code', $province->code) }}" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required">Tỉnh thành</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="Nhập tên tỉnh thành" name="name" value="{{ old('name', $province->name) }}" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required">Từ khóa</label>
                                    <input class="form-control @error('keyword') is-invalid @enderror" type="text" placeholder="Nhập từ khóa liên quan" name="keyword" value="{{ old('keyword', $province->keyword) }}" required>
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

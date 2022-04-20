@extends('layouts.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <form method="POST" action="{{ route('admin.wards.update', array('ward' => $ward->id)) }}">
                        <div class="card-header d-flex justify-content-between">
                            <div>
                                <i class="fa fa-align-justify"></i> Cập nhật: {{ $ward->title }}
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
                                    <label class="required" for="select1">Tỉnh thành</label>
                                    <select class="form-control frm-select2" id="select1" name="p_id" value="{{ $ward->p_id }}" onchange="changeDistricts()">
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->id }}" {{ $ward->p_id === $province->id ? 'selected="selected"' : '' }}>{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required" for="select2">Quận huyện</label>
                                    <select class="form-control frm-select2" id="select2" name="d_id" value="{{ $ward->d_id }}">
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}" {{ $ward->d_id === $district->id ? 'selected="selected"' : '' }}>{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required">Mã</label>
                                    <input class="form-control @error('code') is-invalid @enderror" type="text" placeholder="Nhập mã phường xã" name="code" value="{{ $ward->code }}" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required">Phường xã</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="Nhập tên phường xã" name="name" value="{{ $ward->name }}" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required">Từ khóa</label>
                                    <input class="form-control @error('keyword') is-invalid @enderror" type="text" placeholder="Nhập từ khóa liên quan" name="keyword" value="{{ $ward->keyword }}" required>
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
    <script type="application/javascript">
        let apiDistrictsByProvince = `{{ route('api.districts.get-by-province', ":slug") }}`;
    </script>

    <script src="{{ asset('js/pages/operators/wards/create-edit.min.js') }} "></script>
@endsection

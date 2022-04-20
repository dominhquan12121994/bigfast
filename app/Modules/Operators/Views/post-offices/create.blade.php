@extends('layouts.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <form method="POST" action="{{ route('admin.post-offices.store') }}">
                        <div class="card-header d-flex justify-content-between">
                            <div><i class="fa fa-align-justify"></i> Thêm mới bưu cục</div>
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
                                    <select class="form-control frm-select2" id="select1" name="p_id" onchange="changeProvinces()">
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required" for="select2">Quận huyện</label>
                                    <select class="form-control frm-select2" id="select2" name="d_id" onchange="changeDistricts()">
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required" for="select3">Phường xã</label>
                                    <select class="form-control frm-select2" id="select3" name="w_id">
                                        @foreach ($wards as $ward)
                                            <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label class="required">Tên Bưu Cục</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="Nhập tên bưu cục" name="name" maxlength="255" required>
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
        let apiWardsByDistrict = `{{ route('api.wards.get-by-district', ":slug") }}`;
        let headerAuthorization = `Bearer {{ $currentUser->passport_token }}`;
    </script>

    <script type="text/javascript" src="{{ asset('js/pages/operators/post-offices/create-edit.min.js') }}"></script>
@endsection

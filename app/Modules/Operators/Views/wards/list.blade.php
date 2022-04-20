@extends('layouts.base')

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> Quản lý phường xã
                            <span class="float-right">
                                @if($currentUser->can('action_wards_create'))
                                    <a href="{{ route('admin.wards.create') }}" class="btn btn-primary">Thêm mới</a>
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            <form class="form-inline" action="" method="get">
                                <label for="select1" class="mr-1" for="exampleInputName2">Tỉnh thành</label>
                                <div class="form-group">
                                    <select class="form-control frm-select2" id="select1" name="p"
                                            onchange="changeDistricts()">
                                        <option value="0">Chọn tỉnh thành để quản lý</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->id }}" {{ $arrFilter['p_id'] === $province->id ? 'selected="selected"' : '' }}>{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <label for="select2" class="mx-1" for="exampleInputEmail2">Quận huyện</label>
                                <div class="form-group">
                                    <select class="form-control frm-select2" id="select2" name="d">
                                        <option value="0">Chọn quận huyển để quản lý</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}" {{ $arrFilter['d_id'] === $district->id ? 'selected="selected"' : '' }}>{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <div class="form-group">
                                    <label for="name">Từ khoá</label>&nbsp;
                                    <input class="form-control form-control-sm" id="name" name="n" type="text"
                                           value="{{ $arrFilter['name'] }}" placeholder="Nhập từ khoá tìm kiếm">
                                </div>
                                &nbsp;&nbsp;
                                <button class="btn btn-sm btn-primary" type="submit">Tìm kiếm</button>&nbsp;
                                <a href="{{ route('admin.wards.index') }}" class="btn btn-sm btn-danger">làm lại</a>
                            </form>
                            <br>
                            <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped">
                                <thead>
                                <tr>
                                    <th>Tỉnh thành</th>
                                    <th>Quận huyện</th>
                                    <th>Mã</th>
                                    <th>Phường xã</th>
                                    @if($currentUser->can('action_wards_update'))
                                        <th width="40px"></th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($wards as $ward)
                                    <tr>
                                        <td>{{ $ward->provinces->name }}</td>
                                        <td>{{ $ward->districts->name }}</td>
                                        <td>{{ $ward->code }}</td>
                                        <td><strong>{{ $ward->name }}</strong></td>
                                        @if($currentUser->can('action_wards_update'))
                                            <td class="pl-1 pr-1">
                                                <a href="{{ route('admin.wards.edit', array('ward' => $ward->id)) }}"
                                                   class="btn btn-sm btn-block btn-primary" title="Edit">Sửa</a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $wards->withQueryString()->links() }}
                        </div>
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

    <script src="{{ asset('js/pages/operators/wards/list.min.js') }} "></script>
@endsection


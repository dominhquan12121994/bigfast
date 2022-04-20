@extends('layouts.base')

@section('css')

@endsection

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> Quản lý quận huyện
                            <span class="float-right">
                                @if($currentUser->can('action_districts_create'))
                                    <a href="{{ route('admin.districts.create') }}" class="btn btn-primary">Thêm mới</a>
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            <form class="form-inline" action="" method="get">
                                <label for="select1" class="mr-1" for="exampleInputName2">Tỉnh thành</label>
                                <div class="form-group">
                                    <select class="form-control frm-select2" id="select1" name="p">
                                        <option value="0">Chọn tỉnh thành quản lý</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->id }}" {{ $arrFilter['p_id'] === $province->id ? 'selected="selected"' : '' }}>{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <div class="form-group">
                                    <label for="name">Từ khoá</label>&nbsp;
                                    <input class="form-control form-control-sm" id="name" name="n" type="text"
                                           value="{{ $arrFilter['name'] }}" placeholder="Nhập tên huyện cần tìm">
                                </div>
                                &nbsp;&nbsp;
                                <button class="btn btn-sm btn-primary" type="submit">Tìm kiếm</button>&nbsp;
                                <a href="{{ route('admin.districts.index') }}" class="btn btn-sm btn-danger">Làm lại</a>
                            </form>
                            <br>
                            <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table1">
                                <thead>
                                <tr>
                                    <th>Tỉnh thành</th>
                                    <th>Loại</th>
                                    <th>Mã</th>
                                    <th>Quận huyện</th>
                                    <th>Phường xã</th>
                                    @if($currentUser->can('action_provinces_update'))
                                        <th width="40px"></th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($districts as $district)
                                    <tr>
                                        <td>{{ $district->provinces->name }}</td>
                                        <td>{{ \App\Modules\Operators\Constants\ZoneConstant::district_type[$district->type] }}</td>
                                        <td>{{ $district->code }}</td>
                                        <td><strong>{{ $district->name }}</strong></td>
                                        <td>
                                            <a href="{{ route('admin.wards.index', array('p' => $district->p_id, 'd' => $district->id)) }}">{{ count($district->wards) . ' phường/xã' }}</a>
                                        </td>
                                        @if($currentUser->can('action_provinces_update'))
                                            <td class="pl-1 pr-1">
                                                <a href="{{ route('admin.districts.edit', array('district' => $district->id)) }}"
                                                   class="btn btn-sm btn-block btn-primary" title="Edit">Sửa</a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $districts->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('javascript')

@endsection


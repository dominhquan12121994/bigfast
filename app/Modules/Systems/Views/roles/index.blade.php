@extends('layouts.base')

@section('content')


    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Phân quyền</h4>
                            <div>
                                <a class="btn btn-primary disabled" href="{{ route('admin.roles.create') }}">Thêm mới</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered datatable">
                                <thead>
                                <tr>
                                    <th>Tên quyền</th>
                                    <th>Ưu tiên</th>
                                    <th>Thời gian tạo</th>
                                    <th>Thời gian cập nhật</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($roles as $role)
                                    @if($role->name != 'shop')
                                        <tr>
                                            <td>
                                                {{ isset($rolesContant[$role->name]) ? $rolesContant[$role->name]['name'] : $role->name }}
                                            </td>
                                            <td>
                                                {{ $role->hierarchy }}
                                            </td>
                                            <td>
                                                {{ date('d-m-Y H:i', strtotime($role->created_at)) }}
                                            </td>
                                            <td>
                                                {{ date('d-m-Y H:i', strtotime($role->updated_at)) }}
                                            </td>
                                            <td>
                                                <a class="btn btn-success"
                                                   href="{{ route('admin.roles.up', ['id' => $role->id]) }}">
                                                    <i class="cil-arrow-thick-top"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-success"
                                                   href="{{ route('admin.roles.down', ['id' => $role->id]) }}">
                                                    <i class="cil-arrow-thick-bottom"></i>
                                                </a>
                                            </td>
                                            <td>
                                                @if($role->name != 'superadmin')
                                                    <a href="{{ route('admin.roles.show', $role->id ) }}"
                                                       class="btn btn-primary">Cấu hình</a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.roles.edit', $role->id ) }}" class="btn btn-primary disabled">Chỉnh
                                                    sửa</a>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.roles.destroy', $role->id ) }}" method="POST">
                                                    @method('DELETE')
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                    <button class="btn btn-danger">Xoá</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection

@section('javascript')

@endsection

@extends('layouts.base')

@section('css')
    <link href="{{ asset('libs/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> Nhân viên
                            <span class="float-right">
                            @if($currentUser->can('action_users_create'))
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Thêm nhân viên</a>
                            @endif
                        </span>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table1">
                                <thead>
                                <tr>
                                    <th>Tài khoản</th>
                                    <th>Họ tên</th>
                                    <th>Nhóm quyền<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Vai trò của người dùng trong hệ thống"></i></th>
                                    <th>Số điện thoại</th>
                                    <th>E-mail</th>
                                    <th>Ngày bắt đầu<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Ngày tạo tài khoản"></i></th>
                                    {{--<th style="width: 40px"></th>--}}
                                    <th style="width: 40px"></th>
                                    <th style="width: 40px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            @foreach($user->getRoleNames() as $role)
                                            <span class="badge badge-{{ $rolesConfig[$role]['color'] }}">
                                                {{ $rolesConfig[$role]['name'] }}
                                            </span>
                                            @endforeach
                                        </td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ date('d-m-Y', strtotime($user->created_at)) }}</td>
                                        {{--<td class="pl-0 pr-0">--}}
                                        {{--<a href="{{ route('admin.users.show', array('user' => $user->id)) }}" class="btn btn-sm btn-block btn-primary">Xem</a>--}}
                                        {{--</td>--}}

                                        <td class="pl-1 pr-1">
                                            @if($user->getRoleNames()[0] !== 'superadmin')
                                                @if($currentUser->can('action_users_update'))
                                                <a href="{{ route('admin.users.edit', array('user' => $user->id)) }}"
                                                   class="btn btn-sm btn-block btn-primary">Sửa</a>
                                                @endif
                                            @endif
                                        </td>

                                        <td class="pl-0 pr-0">
                                            @if($user->getRoleNames()[0] !== 'superadmin')
                                                @if($currentUser->can('action_users_delete'))
                                                    @if( $you->id !== $user->id )
                                                        <form action="{{ route('admin.users.destroy', $user->id ) }}"
                                                              method="POST">
                                                            @method('DELETE')
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                            <button class="btn btn-sm btn-block btn-danger"
                                                                    onclick="return confirm('Thao tác này không thể hoàn tác! Bạn có chắc chắn xoá?');">
                                                                Xoá
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script type="application/javascript">
        $(document).ready(function () {
            $('#table1').DataTable({
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ bản ghi mỗi trang",
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "info": "_PAGE_/_PAGES_ trang",
                    "infoEmpty": "Không tìm thấy dữ liệu",
                    "infoFiltered": "(tìm kiếm trong tổng số _MAX_ bản ghi)",
                    "decimal": "",
                    "emptyTable": "Không tìm thấy dữ liệu",
                    "infoPostFix": "",
                    "thousands": ",",
                    "loadingRecords": "Đang tải...",
                    "processing": "Đang tải...",
                    "search": "Tìm kiếm:",
                    "paginate": {
                        "first": "Đầu",
                        "last": "Cuối",
                        "next": "Sau",
                        "previous": "Trước"
                    },
                    "aria": {
                        "sortAscending": ": xếp tăng dần",
                        "sortDescending": ": xếp giảm dần"
                    }
                },
                stateSave: true,
            });
        });
    </script>
@endsection


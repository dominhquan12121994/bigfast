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
                            <i class="fa fa-align-justify"></i> Quản lý tỉnh thành
                            <span class="float-right">
                                @if($currentUser->can('action_provinces_create'))
                                    <a href="{{ route('admin.provinces.create') }}"
                                       class="btn btn-primary">{{ __('Thêm mới') }}</a>
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table1">
                                <thead>
                                <tr>
                                    <th>Khu vực</th>
                                    <th>Mã</th>
                                    <th>Tên</th>
                                    <th>Quận huyện</th>
                                    @if($currentUser->can('action_provinces_update'))
                                        <th width="40px"></th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($provinces as $province)
                                    <tr>
                                        <td>{{ $province->zone }}</td>
                                        <td>{{ $province->code }}</td>
                                        <td><strong>{{ $province->name }}</strong></td>
                                        <td>
                                            <a href="{{ route('admin.districts.index', array('p' => $province->id)) }}">{{ count($province->districts) . ' quận/huyện' }}</a>
                                        </td>
                                        @if($currentUser->can('action_provinces_update'))
                                            <td class="pl-1 pr-1">
                                                <a href="{{ route('admin.provinces.edit', array('province' => $province->id)) }}"
                                                   class="btn btn-sm btn-primary" title="Edit">Sửa</a>
                                            </td>
                                        @endif
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


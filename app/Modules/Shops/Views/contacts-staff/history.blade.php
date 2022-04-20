@extends('layouts.baseShop')

@section('css')

@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
					<div class="card">
						<div class="card-header">
							<i class="fa fa-align-justify"></i> {{ __('Lịch sử trợ giúp') }}
						</div>
						<div class="card-body">
                            <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <th>Người xử lý</th>
                                        <th>Hoạt động</th>
                                        <th>Ngày</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ( $history as $index1 => $item1)
                                        <tr>
                                            <td>{{ $item1->name }}</td>
                                            <td>
                                                @if ($item1->action == 'create')
                                                    Thêm mới trợ giúp
                                                @elseif ($item1->action == 'update')
                                                    @if ($item1->column == 'assign_id')
                                                        Gán người trợ giúp cho {{ $item1->new }}
                                                    @elseif ( $item1->column == 'file_path' )
                                                        Đã thay đổi file tải lên
                                                    @elseif ( $item1->column == 'status' )
                                                        Sửa trường {{ $item1->column }} từ '{{ $status[$item1->old]}}' thành '{{ $status[$item1->new] }}'
                                                    @else
                                                        Sửa trường {{ $item1->column }} từ '{{$item1->old}}' thành '{{$item1->new}}'
                                                    @endif
                                                @else
                                                    Xóa trợ giúp
                                                @endif
                                            </td>
                                            <td> {{ date('d-m-Y H:i', strtotime($item1->created_at)) }}</td>
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

@endsection

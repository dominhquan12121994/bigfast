@extends('layouts.baseShop')

@section('css')
    <style type="text/css">
        #table1 th {
            border-top: none;
        }

        @media screen and (max-width: 575px) {
            .search_btn, .reset_btn, .create_btn, .search_input {
                width: 100% !important;
                margin-top: 8px;
            }
        }
    </style>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-header d-block d-sm-flex justify-content-between">
                            <div>
                                <i class="fa fa-align-justify"></i> <strong>{{ __('Danh sách nhân viên') }}</strong>
                            </div>
                            <div>
                                <a href="{{ route('shop.shop-staffs.create') }}" class="btn btn-sm btn-primary create_btn">Thêm mới nhân viên</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table1">
                                <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th width="150px">Số điện thoại</th>
                                    <th>Email</th>
                                    <th width="130px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($staffs as $staff)
                                    <tr>
                                        <td><strong>{{ $staff->name }}</strong></td>
                                        <td>{{ $staff->phone }}</td>
                                        <td>{{ $staff->email }}</td>
                                        <td class="pl-0 pr-0 box-actions">
                                                <a href="{{ route('shop.shop-staffs.edit', array('shop_staff' => $staff->id)) }}" class="btn btn-sm btn-pill btn-primary" title="Edit">Sửa</a>
                                                <form action="{{ route('shop.shop-staffs.destroy', $staff->id ) }}" method="POST" style="display: inline">
                                                    @method('DELETE')
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                    <button class="btn btn-sm btn-pill btn-danger" title="Delete" onclick="return confirm('Thao tác này không thể hoàn tác! Bạn có chắc chắn xoá?');">
                                                        Xoá
                                                    </button>
                                                </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-auto mr-auto"></div>
                                <div class="col-auto">
                                    {{ $staffs->withQueryString()->links() }}
                                </div>
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


@extends('layouts.base')

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
                                <form class="form-inline" method="GET">
                                    <input type="text" class="form-control form-control-sm search_input" placeholder="Nhập tên/email/sđt shop cần tìm" name="search"
                                           maxlength="255" value="{{ $search }}" autofocus style="width: 200px"><span class="d-none d-sm-inline">&nbsp;</span>
                                    <button class="btn btn-sm btn-success search_btn" type="submit">Tìm kiếm</button><span class="d-none d-sm-inline">&nbsp;</span>
                                    <a class="btn btn-sm btn-info reset_btn" href="{{ route('admin.shops.index') }}">Làm lại</a>
                                </form>
                            </div>
                            <div>
                                @if($currentUser->can('action_shops_create'))
                                    <a href="{{ route('admin.shops.create') }}" class="btn btn-sm btn-primary create_btn">Thêm mới Shop</a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table1">
                                <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th width="150px">Số điện thoại</th>
                                    <th>Email</th>
                                    <th style="min-width: 220px">Lịch đối soát</th>
                                    <th>Nhân viên</th>
                                    <th width="200px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($shops as $shop)
                                    <tr>
                                        <td><strong>{!! $shop->name !!}</strong></td>
                                        <td>{{ $shop->phone }}</td>
                                        <td>{{ $shop->email }}</td>
                                        <td>{{ App\Modules\Orders\Constants\ShopConstant::bank['cycle_cod'][$shop->bank->cycle_cod]['name'] }}</td>
                                        <td>{{ count($shop->staff) }}</td>
                                        <td class="pl-0 pr-0 box-actions">
                                            <a href="{{ route('admin.shop-staff.index', array('shop_id' => $shop->id)) }}" class="btn btn-sm btn-pill btn-info" title="Staff">Nhân viên</a>
                                            @if($currentUser->can('action_shops_update'))
                                                <a href="{{ route('admin.shops.edit', array('shop' => $shop->id)) }}" class="btn btn-sm btn-pill btn-primary" title="Edit">Sửa</a>
                                            @endif
                                            @if($currentUser->can('action_shops_delete'))
                                                <form action="{{ route('admin.shops.destroy', $shop->id ) }}" method="POST" style="display: inline">
                                                    @method('DELETE')
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                    <button class="btn btn-sm btn-pill btn-danger" title="Delete" onclick="return confirm('Thao tác này không thể hoàn tác! Bạn có chắc chắn xoá?');">
                                                        Xoá
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-auto mr-auto"></div>
                                <div class="col-auto">
                                    {{ $shops->withQueryString()->links() }}
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


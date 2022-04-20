<div class="card-header d-block d-sm-flex justify-content-between">
    <div class="flex">
        @if($shop)
            <span class="badge badge-success">Shop</span> {!! $shop->name !!} - {{ $shop->phone }}<br>
            {{ $shop->address }}
        @else
            <select id="shopSelected" class="form-control" onchange="shopSelectedChange()"></select>
            @if(count($arrShopSelected) > 0)
                <p class="my-2 my-sm-0 mb-0">
                    @foreach($arrShopSelected as $key => $shopSelected)
                        <span class="badge badge-{{ array('success', 'danger', 'info', 'warning', 'light')[$key] }}"
                              onclick="shopSelectedRedis({{ $shopSelected['id'] }});" style="cursor: pointer">{{ $shopSelected['name'] }}</span>
                    @endforeach
                </p>
            @endif
        @endif
    </div>
    <div class="d-block d-sm-flex justify-content-end action_container">
        @if($shop)
            @if ( $currentUser->can('action_orders_create') )
                <a href="{{ route('admin.orders.create', array('shop_id' => $shop->id)) }}">
                    <button type="button" class="btn btn-primary mx-0 mr-sm-1">Thêm mới order</button>
                </a>
            @endif
            @if ( $currentUser->can('action_orders_assign_ship') )
                <a href="{{ route('admin.assign-ship.show', array('shop' => $shop->id)) }}">
                    <button type="button" class="btn btn-info mx-0 mr-sm-1">Gán Ship</button>
                </a>
            @endif
            <a><button type="button" class="btn btn-info mx-0 mr-sm-1" data-toggle="modal" data-target="#modal_aside_left">Tìm kiếm</button></a>
            @if ( $currentUser->can('action_orders_import') )
                <form action="{{ route('admin.orders.import', array('shop_id' => $shop->id)) }}" method="POST" id="frmImportExcel" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="file" id="fileImport" name="fileImport" onchange="document.getElementById('frmImportExcel').submit();" style="display: none"/>
                    <button type="button" class="btn btn-warning btn-submit"
                            onclick="document.getElementById('fileImport').click()"
                            value="Select a File">Import đơn hàng</button>
                    <a class="excel_img" href="{{ asset('excel/BigFast_MauDanhSachDonHang_2021.xlsx') }}" download>
                        <img src="{{ asset('icons/excel.png') }}" title="Bấm để tải file mẫu" height="34px" style="cursor: pointer" alt="" />
                    </a>
                </form>
            @endif
        @else
            <a><button type="button" class="btn btn-secondary mx-0 mr-sm-1" disabled>Thêm mới order</button></a>
            @if ( $currentUser->can('action_orders_assign_ship') )
                <a href="{{ route('admin.assign-ship.show') }}">
                    <button type="button" class="btn btn-success mx-0 mr-sm-1">Gán Ship</button>
                </a>
            @endif
            <a><button type="button" class="btn btn-info mx-0 mr-sm-1" data-toggle="modal" data-target="#modal_aside_left">Tìm kiếm</button></a>
            @if ( $currentUser->can('action_orders_import') )
                <form action="{{ route('admin.orders.import', array('shop_id' => 0)) }}" method="POST" id="frmImportExcel" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="file" id="fileImport" name="fileImport" onchange="document.getElementById('frmImportExcel').submit();" style="display: none"/>
                    <button type="button" class="btn btn-warning btn-submit"
                            onclick="document.getElementById('fileImport').click()"
                            value="Select a File">Import đơn hàng</button>
                    <a class="excel_img" href="{{ asset('excel/BigFast_MauDanhSachDonHang_2021.xlsx') }}" download>
                        <img src="{{ asset('icons/excel.png') }}" title="Bấm để tải file mẫu" height="34px" style="cursor: pointer" alt="" />
                    </a>
                </form>
            @endif
        @endif
    </div>
</div>

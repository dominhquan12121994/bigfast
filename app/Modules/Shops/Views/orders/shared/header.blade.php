<div class="card-header d-flex justify-content-between">
    <div class="flex">
        @if($shop)
            <span class="badge badge-success">Shop</span> {!! $shop->name !!} - {{ $shop->phone }}<br>
            {{ $shop->address }}
        @else
            @if(count($arrShopSelected) > 0)
                <p class="mb-1">
                    @foreach($arrShopSelected as $key => $shopSelected)
                        <span class="badge badge-{{ array('success', 'danger', 'info', 'warning', 'light')[$key] }}"
                              onclick="shopSelectedRedis({{ $shopSelected['id'] }});" style="cursor: pointer">{{ $shopSelected['name'] }}</span>
                    @endforeach
                </p>
            @endif
            <select id="shopSelected" class="form-control" onchange="shopSelectedChange()"></select>
        @endif
    </div>
    <div class="d-flex justify-content-end">
        @if($shop)
            <a class="mr-sm-1" href="{{ route('shop.orders.create-by-shop', array('shop_id' => $shop->id)) }}">
                <button type="button" class="btn btn-primary">Thêm mới order</button>
            </a>
            <a><button type="button" class="btn btn-info mx-0 mr-sm-1" data-toggle="modal" data-target="#modal_aside_left">Tìm kiếm</button></a>
            <form action="{{ route('shop.orders.import', array('shop_id' => $shop->id)) }}" method="POST" id="frmImportExcel" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="file" id="fileImport" name="fileImport" onchange="document.getElementById('frmImportExcel').submit();" style="display: none"/>
                <button type="button" class="btn btn-warning"
                        onclick="document.getElementById('fileImport').click()"
                        value="Select a File">Import đơn hàng</button>
                <a href="{{ asset('excel/BigFast_MauDanhSachDonHang_2021.xlsx') }}" download>
                    <img src="{{ asset('icons/excel.png') }}" title="Bấm để tải file mẫu" height="34px" style="cursor: pointer" alt="" />
                </a>
            </form>&nbsp;
        @else
            <a>
                <button type="button" class="btn btn-secondary" disabled>Thêm mới order</button>
            </a>&nbsp;
        @endif
    </div>
</div>

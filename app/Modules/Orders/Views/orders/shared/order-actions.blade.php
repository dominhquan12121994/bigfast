<div id="boxActions" style="display: none">
    <div class="d-flex">
        <div class="flex-grow-1 ml-3 mr-5">
            <b>Đã chọn</b><br>
            <b id="countOrderSelected" style="font-size: 30px; color: orangered">0</b> đơn hàng
        </div>
        <div class="d-flex align-items-end" style="position: relative; min-width: 400px">
            <div style="position: absolute; top: 5px; right: 10px">Tổng phí: <span id="txtTotalFee"></span>đ&nbsp;&nbsp;-&nbsp;&nbsp;Tổng COD: <span id="txtTotalCod"></span>đ</div>
            <div id="boxActionBtn" style="display: @if($filter['status_detail'] && !empty(\App\Modules\Orders\Constants\OrderConstant::status[$filter['status']]['detail'][$filter['status_detail']]['next'])) block @else none @endif"><button class="btn btn-sm btn-pill btn-info mr-2" type="button" data-toggle="modal" data-target="#orderActionModal">Thao tác đơn được chọn</button></div>
            @if($currentUser->can('action_orders_export'))
                <div><button class="btn btn-sm btn-pill btn-warning mr-2" type="button" id="btnExportExcel">Xuất excel</button></div>
            @endif
            @if($currentUser->can('action_orders_print'))
                @foreach( \App\Modules\Operators\Constants\PrintTemplatesConstant::size as $key => $item )
                    <div><button class="btn btn-sm btn-pill btn-primary mr-2" type="button" onclick="printListOrder('{{$key}}', 1)">In khổ {{$key}}</button></div>
                @endforeach
            @endif
        </div>
    </div>
</div>

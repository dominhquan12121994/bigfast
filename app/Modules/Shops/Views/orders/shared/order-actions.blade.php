<div id="boxActions" style="display: none">
    <div class="d-flex">
        <div class="flex-grow-1 ml-3 mr-5">
            <b>Đã chọn</b><br>
            <b id="countOrderSelected" style="font-size: 30px; color: orangered">0</b> đơn hàng
        </div>
        <div class="d-flex align-items-end">
            <div><button class="btn btn-sm btn-pill btn-warning mr-2" type="button" id="btnExportExcel">Xuất excel</button></div>
            @foreach( \App\Modules\Operators\Constants\PrintTemplatesConstant::size as $key => $item )
                <div><button class="btn btn-sm btn-pill btn-primary mr-2" type="button" onclick="printListOrder('{{$key}}', 1)">In khổ {{$key}}</button></div>
            @endforeach
        </div>
    </div>
</div>
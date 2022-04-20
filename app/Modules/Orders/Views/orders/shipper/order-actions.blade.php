<div id="boxActions" style="display: none">
    <div class="d-flex">
        <div class="flex-grow-1 ml-3 mr-5">
            <b>Đã chọn</b><br>
            <b id="countOrderSelected" style="font-size: 30px; color: orangered">0</b> đơn hàng
        </div>
        <div class="d-flex align-items-end">
            @if($filter['status_detail'] && !empty(\App\Modules\Orders\Constants\OrderConstant::status[$filter['status']]['detail'][$filter['status_detail']]['next']))
                <div><button class="btn btn-sm btn-pill btn-info mr-2" type="button" data-toggle="modal" data-target="#orderActionModal">Thao tác đơn được chọn</button></div>
            @endif
        </div>
    </div>
</div>
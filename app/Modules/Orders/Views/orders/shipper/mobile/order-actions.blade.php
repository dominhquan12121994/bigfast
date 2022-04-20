<div id="boxActions" style="display: none">
    <div class="d-flex">
        <div class="flex-fill" style="width: 95px;">
            <b>Đã chọn</b><br>
            <b id="countOrderSelected" style="font-size: 30px; color: orangered">0</b> đơn hàng
        </div>
        @foreach( \App\Modules\Operators\Constants\PrintTemplatesConstant::size as $key => $item )
            <div class="flex-fill bg-primary action-moblie" onclick="printListOrder('{{$key}}', 1)">
                In khổ {{$key}}
            </div>
        @endforeach
    </div>
</div>
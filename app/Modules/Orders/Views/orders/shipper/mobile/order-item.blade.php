@if ( count($orders) > 0 )
<div class="row m-2">
    <div class="form-check form-check-inline mr-5">
        <input type="checkbox" id="selectall"/>
        <label class="form-check-label" for="cbxFullData" style="cursor: pointer">&nbsp;Chọn tất cả đơn</label>
    </div>
</div>
@endif
<div class="row m-2 listOrder">
    @php
        $count = 0;
    @endphp
    @foreach($orders as $key => $order)
        @php
            $count++;
        @endphp

        @include('Orders::orders.shipper.mobile.order-item-detail', [
            'count' => $count,
            'order' => $order,
            'constantStatus'    => OrderConstant::status,
            'constantPayfees'   => OrderConstant::payfees,
        ])

    @endforeach
</div>
<div class="text-center load-more" style="width:100%; display:none;">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
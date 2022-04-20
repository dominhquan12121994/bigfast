<a href="{{ route('shop.order-staff.show', array('order_staff' => $order->id)) }}" class="btn btn-sm btn-pill btn-info float-left">Tra cứu</a>
<!-- <a href="{{ route('shop.orders.edit', array('order' => $order->id)) }}" class="btn btn-sm btn-pill btn-primary float-left">Sửa đơn</a> -->
<button type="button" onclick="showContacts('{{ $order->lading_code }}')" class="btn btn-sm btn-pill btn-warning float-left">Trợ giúp</button>
@if($currentUser->can('action_orders_print'))
<div class="nav-item dropdown float-left">
    <span class="status btn btn-sm btn-pill btn-danger float-left"  data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">In đơn</span>
    <div class="dropdown-menu status-menu" style="margin: 0px;">
        @foreach( \App\Modules\Operators\Constants\PrintTemplatesConstant::size as $type => $type_item )
            <a class="badge bg-light text-dark dropdown-item status-dropdown" href="javascript:void(0)" onclick="printOrder('{{$order->id}}', '{{$type}}')">In khổ {{$type}}</a>
        @endforeach
    </div>
</div>
@endif
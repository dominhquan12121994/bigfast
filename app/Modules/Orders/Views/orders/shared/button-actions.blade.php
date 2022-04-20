<a href="{{ route('admin.orders.show', array('order' => $order->id)) }}" {{ isset($search['searchs']) ? 'target="_blank"' : '' }} class="btn btn-sm btn-pill btn-info float-left">Tra cứu</a>
@if($currentUser->can('action_orders_update'))
    <a href="{{ route('admin.orders.edit', array('order' => $order->id)) }}" {{ isset($search['searchs']) ? 'target="_blank"' : '' }} class="btn btn-sm btn-pill btn-primary float-left">Sửa đơn</a>
@endif
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
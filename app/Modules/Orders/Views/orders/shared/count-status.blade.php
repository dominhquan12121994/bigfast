<div class="row m-0 mb-2 status_container">
    @if($shop && $countDraft)
    <a href="{{ route('admin.orders.drafts', array('shop_id' => $shop->id)) }}" class="btn btn-pill btn-light m-1 {{ $statusActive === -2 ? 'btn-dark' : 'btn-light' }}" type="button" aria-pressed="true">
        Đơn nháp <span class="badge badge-danger">{{ $countDraft }}</span>
    </a>
    @endif
    @if(count($countStatus) > 0)
        @foreach($countStatus as $status => $statusItem)
            @if(in_array($userRole, array('shipper', 'pickup')) && $status === 0)
                @continue;
            @endif
            @php
            $arrRoute = array('status' => $status);
            if($shop) $arrRoute['shop'] = $shop->id;
            @endphp
            <a href="{{ route('admin.orders.index', $arrRoute) }}" class="btn btn-pill m-1 {{ $status == $statusActive ? 'btn-dark' : 'btn-light' }}" type="button" aria-pressed="true">
                {{ $statusItem['name'] }}{!! $statusItem['count'] ? ' <span class="badge badge-danger">'. $statusItem['count'] .'</span>' : '' !!}
            </a>
        @endforeach
    @endif
</div>

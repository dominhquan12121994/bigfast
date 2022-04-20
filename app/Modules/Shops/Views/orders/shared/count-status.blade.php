<div class="row m-0 mb-2">
    @if($shop && $countDraft)
    <a href="{{ route('shop.orders-drafts') }}" class="btn btn-pill btn-light m-1 {{ $statusActive === -2 ? 'btn-dark' : 'btn-light' }}" type="button" aria-pressed="true">
        Đơn nháp <span class="badge badge-danger">{{ $countDraft }}</span>
    </a>
    @endif
    @if(count($countStatus) > 0)
        @foreach($countStatus as $status => $statusItem)
            @php
                $arrRoute = array('status' => $status);
            @endphp
            <a href="{{ route('shop.orders.index', $arrRoute) }}" class="btn btn-pill m-1 {{ $status == $statusActive ? 'btn-dark' : 'btn-light' }}" type="button" aria-pressed="true">
                {{ $statusItem['name'] }}{!! $statusItem['count'] ? ' <span class="badge badge-danger">'. $statusItem['count'] .'</span>' : '' !!}
            </a>
        @endforeach
    @endif
</div>

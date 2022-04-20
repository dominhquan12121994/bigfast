<style>
    .slider {
        width: 100%;
        height: 35px;
        display: flex;
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }
    .slide {
        height: 20px;
        flex-shrink: 0;
    }
</style>

<div class="m-0 mb-2 slider" id="slider">
    @if($shop && $countDraft)
    <a href="{{ route('admin.orders.drafts', array('shop_id' => $shop->id)) }}" class="slide badge badge-danger m-1 {{ $statusActive === -2 ? 'btn-dark' : 'btn-light' }}"
    {{ $statusActive === -2 ? 'id=slider-active' : '' }} type="button" aria-pressed="true">
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
            @endphp
            <a href="{{ route('admin.orders.index', $arrRoute) }}" class="slide badge badge-danger m-1 {{ $status == $statusActive ? 'btn-dark' : 'btn-light' }}"
            {{ $status == $statusActive ? 'id=slider-active' : '' }} aria-pressed="true">
                {{ $statusItem['name'] }}{!! $statusItem['count'] ? ' <span class="badge badge-danger">'. $statusItem['count'] .'</span>' : '' !!}
            </a>
        @endforeach
    @endif
</div>

<script>
    let elmnt_active = document.getElementById("slider-active");
    let left_active = parseInt($(elmnt_active).offset().left) - 40;
    let elmnt = document.getElementById("slider");
    elmnt.scrollLeft = left_active;
</script>

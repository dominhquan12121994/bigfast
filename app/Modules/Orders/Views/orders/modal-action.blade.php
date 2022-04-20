@if($filter['status_detail'])
    <div class="nav-tabs-boxed nav-tabs-boxed-left">
        <ul class="nav nav-tabs" role="tablist" style="width: 20%">
            @php
                $count = 0;
            @endphp
            @foreach( $statusList[$filter['status']]['detail'][$filter['status_detail']]['next'] as $key => $value)
                <li class="nav-item">
                    <a class="nav-link {{ $count ? '' : 'active' }}" data-toggle="tab" href="#tab{{ $key  }}"
                       role="tab" aria-controls="tab{{ $key }}" aria-selected="{{ $count ? false : true }}">{{ $value }}</a></li>
                @php
                    $count++;
                @endphp
            @endforeach
        </ul>
        <div class="tab-content" style="width: 80%">
            @php
                $count = 0;
            @endphp
            @foreach( $statusList[$filter['status']]['detail'][$filter['status_detail']]['next'] as $key => $value)
                <div class="tab-pane {{ $count ? '' : 'active' }}" id="tab{{ $key }}" role="tabpanel">
                    <div class="row justify-content-md-center">
                        <div class="col-10">
                            @if($key===12)
                                @if($filter['status_detail']==13)
                                    @include('Orders::orders.modal.re-shipper-pick')
                                @else
                                    @include('Orders::orders.modal.assign-shipper-pick')
                                @endif
                            @endif
                            @if($key===13)
                                @include('Orders::orders.modal.pick-fail')
                            @endif
                            @if($key===21)
                                @include('Orders::orders.modal.pick-success')
                            @endif
                            @if($key===22)
                                @include('Orders::orders.modal.warehouse')
                            @endif
                            @if($key===23)
                                @if($filter['status_detail']==34 || $filter['status_detail']==41)
                                    @include('Orders::orders.modal.re-shipper-send')
                                @else
                                    @include('Orders::orders.modal.assign-shipper-send')
                                @endif
                            @endif
                            @if($key===24)
                                @include('Orders::orders.modal.send-fail')
                            @endif
                            @if($key===25)
                                @include('Orders::orders.modal.store')
                            @endif
                            @if($key===31)
                                @include('Orders::orders.modal.set-refund')
                            @endif
                            @if($key===32)
                                @include('Orders::orders.modal.assign-shipper-refund')
                            @endif
                            @if($key===33)
                                @include('Orders::orders.modal.refund-fail')
                            @endif
                            @if($key===34)
                                @include('Orders::orders.modal.confirm-refund')
                            @endif
                            @if($key===35)
                                @include('Orders::orders.modal.approval-refund')
                            @endif
                            @if($key===36)
                                @include('Orders::orders.modal.warehouse-refund')
                            @endif
                            @if($key===41)
                                @include('Orders::orders.modal.confirm-resend')
                            @endif
                            @if($key===51)
                                @include('Orders::orders.modal.send-success')
                            @endif
                            @if($key===52)
                                @include('Orders::orders.modal.refund-success')
                            @endif
                            @if($key===61)
                                @include('Orders::orders.modal.cancel-orders')
                            @endif
                            @if($key===71)
                                @include('Orders::orders.modal.missing')
                            @endif
                            @if($key===72)
                                @include('Orders::orders.modal.damaged')
                            @endif
                            @if($key===73)
                                @include('Orders::orders.modal.missing-confirm')
                            @endif
                            @if($key===74)
                                @include('Orders::orders.modal.damaged-confirm')
                            @endif
                            @if($key===81)
                                @include('Orders::orders.modal.reconcile-send')
                            @endif
                            @if($key===82)
                                @include('Orders::orders.modal.reconcile-refund')
                            @endif
                            @if($key===83)
                                @include('Orders::orders.modal.reconcile-missing')
                            @endif
                            @if($key===84)
                                @include('Orders::orders.modal.reconcile-damaged')
                            @endif
                            @if($key===91)
                                @include('Orders::orders.modal.collect-money')
                            @endif
                        </div>
                    </div>
                </div>
                @php
                    $count++;
                @endphp
            @endforeach
        </div>
    </div>
@endif
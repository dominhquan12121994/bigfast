<div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 p-2">
    <div class="card col p-2 mb-0 singlechkboxBig">
        <div class="card-body pt-0 pb-0">
            <div class="row mb-2">
                <div class="col p-0">
                    <span class="float-left badge badge-pill badge-dark">{{ $count }}</span>
                    @php
                        $status = $order->status;
                        $status_detail = $order->status_detail;
                        $status = ($status === 9) ? 5 : $status;
                        $status_detail = ($status_detail === 91) ? 51 : $status_detail;
                        $status_detail_style = $constantStatus[$status]['detail'][$status_detail];
                    @endphp
                    <span class="float-right badge badge-{{ isset($status_detail_style['color']) ? $status_detail_style['color'] : 'warning' }}">
                        {{ $constantStatus[$status]['detail'][$status_detail]['name'] }}
                    </span>
                </div>
            </div>
            <div class="row mt-1 box-info-order-draft">
                <div class="col-12 p-0">
                    <i class="cil-code"></i> : {{ $order->lading_code }}<br>
                    <i class="cil-user"></i> : {{ $order->receiver->name }}<br>
                    <i class="cil-phone"></i> : {{ $order->receiver->phone }}<br>
                    <i class="cil-address-book"></i> : {{ $order->receiver->provinces->name }}<br>
                    <span class="badge badge-{{ array('payfee_sender' => 'danger', 'payfee_receiver' => 'info')[$order->payfee] }}">
                        {{ $constantPayfees[$order->payfee] }}
                    </span><br>
                    <b>Thu hộ: </b>{{ number_format($order->cod) . ' vnđ' }}<br>
                    <b>Phí giao hàng: </b>
                    {{ number_format($order->transport_fee) . ' vnđ' }}<br>
                </div>
            </div>
            <div class="row form-group" style="display:none;">
                <div class="col p-0 mt-1">
                    <div class="form-check float-right">
                        @if(1===2)
                            <input type="checkbox" class="singlechkbox" name="cbx_order_id[]" value="{{ $order->id }}"/>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

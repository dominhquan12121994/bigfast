@php
    $incurred_total_cod = $incurred_fee[$order->id]['incurred_total_cod'] ?? 0;
    $incurred_fee_transport = $incurred_fee[$order->id]['incurred_fee_transport'] ?? 0;
    $incurred_fee_cod = $incurred_fee[$order->id]['incurred_fee_cod'] ?? 0;
    $transport = ($order->payfee === 'payfee_receiver') ? $order->transport_fee + $incurred_fee_transport : 0;
@endphp
<span class="badge-light">
    {{ number_format($order->transport_fee + $incurred_fee_transport ) }}
</span>
<span class="badge badge-{{ array('payfee_sender' => 'danger', 'payfee_receiver' => 'info')[$order->payfee] }}">
    {{ \App\Modules\Orders\Constants\OrderConstant::payfees[$order->payfee] }}
</span>
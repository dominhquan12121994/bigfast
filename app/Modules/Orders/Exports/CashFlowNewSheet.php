<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class CashFlowNewSheet implements FromView, WithTitle
{
    private $_orderFeeInterface;
    private $_orderServices;
    private $_payload;
    private $_feeType;

    public function __construct($orderFeeInterface, $orderServices, $payload, $feeType)
    {
        $this->_orderFeeInterface = $orderFeeInterface;
        $this->_orderServices = $orderServices;
        $this->_payload = $payload;
        $this->_feeType = $feeType;
    }

    public function view(): View
    {
        $fee_type = explode(',', $this->_feeType);
        $orderFees = $this->_orderFeeInterface->getMore(array(
            'shop_id' => (int)$this->_payload['shopId'],
            'fee_type' => $fee_type,
            'date' => array((int)$this->_payload['timeBegin'], (int)$this->_payload['timeEnd'])
        ));
        $groupByOrder = $orderFees->groupBy('order_id');
        $ordersId = array_unique($orderFees->transform(function ($item){ return $item->order_id; })->toArray());

        $orders = array();
        if (count($ordersId) > 0) {
            foreach ($ordersId as $orderId) {
                $order = $this->_orderServices->getOne($orderId);
                $orders[] = json_decode($order);
            }
        }
        
        return view('Orders::exports.cashflow', [
            'orders' => $orders,
            'groupByOrder' => $groupByOrder,
            'fee_type' => $fee_type,
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        $title = 'BigFast - Đơn hàng';
        if ($this->_feeType === 'total_cod,incurred_total_cod') {
            $title = 'Tổng-cod';
        }
        if ($this->_feeType === 'transport,cod,insurance,incurred_fee_transport,incurred_fee_cod') {
            $title = 'Tổng-phí';
        } 
        if ($this->_feeType === 'refund_cod,refund_transport') {
            $title = 'Tiền-bồi-hoàn';
        } 
    
        return $title;
    }
}
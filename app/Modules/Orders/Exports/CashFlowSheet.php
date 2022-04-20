<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class CashFlowSheet implements FromView, WithTitle
{
    private $ordersId;
    private $_orderServices;

    public function __construct($ordersId, $orderServices)
    {
        $this->ordersId = $ordersId;
        $this->_orderServices = $orderServices;
    }

    public function view(): View
    {
        $orders = array();
        if (count($this->ordersId) > 0) {
            foreach ($this->ordersId as $orderId) {
                $order = $this->_orderServices->getOne($orderId);
                $orders[] = json_decode($order);
            }
        }

        return view('Orders::exports.cashflow', [
            'orders' => $orders
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'BigFast - Đơn hàng';
    }
}
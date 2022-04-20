<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CashFlowNewExport implements WithMultipleSheets
{
    use Exportable;

    private $_orderFeeInterface;
    private $_orderServices;
    private $_payload;

    public function __construct($orderFeeInterface, $orderServices, $payload)
    {
        $this->_orderFeeInterface = $orderFeeInterface;
        $this->_orderServices = $orderServices;
        $this->_payload = $payload;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [
            new CashFlowNewSheet($this->_orderFeeInterface, $this->_orderServices, $this->_payload, 'transport,cod,insurance,incurred_fee_transport,incurred_fee_cod'),
            new CashFlowNewSheet($this->_orderFeeInterface, $this->_orderServices, $this->_payload, 'total_cod,incurred_total_cod'),
            new CashFlowNewSheet($this->_orderFeeInterface, $this->_orderServices, $this->_payload, 'refund_cod,refund_transport'),
        ];

        return $sheets;
    }
}
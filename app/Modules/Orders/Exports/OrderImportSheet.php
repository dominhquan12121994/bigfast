<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class OrderImportSheet implements FromView, WithTitle
{
    private $payload;
    private $colsFail;

    public function __construct($payload, $colsFail)
    {
        $this->payload = $payload;
        $this->colsFail = $colsFail;
    }

    public function view(): View
    {
        $arrFails = array();
        if (count($this->colsFail) > 0) {
            foreach ($this->colsFail as $fail) {
                $failArr = explode('.', $fail);
                $arrFails[$failArr[0]][] = $failArr[1];
            }
        }

        $orders = array();
        if (count($this->payload) > 0) {
            foreach ($this->payload as $key => $order) {
                if (isset($arrFails[$key])) {
                    foreach ($arrFails[$key] as $colFail) {
                        $order[$colFail] = 'error-' . $order[$colFail];
                    }
                    $orders[] = $order;
                }
            }
        }

        return view('Orders::exports.orders-fails', [
            'orders' => $orders,
            'arrFails' => $arrFails,
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
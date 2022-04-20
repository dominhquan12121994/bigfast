<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OrderExport implements WithMultipleSheets
{
    use Exportable;

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [
            new OrderSheet()
        ];

        return $sheets;
    }
}
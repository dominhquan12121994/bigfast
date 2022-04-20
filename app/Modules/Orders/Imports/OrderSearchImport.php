<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Created by PhpStorm.
 * User: Electric
 * Date: 3/5/2021
 * Time: 2:32 PM
 */

namespace App\Modules\Orders\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OrderSearchImport implements WithMultipleSheets
{
    function __construct() {

    }

    public function sheets(): array
    {
        return [
            0 => new OrderSearchFirstImport()
        ];
    }
}
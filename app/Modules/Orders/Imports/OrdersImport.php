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

class OrdersImport implements WithMultipleSheets
{
    protected $shop_id;
    protected $user_id;
    protected $user_type;
    protected $file_fails;

    function __construct($shop_id, $user_type, $user_id, $fileFails = '') {
        $this->shop_id = $shop_id;
        $this->user_id = $user_id;
        $this->user_type = $user_type;
        $this->file_fails = $fileFails;
    }

    public function sheets(): array
    {
        return [
            0 => new OrdersFirstImportV2($this->shop_id, $this->user_type, $this->user_id, $this->file_fails)
        ];
    }
}
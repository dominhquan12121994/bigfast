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

use Validator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class OrderSearchFirstImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        if (count($rows) < 1000) {
            $arrCodeSearch = array();
            foreach ($rows->toArray() as $code) {
                if (strlen(trim($code[0])) === 12) {
                    $arrCodeSearch[] = trim($code[0]);
                }
            }
            request()->session()->put('order-search-excel', $arrCodeSearch);
            \Func::setToast('Thành công', 'Upload file thành công', 'notice');
        } else {
            \Func::setToast('Thất bại', 'Tối đa 1000 đơn/file', 'error');
        }
    }
}

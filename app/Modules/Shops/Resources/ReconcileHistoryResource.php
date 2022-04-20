<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Resources;

use App\Http\Resources\AbstractResource;

/**
 * Class ReconcileHistoryResource
 * package App\Modules\Shops\Resources
 * author HuyDien <huydien.itgmail.com>
 */
class ReconcileHistoryResource extends AbstractResource
{
    /**
     * @param $request
     * @return array
     * @author HuyDien <huydien.it@gmail.com>
     */
    public function toArray($request)
    {
        return $this->resource->map(function ($item) {
            return array(
                'end_date' => date('d/m/Y', strtotime($item['end_date'])),
                'total_cod' => number_format($item['total_cod']),
                'money_indemnify' => number_format($item['money_indemnify']),
                'total_fee' => number_format($item['total_fee']),
                'total_du' => number_format($item['total_du'])
            );
        });
    }
}

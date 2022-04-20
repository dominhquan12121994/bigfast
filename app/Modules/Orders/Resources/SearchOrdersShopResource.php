<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Resources;

use Illuminate\Support\Collection;
use App\Http\Resources\AbstractResource;

use App\Modules\Orders\Constants\OrderConstant;

/**
 * Class WardsResource
 * @package App\Modules\Transport\Resources
 * @author HuyDien <huydien.it@gmail.com>
 */
class SearchOrdersShopResource extends AbstractResource
{
    /**
     * @param $request
     * @return array
     * @author HuyDien <huydien.it@gmail.com>
     */
    public function toArray($request)
    {
        $incurred_fee = $this->resource['incurred_fee'];

        if (isset($this->resource['data'])) {
            $this->resource['products'] = $this->resource['data']->transform(function ($item) use ($incurred_fee) {
                return new OrdersShopDetailResource( array('data' => $item, 'incurred_fee' => $incurred_fee));
            });
        }

        unset($this->resource['data']);
        unset($this->resource['incurred_fee']);

        return $this->resource;
    }
}
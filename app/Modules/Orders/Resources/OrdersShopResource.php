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
class OrdersShopResource extends AbstractResource
{
    /**
     * @param $request
     * @return array
     * @author HuyDien <huydien.it@gmail.com>
     */
    public function toArray($request)
    {
        $incurred_fee = $this->resource['incurred_fee'];
        $this->resource['status'] = collect($this->resource['statusList'])->transform(function ($item) {
            return array (
                "status" => (int)$item,
                "name" => OrderConstant::status[$item]['name'],
                "total" => (int)$this->resource['statusActive'] === (int)$item ? $this->resource['totalOrders'] : $this->resource['countAryStatus'][$item]
            );
        })->toArray();

        $this->resource['filter'] = array(
            'date' => in_array($this->resource['statusActive'], $this->resource['status_date_allow'] ),
            'status_detail' => [
                'lists' => collect(OrderConstant::status[(int)$this->resource['statusActive']]['detail'])->transform(function ($item , $key) {
                    return array (
                        "status_detail" => $key,
                        "name" => $item['name']
                    );
                })->toArray()
            ]
        );

        if (isset($this->resource['data'])) {
            $this->resource['products'] = $this->resource['data']->transform(function ($item) use ($incurred_fee) {
                return new OrdersShopDetailResource( array('data' => $item, 'incurred_fee' => $incurred_fee));
            });
        }

        unset($this->resource['data']);
        unset($this->resource['statusActive']);
        unset($this->resource['totalOrders']);
        unset($this->resource['incurred_fee']);
        unset($this->resource['status_date_allow']);
        unset($this->resource['countAryStatus']);
        unset($this->resource['statusList']);

        return $this->resource;
    }
}
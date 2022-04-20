<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Created by PhpStorm.
 * @author Electric <huydien.it@gmail.com>
 * Date: 8/4/2020
 */

namespace App\Modules\Orders\Resources;

use App\Http\Resources\AbstractResource;
use App\Modules\Orders\Constants\OrderConstant;

class OrderShipperResource extends AbstractResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array|void
     * @author Electric <huydien.it@gmail.com>
     * @copyright (c) 8/4/2020
     */
    public function toArray($request)
    {
//        $arrStatus = array_keys(OrderConstant::statusShipper);
        if (isset($this->resource['arrStatusByType']) && isset($this->resource['status'])) {
            $arrStatus = $this->resource['arrStatusByType'];
            array_splice($arrStatus, 1, 0, array('pickup' => 22, 'shipper' => 51, 'refund' => 82)[$this->resource['userType']]);
            $this->resource['status'] = collect($arrStatus)->transform(function ($status, $key){
                return array(
                    'status' => $status,
                    'active' => ($status === (int)$this->resource['statusActive']),
                    'name' => OrderConstant::statusShipperMobile[$status]['name'],
                    'total' => ($key === 1) ? $this->resource['totalDone'] : ($this->resource['status'][$status] ?? 0)
                );
            });
        }

        if (isset($this->resource['data'])) {
            $this->resource['data']->transform(function ($item){
                return new OrderShipperDetailResource($item);
            });
        }

        if (isset($this->resource['shops'])) {
            collect($this->resource['shops'])->transform(function ($item){
                $item->orders->transform(function ($item){
                    return new OrderPickupDetailResource($item);
                });
            });
        }

        unset($this->resource['userType']);
        unset($this->resource['totalDone']);
        unset($this->resource['statusActive']);
        unset($this->resource['arrStatusByType']);

        return $this->resource;

    }
}

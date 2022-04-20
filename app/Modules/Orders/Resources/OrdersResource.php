<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Resources;

use Illuminate\Support\Collection;
use App\Http\Resources\AbstractResource;
use App\Modules\Orders\Constants\OrderConstant;

/**
 * Class OrdersResource
 * @package App\Modules\Transport\Resources
 * @author HuyDien <huydien.it@gmail.com>
 */
class OrdersResource extends AbstractResource
{
    /**
     * @param $request
     * @return array
     * @author HuyDien <huydien.it@gmail.com>
     */
    public function toArray($request)
    {
        if ($this->resource instanceof Collection) {
            return $this->resource->map(function($item){

                $status = $item->status;
                $collect = $item->cod;
                if ($item->payfee === 'payfee_receiver') {
                    $collect += $item->transport_fee;
                }

                return array(
                    'order' => array(
                        'lading_code' => $item->lading_code,
                        'status' => $status,
                        'statusText' => OrderConstant::status[$status]['name'],
                        'collect' => number_format($collect) . ' vnđ',
                        'weight' => round($item->weight / 1000, 2) . ' kg',
                        'note1' => $item->extra->note1,
                        'note2' => $item->extra->note2,
                    ),
                    'sender' => array(
                        'name' => $item->sender->name,
                        'phone' => $item->sender->phone,
                    ),
                    'receiver' => array(
                        'name' => $item->receiver->name,
                        'phone' => $item->receiver->phone,
                        'address' => $item->receiver->address . ', ' . $item->receiver->wards->name . ', ' . $item->receiver->districts->name . ', ' . $item->receiver->provinces->name,
                    ),
                    'logs' => $item->logs->transform(function ($item){ return $item->note1; })
                );
            });
        }
    }
}

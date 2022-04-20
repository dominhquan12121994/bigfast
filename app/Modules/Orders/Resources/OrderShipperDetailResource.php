<?php
/**
 * Created by PhpStorm.
 * @author Electric <huydien.it@gmail.com>
 * Date: 8/4/2020
 */

namespace App\Modules\Orders\Resources;

use App\Http\Resources\AbstractResource;
use App\Modules\Orders\Constants\OrderConstant;

class OrderShipperDetailResource extends AbstractResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @author Electric <huydien.it@gmail.com>
     * @copyright (c) 8/4/2020
     */
    public function toArray($request)
    {
        $message = $this->resource;

        $status = $message->status;
        $status_detail = $message->status_detail;
        $collect = $message->cod;
        if ($message->payfee === 'payfee_receiver') {
            $collect += $message->transport_fee;
        }

        return array(
            'shop' => array(
                'name' => $message->shop->name,
                'phone' => $message->shop->phone,
                'address' => $message->shop->address
            ),
            'order' => array(
                'id' => $message->id,
                'lading_code' => $message->lading_code,
                'status' => $status_detail,
                'statusText' => OrderConstant::status[$status]['detail'][$status_detail]['name'],
                'collect' => number_format($collect) . ' vnđ',
                'product' => implode(', ', $message->products->transform(function ($product){
                    return $product->quantity . ' x ' . $product->name;
                })->toArray()),
                'weight' => round($message->weight / 1000, 2) . ' kg',
                'note1' => $message->extra->note1 ? OrderConstant::notes[$message->extra->note1] : 'N/A',
                'note2' => $message->extra->note2 ?? 'N/A',
            ),
            'receiver' => array(
                'name' => $message->receiver->name,
                'phone' => $message->receiver->phone,
                'address' => $message->receiver->address . ', ' . $message->receiver->wards->name . ', ' . $message->receiver->districts->name . ', ' . $message->receiver->provinces->name,
            ),
            'logs' => array_unique($message->logs->filter(function ($item){ return $item->log_type === 'call_history'; })
                ->transform(function ($item){ return $item->note1; })->toArray())
        );
    }
}

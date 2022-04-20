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

class OrderPickupDetailResource extends AbstractResource
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
        $products = $message->products;
        $productStr = array();
        if (count($products) > 0) {
            foreach ($products as $product) {
                $productStr[] = $product->quantity . ' x ' . $product->name;
            }
        }

        return array(
            'id' => $message->id,
            'lading_code' => $message->lading_code,
            'status' => $status_detail,
            'statusText' => OrderConstant::status[$status]['detail'][$status_detail]['name'],
            'product' => implode(', ', $productStr),
            'weight' => round($message->weight / 1000, 2) . ' kg',
            'note2' => $message->extra->note2 ?? 'N/A',
            'client_code' => $message->extra->client_code ?? 'N/A',
            'receiver_name' => $message->receiver->name,
            'receiver_address' => $message->receiver->address,
            'receiver_wards' => $message->receiver->wards->name,
            'receiver_districts' => $message->receiver->districts->name,
            'receiver_provinces' => $message->receiver->provinces->name,
        );
    }
}

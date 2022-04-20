<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Resources;

use Illuminate\Support\Collection;
use App\Http\Resources\AbstractResource;
use App\Modules\Orders\Constants\OrderConstant;

/**
 * Class OrdersShopDetailResource
 * @package App\Modules\Transport\Resources
 * @author HuyDien <huydien.it@gmail.com>
 */
class OrdersShopDetailResource extends AbstractResource
{
    /**
     * @param $request
     * @return array
     * @author HuyDien <huydien.it@gmail.com>
     */
    public function toArray($request)
    {
        $message = $this->resource['data'];

        $status = $message->status;
        if ($status === 9) {
            $status = 5;
        }
        $status_detail = $message->status_detail;
        if ($status_detail === 91) {
            $status_detail = 51;
        }
        $cod = $message->cod;
        $collect = $message->cod;
        $incurred_fee = $this->resource['incurred_fee'];
        $pay_text = 'Người gửi trả';
        if ($message->payfee === 'payfee_receiver') {
            $collect += $message->transport_fee;
            $pay_text = 'Người nhận trả';
        }

        $total_fee = $message->total_fee
            + ( $incurred_fee[$message->id]['incurred_fee_transport'] ?? 0 )
            + ( $incurred_fee[$message->id]['insurance_value'] ?? 0 )
            + ( $incurred_fee[$message->id]['incurred_total_cod'] ?? 0 );
    
        $aryLogs = array(
            [
                "type" => "Tạo đơn hàng",
                "lists" => [],
            ],
            [
                "type" => "Lấy hàng",
                "lists" => [],
            ],
            [
                "type" => "Giao hàng",
                "lists" => [],
            ],
            [
                "type" => "Hoàn hàng",
                "lists" => [],
            ],
            [
                "type" => "Đối soát",
                "lists" => [],
            ]
        );

        $warehouse_times = 0;
        foreach ( $message->logs as $log ) {
            $text = $log->note1;
            $key = 5;
            if ( in_array($log->log_type, array('create_order') ) ) {
                $text = 'Đã tạo đơn hàng';
                $key = 0;
            }
            if ( in_array($log->log_type, array('assign_shipper_pick', 'warehouse', 'pick_fail') ) ) {
                $key = 1;
                //Check lưu kho
                if ($log->log_type == 'warehouse') {
                    if ($warehouse_times > 0 ) {
                        $key = 2;
                    } else {
                        $text = 'Lấy hàng thành công';
                    }
                    $warehouse_times++;
                }
            }
            if ( in_array($log->log_type, array('assign_shipper_send', 'confirm_resend', 'send_success') ) ) {
                $key = 2;
            }
            if ( in_array($log->log_type, array('assign_refund', 'warehouse_refund') ) ) {
                $key = 3;
            }
            if ( in_array($log->log_type, array('missing_confirm', 'damaged_confirm', 'reconcile_send', 'reconcile_refund', 'reconcile_missing', 'reconcile_damaged') ) ) {
                $key = 4;
            }
            if ( $key != 5) {
                $aryLogs[$key]['lists'][] = array( 
                    'note1' => $text,
                    'timer' => date('H:i d-m', strtotime($log->timer)),
                );
            }
        };

        return array(
            'shop' => array(
                'name' => $message->shop->name,
                'phone' => $message->shop->phone,
                'address' => $message->shop->address
            ),
            'order' => array(
                'id' => $message->id,
                'lading_code' => $message->lading_code,
                'client_code' => $message->extra->client_code,
                'status' => $message->status,
                'status_detail' => $message->status_detail,
                "payfee" => $pay_text,
                'statusText' => OrderConstant::status[$status]['detail'][$status_detail]['name'],
                'cod' => number_format($cod) . ' vnđ',
                'collect' => number_format($collect) . ' vnđ',
                'product' => implode(', ', $message->products->transform(function ($product){
                    return $product->quantity . ' x ' . $product->name;
                })->toArray()),
                'weight' => round($message->weight / 1000, 2) . ' kg',
                'insurance' => number_format($incurred_fee[$message->id]['insurance'] ?? 0) . 'vnđ',
                'total_fee' => number_format($total_fee) . ' vnđ',
                'expect_pick' => OrderConstant::weekday[strtolower(date('l', strtotime($message->extra->expect_pick)))] . ' ' . date('d/m/Y H:i', strtotime($message->extra->expect_pick)),
                'expect_receiver' => OrderConstant::weekday[strtolower(date('l', strtotime($message->extra->expect_receiver)))] . ' ' . date('d/m/Y H:i', strtotime($message->extra->expect_receiver)),
                'note1' => $message->extra->note1 ? OrderConstant::notes[$message->extra->note1] : 'N/A',
                'note2' => $message->extra->note2 ?? 'N/A',
                'created_at' => date('H:i d/m/Y', strtotime($message->created_at)),
            ),
            'receiver' => array(
                'name' => $message->receiver->name,
                'phone' => $message->receiver->phone,
                'address' => $message->receiver->address . ', ' . $message->receiver->wards->name . ', ' . $message->receiver->districts->name . ', ' . $message->receiver->provinces->name,
            ),
            'logs' => $aryLogs,
            'contacts' => $message->contacts->transform(function ($item, $key) {
                return array(
                    "username" => $item->assign->name ?? 'N/A',
                    "detail" => $item->detail,
                    "created_at" => date('d/m/Y H:i', strtotime($item->created_at))
                );
            })
        );
    }
}
<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Created by PhpStorm.
 * User: Electric
 * Date: 3/5/2021
 * Time: 4:54 PM
 */

namespace App\Modules\Orders\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Modules\Orders\Models\Entities\OrderQueue;
use App\Modules\Orders\Models\Services\OrderServices;
use App\Modules\Orders\Models\Entities\OrderShopAddress;

use App\Helpers\StringHelper;

class CreateOrdersApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(OrderServices $orderServices)
    {
        try {
            DB::beginTransaction();

            // check sender address
            $sender = OrderShopAddress::where('shop_id', $this->payload['shop_id'])
                ->where('type', 'send')
                ->where('p_id', $this->payload['senderProvinces'])
                ->where('d_id', $this->payload['senderDistricts'])
                ->where('w_id', $this->payload['senderWards'])
                ->where('phone', $this->payload['senderPhone'])
                ->first();

            if (!$sender) {
                $sender = OrderShopAddress::create([
                    'shop_id' => $this->payload['shop_id'],
                    'p_id' => $this->payload['senderProvinces'],
                    'd_id' => $this->payload['senderDistricts'],
                    'w_id' => $this->payload['senderWards'],
                    'type' => 'send',
                    'name' => $this->payload['senderName'],
                    'phone' => $this->payload['senderPhone'],
                    'address' => $this->payload['senderAddress']
                ]);
            }

//            if (!$sender) {
//                throw new \Exception('Chưa có nơi gửi hàng');
//            }
//
//            if (empty($sender->name)) {
//                throw new \Exception('Chưa có nơi gửi hàng');
//            }

            $payload = array(
                'lading_code' => $this->payload['lading_code'],
                'user_type' => $this->payload['user_type'],
                'user_id' => $this->payload['user_id'],
                'shopId' => $this->payload['shop_id'],
                'senderId' => $sender->id,
                'receiverName' => $this->payload['receiverName'],
                'receiverPhone' => $this->payload['receiverPhone'],
                'receiverAddress' => $this->payload['receiverAddress'],
                'receiverProvinces' => $this->payload['receiverProvinces'], // id
                'receiverDistricts' => $this->payload['receiverDistricts'], // id
                'receiverWards' => $this->payload['receiverWards'], // id
                'address_refund' => $this->payload['address_refund'],
                'quantity_products' => $this->payload['quantity_products'],
                'addProductName' => $this->payload['addProductName'],
                'addProductPrice' => $this->payload['addProductPrice'],
                'addProductSlg' => $this->payload['addProductSlg'],
                'addProductCode' => $this->payload['addProductCode'],
                'weight' => $this->payload['weight'],
                'length' => $this->payload['length'],
                'width' => $this->payload['width'],
                'height' => $this->payload['height'],
                'cod' => $this->payload['cod'],
                'insurance_value' => $this->payload['insurance_value'] ?: 0,
                'service_type' => $this->payload['service_type'],
                'expect_pick' => now(),
                'payfee' => array('sender' => 'payfee_sender', 'receiver' => 'payfee_receiver')[$this->payload['payment_type']],
                'client_code' => $this->payload['client_order_code'],
                'note1' => $this->payload['required_note'],
                'note2' => $this->payload['note']
            );

            if (isset($this->payload['refundName'])) {
                $refund = OrderShopAddress::where('shop_id', $this->payload['shop_id'])
                    ->where('type', 'refund')
                    ->where('p_id', $this->payload['refundProvinces'])
                    ->where('d_id', $this->payload['refundDistricts'])
                    ->where('w_id', $this->payload['refundWards'])
                    ->where('phone', $this->payload['refundPhone'])
                    ->first();

                if (!$refund) {
                    $refund = OrderShopAddress::create([
                        'shop_id' => $this->payload['shop_id'],
                        'p_id' => $this->payload['refundProvinces'],
                        'd_id' => $this->payload['refundDistricts'],
                        'w_id' => $this->payload['refundWards'],
                        'type' => 'refund',
                        'name' => $this->payload['refundName'],
                        'phone' => $this->payload['refundPhone'],
                        'address' => $this->payload['refundAddress']
                    ]);
                }
                $payload['address_refund'] = $refund->id;
            }

            $dataRes = $orderServices->crudStore($payload);
            if (!$dataRes->result) {
                throw new \Exception($dataRes->message);
            } else {
                $queue = OrderQueue::find($this->payload['queue_id']);
                $queue->status = 1;
                $queue->reason = "";
                $queue->save();
                print_r('=====================Thanh cong===================');
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $queue = OrderQueue::where('id', $this->payload['queue_id'])->update([
                'status' => 2,
                'reason' => $e->getMessage()
            ]);
            print_r('=====================That Bai===================');

        }
    }

    public function failed($exception)
    {
        $exception->getMessage();
        // etc...
    }
}

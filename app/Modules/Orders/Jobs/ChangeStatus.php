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
use App\Modules\Orders\Models\Entities\Orders;
use App\Modules\Orders\Models\Services\OrderServices;

class ChangeStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    protected $payload;

    protected $_ordersInterface;

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
            $orders = Orders::where('shop_id', $this->payload['shop_id'])
                ->where('status', $this->payload['from_status'])
                ->where('status_detail', $this->payload['from_status_detail'])
                ->whereBetween('collect_money_date', $this->payload['collect_money_range'])
                ->get();
            if (count($orders) > 0) {
                DB::beginTransaction();
                foreach ($orders as $order) {
                    $order->status = $this->payload['to_status'];
                    $order->status_detail = $this->payload['to_status_detail'];
                    $order->last_change_date = (int)date('Ymd');

                    if ($this->payload['log_type'] === 'reconcile_send') {
                        $order->reconcile_send_date = (int)date('Ymd');
                    }
                    $order->save();

                    $dataLog = array(
                        'order_id'  => $order->id,
                        'user_type' => $this->payload['user_type'],
                        'user_id'   => $this->payload['user_id'],
                        'log_type'  => $this->payload['log_type'],
                        'status'    => $this->payload['to_status'],
                        'status_detail' => $this->payload['to_status_detail'],
                        'note1'     => $this->payload['log_note'],
                        'note2'     => '',
                        'logs'      => json_encode($this->payload),
                        'timer'     => now()
                    );
                    $orderServices->createOrderLog($dataLog);
                }
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            print_r($e->getMessage());
            print_r('=====================That Bai===================');
        }
    }

    public function failed($exception)
    {
        $exception->getMessage();
        // etc...
    }
}

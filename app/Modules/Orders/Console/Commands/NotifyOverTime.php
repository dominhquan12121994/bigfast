<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Modules\Systems\Models\Entities\User;

use App\Modules\Systems\Services\NotificationServices;

class NotifyOverTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:overtime-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Orders Over Time Status';

    protected $_notificationServices;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NotificationServices $notificationServices)
    {
        parent::__construct();

        $this->_notificationServices = $notificationServices;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('=============BEGIN============');
        $this->info('Bắt đầu :' . date('H:i:s'));
        try {
            $listStatus = config('order.status_over_time');
            $admins = User::role('admin')->get();
            $admin_sender = $admins->first();
            if ($admin_sender) {
                foreach ( $listStatus as $status => $item ) {
                    $total = 0;
                    $orderOver = DB::table('order_status_overtime')
                        ->select('shop_id', DB::raw('count(*) as total'))
                        ->where('status_detail', $item['status_detail'])
                        ->whereNull('end_date')
                        ->where('start_date', '<=', date('Ymd', strtotime('-'.$item['over_day'].' day')))
                        ->groupBy('shop_id')
                        ->get();
                    
                    if ( count($orderOver) > 0 ) {
                        foreach ( $orderOver as $id => $order) {
                            //Gửi thông báo đến shop
                            $notificationPayload = array(
                                'link' => route('shop.orders.index', ['status' => $status, 'status_detail' => $item['status_detail']]),
                                'sender_id' => $admin_sender->id,
                                'shop_id' => $order->shop_id,
                                'type' => 0,
                                'content_data' => array(
                                    5, $order->total, $item['text']
                                )
                            );
                            $this->_notificationServices->sendToShop($notificationPayload);
                            $total += $order->total;
                        }
    
                        //Gửi thông báo đến admin
                        foreach ($admins as $admin ) {
                            $notificationPayload = array(
                                'link' => route('admin.orders.index', ['status' => $status, 'status_detail' => $item['status_detail']]),
                                'sender_id' => $admin_sender->id,
                                'user_id' => $admin->id,
                                'type' => 0,
                                'content_data' => array(
                                    5, $total, $item['text']
                                )
                            );
                            $this->_notificationServices->sendToUser($notificationPayload);
                        }
                    }
                }
            }
            
            $this->info('Kết thúc: ' . date('H:i:s'));
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $this->info($message);
        }

        $this->info('==============END=============');
        return;
    }
}
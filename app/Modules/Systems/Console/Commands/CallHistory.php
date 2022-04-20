<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Console\Commands;

use Illuminate\Console\Command;

use App\Modules\Systems\Models\Entities\User;
use App\Modules\Systems\Models\Entities\CallHistory as CallHistoryModel;
use App\Modules\Orders\Models\Repositories\Contracts\OrderLogInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;

class CallHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'systems:call-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lịch sử cuộc gọi';

    protected $_orderLogInterface;
    protected $_ordersInterface;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OrderLogInterface $orderLogInterface,
                                OrdersInterface $ordersInterface)
    {
        parent::__construct();

        $this->_orderLogInterface = $orderLogInterface;
        $this->_ordersInterface = $ordersInterface;
    }

    public function handle()
    {
        $arrPhoneSender = array();
        $arrPhoneRefund = array();
        $arrPhoneReceiver = array();
        $typeMess = array(
            'INCOMING' => ' từ ',
            'OUTGOING' => ' tới ',
            'MISSED' => ' từ ',
        );

        $data =  CallHistoryModel::offset(0)->limit(20)->get();
        if ($data) {
            if (count($data) > 0) {
                foreach ($data as $history) {
                    $user = User::find($history->user_id);
                    $history->delete();
                    if ($user) {

                        if (!isset($arrPhoneSender[$user->id])) {
                            $contidion = array(
                                'assignUserCallHistory' => $history->user_id,
                                'timeAssignRange' => array((int)date('Ymd', strtotime('-14 day')), (int)date('Ymd'))
                            );
                            $fetchOptions = array('with' => array('receiver', 'sender', 'refund'));
                            $orders = $this->_ordersInterface->getMore($contidion, $fetchOptions);
                            if (count($orders) > 0) {
                                foreach ($orders as $order) {
                                    $arrPhoneSender[$user->id][] = $order->sender->phone;
                                    $arrPhoneRefund[$user->id][] = $order->refund->phone;
                                    $arrPhoneReceiver[$user->id][] = $order->receiver->phone;
                                }
                            }
                        }

                        if (isset($arrPhoneSender[$user->id]) && $arrPhoneRefund[$user->id] && $arrPhoneReceiver[$user->id]) {
                            $logs = json_decode($history->logs);
                            if (count($logs) > 0) {
                                foreach ($logs as $log) {
                                    $phone = $log->phoneNumber;
                                    $type = $log->type;
                                    $duration = $log->duration;
                                    $timestamp = $log->timestamp / 1000;

                                    $txtDuration = '';
                                    if ($duration < 5 || $type === 'MISSED') {
                                        $txtDuration = ' thất bại ';
                                    }

                                    if (!isset($typeMess[$type])) {
                                        continue;
                                    }

                                    $typeTxt = $typeMess[$type];
                                    $note1 = '';
                                    if (in_array($phone, $arrPhoneSender[$user->id])) {
                                        $note1 = date('H:i d/m', $timestamp) . ' cuộc gọi ' . $txtDuration . $typeTxt . 'người gửi hàng';
                                    }
                                    if (in_array($phone, $arrPhoneRefund[$user->id])) {
                                        $note1 = date('H:i d/m', $timestamp) . ' cuộc gọi ' . $txtDuration . $typeTxt . 'người nhận hàng hoàn';
                                    }
                                    if (in_array($phone, $arrPhoneReceiver[$user->id])) {
                                        $note1 = date('H:i d/m', $timestamp) . ' cuộc gọi ' . $txtDuration . $typeTxt . 'người nhận hàng';
                                    }

                                    if ($note1) {
                                        $this->_orderLogInterface->create(array(
                                            'order_id'          => $order->id,
                                            'user_type'         => 'user',
                                            'user_id'           => $user->id,
                                            'log_type'          => 'call_history',
                                            'status'            => 0,
                                            'status_detail'     => 0,
                                            'note1'             => $note1,
                                            'note2'             => "",
                                            'logs'              => "",
                                            'timer'             => date('Y-m-d H:i:s', $timestamp)
                                        ));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return;
    }
}

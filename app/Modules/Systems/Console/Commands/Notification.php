<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Systems\Jobs\SendNotification;
use App\Modules\Systems\Models\Repositories\Contracts\DeviceTokenInterface;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;
use App\Modules\Systems\Constants\NotificationConstant;
use Illuminate\Support\Str;
use App\Modules\Systems\Services\NotificationServices;

class Notification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'systems:send-notification';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notification';
    protected $_notificationSendInterface;
    protected $_notificationServices;
    protected $_deviceTokenInterface;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NotificationSendInterface $notificationSendInterface,
                                DeviceTokenInterface $deviceTokenInterface,
                                NotificationServices $notificationServices)
    {
        parent::__construct();
        $this->_notificationSendInterface = $notificationSendInterface;
        $this->_deviceTokenInterface = $deviceTokenInterface;
        $this->_notificationServices = $notificationServices;

    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // danh sách các thông báo chưa gửi, hạn mức 100 thông báo
        $notificationsSend = $this->_notificationSendInterface->getMore(
            array('is_send' => 0),
            array('with' => array('notification')),
            100
        );

        if (!empty($notificationsSend)) {
            $notificationsSend->each(function ($item) {
                return $item->pattern = json_decode($item->notification->content_data)[0];
            });

            $userNotification = $notificationsSend->groupBy('user_id');
            if (!empty($userNotification)) {
                $this->_send('admin', $userNotification);
            }

            $shopNotification = $notificationsSend->groupBy('shop_id');
            if (!empty($shopNotification)) {
                $this->_send('shop', $shopNotification);
            }
        }
    }

    public function _send($type, $notifications) {
        if (!empty($notifications)) {
            foreach ($notifications as $id => $notificationBy) {
                if ($id) {
                    $tokens = $this->getToken($type, $id);
                    $result = $notificationBy->groupBy('pattern');
                    foreach ($result as $item) {
                        $mergedNotification = $this->_notificationServices->mergeContent(array(
                            'notificationsSend' => $item
                        ));

                        foreach ($mergedNotification as $notification) {
                            $notificationPayload = array(
                                'tokens' => $tokens,
                                'notification_content' => $notification['content'],
                                'arr_notification_send' => $notification['id']
                            );
                            SendNotification::dispatch($notificationPayload)->onQueue('sendNotification');
                        }
                    }
                }
            }
        }
    }

    public function getToken($type, $id) {
        $tokens = array(
            'registration_ids' => array(),
            'arrExpoToken' => array(),
            'arrFirebaseToken' => array()
        );

        $conditions = array(
            'user_type' => $type,
            'device_type' => 'web',
            'user_id' => $id
        );
        $deviceToken = $this->_deviceTokenInterface->getMore($conditions);
        if (count($deviceToken) > 0) {
            foreach ($deviceToken as $token) {
                if (!in_array($token->device_token, $tokens['registration_ids']))
                    $tokens['registration_ids'][] = $token->device_token;
            }
        }
        //
        $conditions = array(
            'user_type' => $type,
            'device_type' => 'app',
            'user_id' => $id
        );
        $deviceToken = $this->_deviceTokenInterface->getMore($conditions);
        if (count($deviceToken) > 0) {
            foreach ($deviceToken as $token) {
                if (strlen($token->device_token) === 41) {
                    if (!in_array($token->device_token, $tokens['arrExpoToken']))
                        $tokens['arrExpoToken'][] = $token->device_token;
                }
                if (strlen($token->device_token) === 163) {
                    if (!in_array($token->device_token, $tokens['arrFirebaseToken']))
                        $tokens['arrFirebaseToken'][] = $token->device_token;
                }
            }
        }
        return $tokens;
    }
}

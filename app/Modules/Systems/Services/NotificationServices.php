<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Services;

use App\Modules\Systems\Models\Repositories\Contracts\NotificationInterface;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use Illuminate\Support\Str;
use App\Modules\Systems\Constants\NotificationConstant;
use App\Modules\Systems\Events\ShopNotificationEvent;

class NotificationServices
{
    protected $_notificationInterface;
    protected $_notificationSendInterface;
    protected $_shopsInterface;

    public function __construct(NotificationInterface $notificationInterface,
                                ShopsInterface $shopsInterface,
                                NotificationSendInterface $notificationSendInterface)
    {
        $this->_notificationInterface = $notificationInterface;
        $this->_notificationSendInterface = $notificationSendInterface;
        $this->_shopsInterface = $shopsInterface;
    }

    public function sendToUser($payload) {
        $notification = $this->_notificationInterface->create(array(
            'sender_id' => $payload['sender_id'],
            'link' => $payload['link'] ?? null,
            'created_date' => date('Ymd'),
            'receiver_quantity' => 1,
            'content_data' => json_encode($payload['content_data']),
        ));

        $this->_notificationSendInterface->create(array(
            'user_id' => $payload['user_id'],
            'notification_id' => $notification->id,
            'created_date' => date('Ymd'),
            'created_time' => time(),
        ));
    }

    public function sendToShop($payload) {
        $shop_id = $payload['shop_id'];
        $shop = $this->_shopsInterface->getById($shop_id);
        $dateCreate = (int)date('Ymd');

        if ($shop) {
            $payload = array(
                'link' => $payload['link'] ?? null,
                'created_date' => $dateCreate,
                'receiver_quantity' => 1,
                'sender_id' => $payload['sender_id'],
                'type' => $payload['type'] ?? 'info',
                'content_data' => json_encode($payload['content_data']),
            );

            $success = $this->_notificationInterface->create($payload);

            $arrShopNotification = array();
            $arrDeviceToken = array();
            array_push($arrShopNotification, array(
                'notification_id' => $success->id,
                'shop_id' => $shop_id,
                'is_read' => false,
                'created_date' => $dateCreate,
                'created_time' => time(),
            ));
            array_push($arrDeviceToken, $shop->device_token);

            $this->_notificationSendInterface->insert($arrShopNotification);
        }
    }

    public function mergeContent($payload) {
        $notificationsSend = $payload['notificationsSend'];
        if (!empty($notificationsSend)) {
            $notificationsSend->each(function ($item) {
                return $item->pattern = json_decode($item->notification->content_data)[0];
            });
        }

        $result = $notificationsSend->groupBy('pattern');

        $mergedNotification = array();

        foreach ($result as $pattern => $notificationSend) {
            if ($pattern === 0) {
                // không cộng chỉ thêm vào
                foreach ($notificationSend as $key => $item) {
                    $notification = array();
                    $notification['id'] = array($item->notification_id);
                    $notification['content'] = json_decode($item->notification->content_data)[1];
                    $notification['link'] = $item->notification->link;
                    $notification['type'] = $item->notification->type;
                    $notification['sender_name'] = $item->notification->user->name;
                    $notification['is_read'] = $item->is_read;
                    $notification['created_at'] = date('d/m/Y H:i', $item->created_time);
                    array_push($mergedNotification, $notification);
                }
            } else {
                // cộng và thêm
                $notification = array();
                $notification['id'] = array();
                $notification['content'] = array();
                foreach ($notificationSend as $key => $item) {
                    array_push($notification['id'], $item->notification_id);
                    $contentData = json_decode($item->notification->content_data);
                    if (empty($notification['content'])) {
                        $notification['content'] = $contentData;
                    } else {
                        foreach ($contentData as $index => $data) {
                            if ($index > 0) {
                                if (is_numeric($data)) {
                                    $notification['content'][$index] += $data;
                                } else {
                                    if (!strpos($notification['content'][$index], $data)) {
                                        $notification['content'][$index] .= ', ' . $data;
                                    }
                                }
                            }
                        }
                    }
                    $notification['link'] = $item->notification->link;
                    $notification['type'] = $item->notification->type;
                    $notification['sender_name'] = $item->notification->user->name;
                    $notification['is_read'] = $item->is_read;
                    $notification['created_at'] = date('d/m/Y H:i', $item->created_time);
                }

                $notification['content'] = $this->generateContent(array(
                    'content_data' => $notification['content']
                ));

                array_push($mergedNotification, $notification);
            }
        }
        return $mergedNotification;
    }

    public function generateContent($payload) {
        $contentData = $payload['content_data'];
        $contentPatterns = NotificationConstant::content_pattern;
        $pattern = $contentData[0];
        if ($pattern === 0) {
            return $contentData[1];
        } else {
            $notificationContent = $contentPatterns[$pattern];
            return Str::replaceArray('?', array_splice($contentData, 1), $notificationContent);
        }
    }
}

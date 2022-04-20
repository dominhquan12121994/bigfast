<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Listeners;

use App\Modules\Systems\Events\UserNotificationEvent;
use App\Modules\Systems\Models\Repositories\Contracts\UsersInterface;
use App\Modules\Systems\Models\Repositories\Contracts\DeviceTokenInterface;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationInterface;

class UserNotificationListener
{
    protected $_notificationInterface;
    protected $_deviceTokenInterface;
    protected $_usersInterface;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UsersInterface $usersInterface,
                                DeviceTokenInterface $deviceTokenInterface,
                                NotificationInterface $notificationInterface)
    {
        //
        $this->_notificationInterface = $notificationInterface;
        $this->_deviceTokenInterface = $deviceTokenInterface;
        $this->_usersInterface = $usersInterface;
    }

    /**
     * Handle the event.
     *
     * @param  UserNotificationEvent  $event
     * @return void
     */
    public function handle(UserNotificationEvent $event)
    {
        $conditions = array(
            'user_type' => 'admin',
            'device_type' => 'web',
            'user_id' => $event->_payload["user_id"]
        );
        $deviceToken = $this->_deviceTokenInterface->getMore($conditions);
        $registration_ids = array();
        if (count($deviceToken) > 0) {
            foreach ($deviceToken as $token) {
                if (!in_array($token->device_token, $registration_ids))
                    $registration_ids[] = $token->device_token;
            }

            // Gửi thông báo trên pc
            $SERVER_API_KEY = config('firebase.init.SERVER_API_KEY');
            $data = [
                "registration_ids" => $registration_ids,
                "data" => array(
                    'id' => $event->_payload["id"],
                    'content' => $event->_payload["notification_content"],
                    'date' => date('d/m/Y H:i'),
                ),
                "notification" => array(
                    'title' => 'Thông báo từ BigFast',
                    'body' => $event->_payload["notification_content"],
                )
            ];

            $dataString = json_encode($data);
            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, config('firebase.init.CURLOPT_URL'));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            $response = curl_exec($ch);
        }

        //
        $conditions = array(
            'user_type' => 'admin',
            'device_type' => 'app',
            'user_id' => $event->_payload["user_id"]
        );
        $deviceToken = $this->_deviceTokenInterface->getMore($conditions);
        $registration_ids = array();
        if (count($deviceToken) > 0) {

            $arrExpoToken = array();
            $arrFirebaseToken = array();
            foreach ($deviceToken as $token) {
                if (strlen($token->device_token) === 41) {
                    if (!in_array($token->device_token, $arrExpoToken))
                        $arrExpoToken[] = $token->device_token;
                }
                if (strlen($token->device_token) === 163) {
                    if (!in_array($token->device_token, $arrFirebaseToken))
                        $arrFirebaseToken[] = $token->device_token;
                }
            }

            if (count($arrExpoToken) > 0) {
                $headers = [
                    'Content-Type: application/json',
                ];
                $data = [
                    'to' => $arrExpoToken,
                    'title' => 'Thông báo từ BigFast',
                    'body' => $event->_payload["notification_content"],
                ];
                $dataString = json_encode($data);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://exp.host/--/api/v2/push/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                $response = curl_exec($ch);
            }

            if (count($arrFirebaseToken) > 0) {
                $SERVER_API_KEY = config('firebase.init.SERVER_API_KEY');
                $data = [
                    "registration_ids" => $arrFirebaseToken,
                    "data" => array(
                        'id' => $event->_payload["id"],
                        'content' => $event->_payload["notification_content"],
                        'date' => date('d/m/Y H:i'),
                    ),
                    "notification" => array(
                        'title' => 'Thông báo từ BigFast',
                        'body' => $event->_payload["notification_content"],
                    )
                ];

                $dataString = json_encode($data);
                $headers = [
                    'Authorization: key=' . $SERVER_API_KEY,
                    'Content-Type: application/json',
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, config('firebase.init.CURLOPT_URL'));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                $response = curl_exec($ch);
            }

        }
    }
}

<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Created by PhpStorm.
 * User: Electric
 * Date: 3/31/2021
 * Time: 11:51 AM
 */

namespace App\Modules\Shops\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Modules\Shops\Events\LoginEvent;
use App\Modules\Systems\Models\Entities\DeviceToken;

class ListenerLoginEvent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LoginEvent $event
     * @return void
     */
    public function handle(LoginEvent $event)
    {
        //
        $oldToken = DeviceToken::distinct()
            ->where('user_id', $event->userID)
            ->where('user_type', $event->userType)
            ->where('device_type', $event->deviceType)
            ->where('device_token', '!=', $event->deviceToken)
            ->get(['device_token']);
        $tokens = $oldToken->map(function ($item) {
            return $item->device_token;
        })->toArray();

        $arrExpoToken = array();
        $arrFirebaseToken = array();
        if (count($tokens) > 0) {
            foreach ($tokens as $token) {
                if (strlen($token) === 41) {
                    $arrExpoToken[] = $token;
                }
                if (strlen($token) === 163) {
                    $arrFirebaseToken[] = $token;
                }
            }
        }

        if (count($arrExpoToken) > 0) {
            $data = [
                "to" => $arrFirebaseToken,
                "title" => "Thông báo truy cập",
                "body" => "Tài khoản của bạn đăng nhập trên thiết bị khác"
            ];

            $dataString = json_encode($data);
            $headers = [
                'Content-Type: application/json',
            ];

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
                    "id" => -1,
                    "title" => "Tài khoản của bạn đăng nhập trên thiết bị khác",
                    "date" => now(),
                ),
                "notification" => array(
                    "title" => "Thông báo truy cập",
                    "body" => "Tài khoản của bạn đăng nhập trên thiết bị khác"
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
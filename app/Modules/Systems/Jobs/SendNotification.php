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

namespace App\Modules\Systems\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Modules\Systems\Models\Entities\NotificationSend;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $tokens = $this->payload['tokens'];
            if (!empty($tokens['registration_ids'])) {
                // Gửi thông báo trên pc
                $SERVER_API_KEY = config('firebase.init.SERVER_API_KEY');
                $data = [
                    "registration_ids" => $tokens['registration_ids'],
                    "data" => array(
                        "content" => $this->payload['notification_content'],
                    ),
                    "notification" => array(
                        'title' => 'Thông báo từ BigFast',
                        'body' => $this->payload['notification_content'],
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
                print_r($response);
                $response = json_decode($response);
                if ($response->success) {
                    NotificationSend::whereIn('notification_id', $this->payload['arr_notification_send'])->update(array('is_send' => 1));
                }
            }
            if (!empty($tokens['arrExpoToken'])) {
                $headers = [
                    'Content-Type: application/json',
                ];
                $data = [
                    'to' => $tokens['arrExpoToken'],
                    'title' => 'Thông báo từ BigFast',
                    'body' => $this->payload['notification_content'],
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
                print_r($response);
                $response = json_decode($response);
                if ($response->data[0]->status === 'ok') {
                    NotificationSend::whereIn('notification_id', $this->payload['arr_notification_send'])->update(array('is_send' => 1));
                }
            }
            if (!empty($tokens['arrFirebaseToken'])) {
                $SERVER_API_KEY = config('firebase.init.SERVER_API_KEY');
                $data = [
                    "registration_ids" => $tokens['arrFirebaseToken'],
                    "data" => array(
                        "content" => $this->payload['notification_content'],
                    ),
                    "notification" => array(
                        'title' => 'Thông báo từ BigFast',
                        'body' => $this->payload['notification_content'],
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
                print_r($response);
                $response = json_decode($response);
                if ($response->success) {
                    NotificationSend::whereIn('notification_id', $this->payload['arr_notification_send'])->update(array('is_send' => 1));
                }
            }
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            print_r('=====================That Bai===================');
        }
    }

    public function failed($exception)
    {
        $exception->getMessage();
    }
}

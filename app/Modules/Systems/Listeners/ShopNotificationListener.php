<?php

namespace App\Modules\Systems\Listeners;

use App\Modules\Systems\Events\ShopNotificationEvent;
use App\Modules\Orders\Models\Repositories\Contracts\OrderFeeInterface;

class ShopNotificationListener
{
    protected $_orderFeeInterface;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(OrderFeeInterface $orderFeeInterface)
    {
        //
        $this->_orderFeeInterface = $orderFeeInterface;
    }

    /**
     * Handle the event.
     *
     * @param  ShopNotificationEvent  $event
     * @return void
     */
    public function handle(ShopNotificationEvent $event)
    {
        $SERVER_API_KEY = config('firebase.init.SERVER_API_KEY');

        $data = [
            "registration_ids" => $event->_dataFromController['arr_device_token'],
            "data" => array(
                "id" => $event->_dataFromController['notification_id'],
                "content" => $event->_dataFromController['notification_content'],
                "link" => $event->_dataFromController['notification_link'],
                "date" => $event->_dataFromController['date'],
            ),
            "notification" => array(
                'title' => 'Thông báo từ BigFast',
                'body' => $event->_dataFromController['notification_content'],
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

<?php

namespace App\Modules\Systems\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShopNotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $_dataFromController;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($dataFromController)
    {
        $this->_dataFromController = $dataFromController;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

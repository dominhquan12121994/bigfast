<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Created by PhpStorm.
 * User: Electric
 * Date: 3/31/2021
 * Time: 11:46 AM
 */

namespace App\Modules\Shops\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LoginEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userID;
    public $userType;
    public $deviceType;
    public $deviceToken;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userID, $userType, $deviceType, $deviceToken)
    {
        $this->userID = $userID;
        $this->userType = $userType;
        $this->deviceType = $deviceType;
        $this->deviceToken = $deviceToken;
    }
}
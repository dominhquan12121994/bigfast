<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserNotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $_payload;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->_payload = $payload;
    }
}

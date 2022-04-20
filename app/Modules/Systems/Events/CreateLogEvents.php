<?php

namespace App\Modules\Systems\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CreateLogEvents
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $log_data;
    public $order_id;
    public $currentUser;
    public $type;
    public $log_name;
    public $description;
    public $request;
    public $method;
    public $ip;
    public $agent;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct($log_data = [], $log_name, $description, $order_id = null)
    {
        $this->log_data = $log_data;
        $this->order_id = $order_id;

        $currentUser = null;
        $type = '';

        if ( request()->is('admin/*') ) {
            if (Auth::guard('admin')->check()) {
                $currentUser = \Auth::guard('admin')->user();
                $type = 'user';
            }
        } elseif ( request()->is('api/*') ) {
            if ( request()->is('api/v1/*') ) {
                if (Auth::guard('shop-api')->check()) {
                    $currentUser = \Auth::guard('shop-api')->user();
                    $type = 'shop';
                }
            } else {
                if (Auth::guard('admin-api')->check()) {
                    $currentUser = \Auth::guard('admin-api')->user();
                    $type = 'user';
                }
            }
        } else {
            if (Auth::guard('shop')->check()) {
                $currentUser = \Auth::guard('shop')->user();
                $type = 'shop';
            }
            if (Auth::guard('shopStaff')->check()) {
                $currentUser = \Auth::guard('shopStaff')->user();
                $type = 'shopStaff';
            }
        }

        $this->currentUser = $currentUser;
        $this->log_name = $log_name;
        $this->description = $description;
        $this->method = request()->method();
        $this->request = request()->except([
            '_token', 
            '_method', 
            'password', 
            'password_confirmation', 
            'fileImport',
            'file',
            'fileSearch'
        ]);
        $this->ip = request()->ip();
        $this->agent = request()->header('User-Agent');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

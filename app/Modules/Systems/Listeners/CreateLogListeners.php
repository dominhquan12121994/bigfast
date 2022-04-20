<?php

namespace App\Modules\Systems\Listeners;

use App\Modules\Systems\Events\CreateLogEvents;
use Illuminate\Http\Request;

use App\Modules\Systems\Models\Entities\SystemLog;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateLogListeners implements ShouldQueue
{
    use Queueable;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function viaQueue()
    {
        return 'jobActivityLogs';
    }

    /**
     * Handle the event.
     *
     * @param  CreateLogEvents  $event
     * @return void
     */
    public function handle(CreateLogEvents $event)
    {
        $log_data = $event->log_data;
        $order_id = $event->order_id;
        $currentUser = $event->currentUser;
        $type = $event->type;
        $log_name = $event->log_name;
        $description = $event->description;
        $method = $event->method;
        $request = $event->request;
        $ip = $event->ip;
        $agent = $event->agent;

        if ($currentUser) {
            foreach ( $log_data as $key => $val) {
                if ( isset($val['model']) && $val['model'] ) {
                    $log_data[$key]['model'] = get_class($val['model']);
                }
                if ( isset($val['old_data']) && $val['old_data'] ) {
                    $log_data[$key]['model'] = get_class($val['old_data']);
                    $log_data[$key]['old_data'] = $val['old_data']->getOriginal();
                    if (array_key_exists('created_at', $log_data[$key]['old_data'] ) && $log_data[$key]['old_data']['created_at'] ) {
                        $log_data[$key]['old_data']['created_at'] = date('Y-m-d H:i:s', strtotime($log_data[$key]['old_data']['created_at']) );
                    }
                    if (array_key_exists('updated_at', $log_data[$key]['old_data'] ) && $log_data[$key]['old_data']['updated_at'] ) {
                        $log_data[$key]['old_data']['updated_at'] = date('Y-m-d H:i:s', strtotime($log_data[$key]['old_data']['updated_at']) );
                    }
                    if (array_key_exists('deleted_at', $log_data[$key]['old_data'] ) && $log_data[$key]['old_data']['deleted_at'] ) {
                        $log_data[$key]['old_data']['deleted_at'] = date('Y-m-d H:i:s', strtotime($log_data[$key]['old_data']['deleted_at']) );
                    }
                    if (array_key_exists('email_verified_at', $log_data[$key]['old_data'] ) && $log_data[$key]['old_data']['email_verified_at'] ) {
                        $log_data[$key]['old_data']['email_verified_at'] = date('Y-m-d H:i:s', strtotime($log_data[$key]['old_data']['email_verified_at']) );
                    }
                    if (array_key_exists('created_date', $log_data[$key]['old_data'] ) && $log_data[$key]['old_data']['created_date'] ) {
                        $log_data[$key]['old_data']['created_date'] = date('Y-m-d H:i:s', strtotime($log_data[$key]['old_data']['created_date']) );
                    }
                }
            }

            SystemLog::create([
                'log_name' => $log_name,
                'description' => $description,
                'user_id' => (int)$currentUser->id,
                'user_type' => $type,
                'method' => $method,
                'request' => $request,
                'data' => $log_data,
                'ip' => $ip,
                'agent' => $agent,
                'date' => (int)date('Ymd'),
                'order_id' => $order_id,
            ]);
        }

    }
}

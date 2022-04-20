<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Resources;

use App\Http\Resources\AbstractResource;

/**
 * Class ShopNotificationResource
 * package App\Modules\Shops\Resources
 * author HuyDien <huydien.itgmail.com>
 */
class ShopNotificationResource extends AbstractResource
{
    /**
     * @param $request
     * @return array
     * @author HuyDien <huydien.it@gmail.com>
     */
    public function toArray($request)
    {
        return $this->resource->map(function ($item) {
            return array(
                'id' => array($item->notification_id),
                'content' => $item->notification->content,
                'link' => $item->notification->link,
                'type' => $item->notification->type,
                'sender_name' => $item->notification->user->name,
                'is_read' => $item->is_read,
                'created_at' => date('d/m/Y H:i', $item->created_time),
            );
        });
    }
}

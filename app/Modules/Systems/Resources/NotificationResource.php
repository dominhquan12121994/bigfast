<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Resources;

use Illuminate\Support\Collection;
use App\Http\Resources\AbstractResource;

/**
 * Class UsersResource
 * @package App\Modules\Transport\Resources
 * @author HuyDien <huydien.it@gmail.com>
 */
class NotificationResource extends AbstractResource
{
    /**
     * @param $request
     * @return array
     * @author HuyDien <huydien.it@gmail.com>
     */
    public function toArray($request)
    {
        $this->resource['data']->transform(function ($item){
            return array(
                'id' => $item->id,
                'content' => $item->notification->content,
                'link' => $item->notification->link ?? '',
                'is_read' => $item->is_read,
                'created_at' => date('d/m/Y H:i', strtotime($item->notification->created_at)),
            );
        });

        return $this->resource;
    }
}

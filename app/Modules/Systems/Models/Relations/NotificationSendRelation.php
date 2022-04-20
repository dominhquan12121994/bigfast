<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class OrderExtraRelation
 * @package App\Modules\Orders\Models\Relations
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Systems\Models\Relations;

use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Systems\Models\Entities\Notification;
use App\User;

trait NotificationSendRelation
{
    public function shop()
    {
        return $this->belongsTo(OrderShop::class, 'shop_id')->withTrashed();
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

}

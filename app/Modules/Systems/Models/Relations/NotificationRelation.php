<?php
/**
 * Class OrderShopRelation
 * @package App\Modules\Orders\Models\Relations
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Systems\Models\Relations;

use App\Modules\Systems\Models\Entities\NotificationSend;
use App\Modules\Systems\Models\Entities\User;

trait NotificationRelation
{
    public function receiveNotification()
    {
        return $this->hasMany(NotificationSend::class, 'notification_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }

}

<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Entities;

use App\Models\Entities\AbstractModel;
use App\Modules\Systems\Models\Relations\NotificationSendRelation;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationSend extends AbstractModel
{
    use SoftDeletes;
    use NotificationSendRelation;

    protected $table = 'system_notification_send';

    protected $fillable = [
        'notification_id', 'shop_id', 'user_id', 'created_date', 'created_time', 'is_read', 'is_send'
    ];

    public $timestamps = false;
}

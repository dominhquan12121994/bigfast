<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Systems\Models\Relations\NotificationRelation;

class Notification extends AbstractModel
{
    use SoftDeletes;
    use NotificationRelation;

    protected $table = 'system_notifications';

    protected $fillable = [
        'sender_id', 'content_data', 'link', 'type', 'created_date', 'receiver_quantity', 'selected_purpose', 'selected_branch', 'selected_scale'
    ];

    public $timestamps = true;
}

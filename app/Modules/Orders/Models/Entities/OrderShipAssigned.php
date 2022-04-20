<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Entities\AbstractModel;

use App\Modules\Orders\Models\Relations\OrderShipAssignedRelation;

class OrderShipAssigned extends AbstractModel
{
    use SoftDeletes;
    use OrderShipAssignedRelation;

    protected $table = 'order_ship_assigned';

    protected $fillable = [
        'user_role', 'user_id', 'shop_id', 'order_id', 'sender_id', 'p_id', 'd_id', 'w_id', 'status', 'status_detail', 'time_assigned', 'time_success', 'time_failed', 'failed_status'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public $timestamps = false;
}

<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Orders\Models\Relations\OrdersQueue;

class OrderQueue extends AbstractModel
{
    use OrdersQueue;

    protected $table = 'order_queues';

    protected $fillable = [
        'status', 'shop_id', 'created_date', 'receiver_name', 'receiver_phone', 'receiver_address', 'cod', 'client_code'
    ];
}
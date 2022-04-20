<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Entities\AbstractModel;

class OrderStatusOvertime extends AbstractModel
{
    use SoftDeletes;
    
    protected $table = 'order_status_overtime';

    protected $fillable = [
        'shop_id', 'order_id', 'status', 'status_detail', 'start_date', 'end_date'
    ];

    protected $dates = [
        'deleted_at'
    ];
}

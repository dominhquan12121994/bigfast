<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;

class OrderFeeShop extends AbstractModel
{
    protected $table = 'order_fee_shop';

    protected $fillable = [
        'shop_id', 'fee_type', 'value'
    ];

    public $timestamps = false;
}
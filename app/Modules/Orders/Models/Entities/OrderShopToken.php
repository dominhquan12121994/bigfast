<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;

class OrderShopToken extends AbstractModel
{
    protected $table = 'order_shops_token';

    protected $fillable = [
        'shop_id', 'token'
    ];
}

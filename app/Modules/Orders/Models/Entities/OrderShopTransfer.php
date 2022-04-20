<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;

class OrderShopTransfer extends AbstractModel
{
    protected $table = 'order_shop_transfer';

    protected $fillable = [
        'date', 'shop_id', 'money', 'user_id', 'note'
    ];

    public $timestamps = true;
}
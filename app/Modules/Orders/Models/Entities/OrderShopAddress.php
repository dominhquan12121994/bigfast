<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Orders\Models\Relations\OrderShopAddressRelation;

class OrderShopAddress extends AbstractModel
{
    use SoftDeletes;
    use OrderShopAddressRelation;

    protected $table = 'order_shops_address';

    protected $fillable = [
        'shop_id', 'p_id', 'd_id', 'w_id', 'type', 'name', 'phone', 'address', 'default'
    ];

    protected $visible = ['name', 'phone', 'address'];

    protected $dates = [
        'deleted_at'
    ];
}
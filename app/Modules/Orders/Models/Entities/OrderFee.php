<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use App\Modules\Orders\Models\Relations\OrderIncurredFeeRelation;

class OrderFee extends AbstractModel
{
    use OrderIncurredFeeRelation;

    protected $table = 'order_fee';

    protected $fillable = [
        'shop_id', 'order_id', 'fee_type', 'value', 'date'
    ];

    public $timestamps = false;
}

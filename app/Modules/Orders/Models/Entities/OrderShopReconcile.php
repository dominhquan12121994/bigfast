<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;

class OrderShopReconcile extends AbstractModel
{
    protected $table = 'order_shop_reconcile';

    protected $fillable = [
        'date', 'shop_id', 'total_cod', 'money_indemnify', 'fee_transport', 'fee_insurance', 'fee_cod', 'fee_refund', 'fee_store', 'fee_change_info', 'fee_transfer', 'refund_cod', 'refund_transport'
    ];

    public $timestamps = true;
}

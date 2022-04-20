<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use App\Modules\Orders\Models\Relations\ShopReconcileHistoryRelation;

class ShopReconcileHistory extends AbstractModel
{
    use ShopReconcileHistoryRelation;

    protected $table = 'order_shop_reconcile_history';

    protected $fillable = [
        'id', 'begin_date', 'end_date', 'shop_id', 'total_fee', 'total_cod', 'money_indemnify', 'total_du', 'user_reconcile'
    ];

    public $timestamps = true;
}

<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class OrderExtraRelation
 * @package App\Modules\Orders\Models\Relations
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Relations;

use App\Modules\Orders\Models\Entities\OrderShop;

trait ShopReconcileHistoryRelation
{
    public function shop()
    {
        return $this->belongsTo(OrderShop::class, 'shop_id')->withTrashed();
    }

}

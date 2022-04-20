<?php
/**
 * Trait OrderIncurredFeeRelation
 * @package App\Modules\Orders\Models\Relations
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Relations;

use App\Modules\Orders\Models\Entities\Orders;
use App\Modules\Orders\Models\Entities\OrderShop;

trait OrderIncurredFeeRelation
{
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id')->withTrashed();
    }

    public function shop()
    {
        return $this->belongsTo(OrderShop::class, 'shop_id')->withTrashed();
    }
}

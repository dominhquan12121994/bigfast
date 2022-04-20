<?php

/**
 * Class OrdersRelation
 * @package App\Modules\Orders\Models\Relations
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Relations;

use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Orders\Models\Entities\Orders;
use App\Modules\Systems\Models\Entities\User;

trait OrderShipAssignedRelation
{
    public function shop()
    {
        return $this->belongsTo(OrderShop::class, 'shop_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id')->withTrashed();
    }
}

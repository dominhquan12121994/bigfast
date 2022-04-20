<?php
/**
 * Class OrderShopRelation
 * @package App\Modules\Orders\Models\Relations
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Relations;

use App\Modules\Orders\Models\Entities\OrderShopBank;
use App\Modules\Orders\Models\Entities\OrderShopStaff;
use App\Modules\Orders\Models\Entities\OrderShopAddress;

trait OrderShopRelation
{
    public function bank()
    {
        return $this->hasOne(OrderShopBank::class, 'id');
    }

    public function getAddress()
    {
        return $this->hasMany(OrderShopAddress::class, 'shop_id')->withTrashed();
    }

    public function staff()
    {
        return $this->hasMany(OrderShopStaff::class, 'shop_id');
    }
}

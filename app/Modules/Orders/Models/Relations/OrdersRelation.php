<?php

/**
 * Class OrdersRelation
 * @package App\Modules\Orders\Models\Relations
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Relations;

use App\Modules\Orders\Models\Entities\OrderLog;
use App\Modules\Orders\Models\Entities\OrderTrace;
use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Orders\Models\Entities\OrderProduct;
use App\Modules\Orders\Models\Entities\OrderExtra;
use App\Modules\Orders\Models\Entities\OrderReceiver;
use App\Modules\Orders\Models\Entities\OrderShopAddress;
use App\Modules\Orders\Models\Entities\OrderService;
use App\Modules\Operators\Models\Entities\Contacts;

trait OrdersRelation
{
    public function logs()
    {
        return $this->hasMany(OrderLog::class, 'order_id');
    }

    public function traces()
    {
        return $this->hasMany(OrderTrace::class, 'order_id');
    }

    public function extra()
    {
        return $this->hasOne(OrderExtra::class, 'id');
    }

    public function shop()
    {
        return $this->belongsTo(OrderShop::class, 'shop_id')->withTrashed();
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id')->withTrashed();
    }

    public function sender()
    {
        return $this->belongsTo(OrderShopAddress::class, 'sender_id')->withTrashed();
    }

    public function receiver()
    {
        return $this->belongsTo(OrderReceiver::class, 'receiver_id')->withTrashed();
    }

    public function refund()
    {
        return $this->belongsTo(OrderShopAddress::class, 'refund_id')->withTrashed();
    }

    public function ordershop()
    {
        return $this->belongsTo(OrderShop::class, 'shop_id')->withTrashed();
    }

    public function servicetype()
    {
        return $this->belongsTo(OrderService::class, 'service_type', 'alias')->withTrashed();
    }

    public function contacts()
    {
        return $this->hasMany(Contacts::class, 'order_id');
    }
}

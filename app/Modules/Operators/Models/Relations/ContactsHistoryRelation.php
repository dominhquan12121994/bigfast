<?php

namespace App\Modules\Operators\Models\Relations;

use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Systems\Models\Entities\User;
use App\Modules\Operators\Models\Entities\ContactsType;

trait ContactsHistoryRelation
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function shop()
    {
        return $this->belongsTo(OrderShop::class, 'user_id')->withTrashed();
    }
}

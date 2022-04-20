<?php

namespace App\Modules\Operators\Models\Relations;

use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Systems\Models\Entities\User;
use App\Modules\Operators\Models\Entities\ContactsType;
use App\Modules\Operators\Models\Entities\ContactsHistory;

trait ContactsRelation
{
    public function orderShop()
    {
        return $this->belongsTo(OrderShop::class, 'shop_id')->withTrashed();
    }

    public function shop()
    {
        return $this->belongsTo(OrderShop::class, 'user_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function assign()
    {
        return $this->belongsTo(User::class, 'assign_id')->withTrashed();
    }

    public function typeContacts()
    {
        return $this->belongsTo(ContactsType::class, 'contacts_type_id')->withTrashed();
    }

    public function history()
    {
        return $this->hasMany(ContactsHistory::class, 'contacts_id')->withTrashed();
    }
}

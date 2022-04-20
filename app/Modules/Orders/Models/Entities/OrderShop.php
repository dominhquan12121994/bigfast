<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

use App\Modules\Orders\Models\Mutators\OrderShopMutator;
use App\Modules\Orders\Models\Relations\OrderShopRelation;

use App\Modules\Orders\Notifications\MailResetPasswordNotification;

/* Filter */
use App\Modules\Orders\Models\Filters\ShopsFilter;

class OrderShop extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    use HasApiTokens;
    use OrderShopMutator;
    use OrderShopRelation;
    use ShopsFilter;

    protected $guard_name = 'shop';

    protected $table = 'order_shops';

    protected $fillable = [
        'name', 'phone', 'address', 'email', 'password', 'api_token'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $visible = [
        'id', 'phone', 'email', 'name', 'address'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $attributes = [
        'menuroles' => 'shop',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordNotification($token, 'shop'));
    }

//    public function scopeWhereLike($query, $column, $value)
//    {
//        return $query->where($column, 'like', '%'.$value.'%');
//    }

//    public function scopeFilter($query, ShopsFilter $filter){
//        return $filter->apply($query);
//    }
}

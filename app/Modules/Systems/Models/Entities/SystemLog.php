<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Entities;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Modules\Systems\Models\Entities\User;
use App\Modules\Orders\Models\Entities\OrderShop;

class SystemLog extends Model
{
    use SoftDeletes;

    // protected $primaryKey = 'id';

    protected $collection = 'system_logs';

    protected $connection = 'mongodb';

    protected $dates = ['deleted_at'];

    protected $fillable = array(
        'log_name',
        'description',
        'user_id',
        'user_type',
        'method',
        'request',
        'data',
        'order_id',
        'ip',
        'agent',
        'date',
    );

    public function user()
    {
        return User::where('id', '=', (int) $this->user_id)->first();
    }

    public function shop()
    {
        return OrderShop::where('id', '=', (int) $this->user_id)->first();
    }
}

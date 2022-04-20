<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Orders\Models\Relations\OrderReceiverRelation;

class OrderReceiver extends AbstractModel
{
    use SoftDeletes;
    use OrderReceiverRelation;

    protected $table = 'order_receivers';

    protected $fillable = [
        'p_id', 'd_id', 'w_id', 'name', 'phone', 'address'
    ];

    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'p_id', 'd_id', 'w_id'];

//    protected $visible = ['name', 'phone', 'address'];

    protected $dates = [
        'deleted_at'
    ];
}
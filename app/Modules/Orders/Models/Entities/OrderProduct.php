<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'order_products';

    protected $fillable = [
        'order_id', 'code', 'name', 'quantity', 'price', 'created_at', 'updated_at'
    ];

    protected $dates = [
        'deleted_at'
    ];
}
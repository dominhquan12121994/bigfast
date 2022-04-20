<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderService extends AbstractModel
{
    use SoftDeletes;
    
    protected $table = 'order_services';

    protected $fillable = [
        'name',
        'alias',
        'description',
        'status', 
    ];

    public $timestamps = true;
}
<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTrace extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'order_traces';

    protected $fillable = [
        'order_id', 'status', 'log_id', 'timer', 'note'
    ];

    public $timestamps = false;
}
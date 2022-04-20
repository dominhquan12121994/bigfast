<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;

class OrderLog extends AbstractModel
{
    protected $table = 'order_logs';

    protected $fillable = [
        'order_id', 'user_type', 'user_id', 'log_type', 'status', 'status_detail', 'logs', 'note1', 'note2', 'timer'
    ];

    protected $visible = ['note1'];

    public $timestamps = false;
}
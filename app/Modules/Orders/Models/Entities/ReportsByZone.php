<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;

use App\Modules\Orders\Models\Relations\ReportsByZoneRelation;

class ReportsByZone extends AbstractModel
{
    use ReportsByZoneRelation;
    
    protected $table = 'order_reports_by_zone';

    protected $fillable = [
        'shop_id', 'date', 'count', 'd_id'
    ];

    public $timestamps = false;
}
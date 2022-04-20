<?php

/**
 * Class Reports
 * @package App\Modules\Reports\Models
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Reports\Models\Entities;

/* Contracts */
use App\Models\Entities\AbstractModel;

/* Traits */
use App\Modules\Reports\Models\Mutators\ReportsMutator;
use App\Modules\Reports\Models\Relations\ReportsRelation;

/* Filter */
//use App\Modules\Reports\Models\Filters\ReportsFilter;

/* Observer */
use App\Modules\Reports\Models\Observers\ReportsObserver;

/* Libs */

class Reports extends AbstractModel
{
    use ReportsMutator, ReportsRelation;

    protected $fillable = array();

    protected $hidden = array(
    );

    /*
    public function scopeFilter($query, ReportsFilter $filter){
        return $filter->apply($query);
    }
    */

    public static function boot()
    {
        parent::boot();
//        self::observe(ReportsObserver::class);
    }
}

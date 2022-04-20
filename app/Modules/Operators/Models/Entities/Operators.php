<?php

/**
 * Class Operators
 * @package App\Modules\Operators\Models
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Operators\Models\Entities;

/* Contracts */
use App\Models\Entities\AbstractModel;

/* Traits */
use App\Modules\Operators\Models\Mutators\OperatorsMutator;
use App\Modules\Operators\Models\Relations\OperatorsRelation;

/* Filter */
//use App\Modules\Operators\Models\Filters\OperatorsFilter;

/* Observer */
use App\Modules\Operators\Models\Observers\OperatorsObserver;

/* Libs */

class Operators extends AbstractModel
{
    use OperatorsMutator, OperatorsRelation;

    protected $fillable = array();

    protected $hidden = array(
    );

    /*
    public function scopeFilter($query, OperatorsFilter $filter){
        return $filter->apply($query);
    }
    */

    public static function boot()
    {
        parent::boot();
//        self::observe(OperatorsObserver::class);
    }
}

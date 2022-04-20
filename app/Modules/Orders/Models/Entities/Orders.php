<?php

/**
 * Class Orders
 * @package App\Modules\Orders\Models
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Entities;

/* Contracts */
use App\Models\Entities\AbstractModel;

/* Traits */
use App\Modules\Orders\Models\Mutators\OrdersMutator;
use App\Modules\Orders\Models\Relations\OrdersRelation;

/* Filter */
//use App\Modules\Orders\Models\Filters\OrdersFilter;

/* Libs */
use Illuminate\Database\Eloquent\SoftDeletes;
use Elasticquent\ElasticquentTrait;

class Orders extends AbstractModel
{
    use OrdersMutator, OrdersRelation;
    use ElasticquentTrait;
    use SoftDeletes;

    protected $fillable = array(
        'shop_id',
        'sender_id',
        'refund_id',
        'receiver_id',
        'status',
        'status_detail',
        'lading_code',
        'transport_fee',
        'total_fee',
        'cod',
        'insurance_value',
        'service_type',
        'payfee',
        'weight',
        'height',
        'width',
        'length',
        'created_date',
        'collect_money_date',
        'last_change_date'
    );

    protected $hidden = array(
        'deleted_at'
    );

    protected $dates = [
        'deleted_at'
    ];

    /*
    public function scopeFilter($query, OrdersFilter $filter){
        return $filter->apply($query);
    }
    */

    function getIndexName()
    {
        return 'orders';
    }

//    function getTypeName()
//    {
//        return 'orders';
//    }

    public static function boot()
    {
        parent::boot();
//        self::observe(OrdersObserver::class);
    }
}

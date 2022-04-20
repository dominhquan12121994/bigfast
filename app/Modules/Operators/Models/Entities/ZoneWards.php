<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Entities;

use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Entities\AbstractModel;

/* Traits */
use App\Modules\Operators\Models\Mutators\WardsMutator;
use App\Modules\Operators\Models\Relations\WardsRelation;
use App\Modules\Orders\Models\Services\TraitAddressServices;

class ZoneWards extends AbstractModel
{
    use SoftDeletes;
    use ElasticquentTrait;
    use WardsRelation;
    use WardsMutator;
    use TraitAddressServices;

    protected $table = 'zone_wards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['p_id', 'd_id', 'code', 'alias', 'name', 'short_name', 'keyword'];

    public $timestamps = false;

    protected $dates = [
        'deleted_at'
    ];

//    protected $mappingProperties = array(
//        'properties' => [
//            'short_name' => [
//                'type' => 'string',
//                'analyzer' => 'standard',
//            ],
//            'alias' => [
//                'type' => 'string',
//                'analyzer' => 'standard',
//            ]
//        ]
//    );

    protected $searchable = [
        'keyword'
    ];

    function getIndexName()
    {
        return 'wards';
    }

//    function getTypeName()
//    {
//        return 'list';
//    }
}

<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Entities;

use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Entities\AbstractModel;

/* Traits */
use App\Modules\Operators\Models\Mutators\DistrictsMutator;
use App\Modules\Operators\Models\Relations\DistrictsRelation;
use App\Modules\Orders\Models\Services\TraitAddressServices;

class ZoneDistricts extends AbstractModel
{
    use SoftDeletes;
    use ElasticquentTrait;
    use DistrictsRelation;
    use DistrictsMutator;
    use TraitAddressServices;

    protected $table = 'zone_districts';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'code', 'alias', 'name', 'short_name', 'keyword'];

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
        return 'districts';
    }

//    function getTypeName()
//    {
//        return 'list';
//    }

    protected $dates = [
        'deleted_at'
    ];

    /**
     * Get the province zone.
     *
     * @param  string  $value
     * @return string
     */
//    public function getTypeAttribute($value)
//    {
//        return array('noi' => 'Nội thành', 'ngoai' => 'Ngoại thành', 'huyen' => 'Huyện/Xã')[$value];
//    }
}

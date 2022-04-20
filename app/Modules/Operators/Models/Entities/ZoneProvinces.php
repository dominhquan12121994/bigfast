<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Entities;

use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Entities\AbstractModel;

/* Traits */
use App\Modules\Operators\Models\Mutators\ProvincesMutator;
use App\Modules\Operators\Models\Relations\ProvincesRelation;
use App\Modules\Orders\Models\Services\TraitAddressServices;

class ZoneProvinces extends AbstractModel
{
    use SoftDeletes;
    use ElasticquentTrait;
    use ProvincesRelation;
    use ProvincesMutator;
    use TraitAddressServices;

    protected $table = 'zone_provinces';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['zone', 'code', 'alias', 'name', 'short_name', 'keyword'];

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
        return 'provinces';
    }

//    function getTypeName()
//    {
//        return 'provinces';
//    }

    /**
     * Get the province zone.
     *
     * @param  string  $value
     * @return string
     */
    public function getZoneAttribute($value)
    {
        return array('bac' => 'Miền Bắc', 'trung' => 'Miền Trung', 'nam' => 'Miền Nam')[$value];
    }
}

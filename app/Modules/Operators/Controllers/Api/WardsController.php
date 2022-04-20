<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AbstractApiController;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\WardsInterface;

use App\Modules\Operators\Resources\WardsResource;

class WardsController extends AbstractApiController
{

    protected $_provincesInterface;
    protected $_districtsInterface;
    protected $_wardsInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface,
                                WardsInterface $wardsInterface)
    {
        parent::__construct();

        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
        $this->_wardsInterface = $wardsInterface;
    }

    /**
     * Display a listing by provinces.
     *
     * @return \Illuminate\Http\Response
     */
    public function getByDistrict($id)
    {
        $wards = $this->_wardsInterface->getMore(array('d_id' => $id));
        return $this->_responseSuccess('Success', new WardsResource($wards));
    }
}

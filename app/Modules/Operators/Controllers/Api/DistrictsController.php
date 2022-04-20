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

use App\Modules\Operators\Resources\DistrictsResource;

class DistrictsController extends AbstractApiController
{

    protected $_provincesInterface;
    protected $_districtsInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProvincesInterface $provincesInterface, DistrictsInterface $districtsInterface)
    {
        parent::__construct();

        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
    }

    /**
     * Display a listing by provinces.
     *
     * @return \Illuminate\Http\Response
     */
    public function getByProvince($id)
    {
        $districts = $this->_districtsInterface->getMore(array('p_id' => $id));
        return $this->_responseSuccess('Success', new DistrictsResource($districts));
    }
}

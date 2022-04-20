<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Operators\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AbstractApiController;
use App\Modules\Operators\Models\Repositories\Contracts\PostOfficesInterface;

use App\Modules\Operators\Resources\PostOfficesResource;

class PostOfficesController extends AbstractApiController
{
    protected $_postOfficesInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PostOfficesInterface $postOfficesInterface)
    {
        parent::__construct();

        $this->_postOfficesInterface = $postOfficesInterface;
    }

    /**
     * Display a listing by provinces.
     *
     * @return \Illuminate\Http\Response
     */
    public function getByZone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'p_id' => 'numeric',
            'd_id' => 'numeric',
            'w_id' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $filter = array();
        if ($request->has('p_id')) {
            $filter['p_id'] = $request->input('p_id');
        }
        if ($request->has('d_id')) {
            $filter['d_id'] = $request->input('d_id');
        }
        if ($request->has('p_id')) {
            $filter['w_id'] = $request->input('w_id');
        }

        $post_offices = $this->_postOfficesInterface->getMore($filter, array('with' => ['provinces', 'districts', 'wards']));
        return $this->_responseSuccess('Success', new PostOfficesResource($post_offices));
    }
}

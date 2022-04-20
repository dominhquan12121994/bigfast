<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AbstractApiController;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;

use App\Modules\Orders\Resources\FindShopsResource;

class ShopsController extends AbstractApiController
{

    protected $_shopsInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ShopsInterface $shopsInterface)
    {
        parent::__construct();

        $this->_shopsInterface = $shopsInterface;
    }

    /**
     * Display a listing by provinces.
     *
     * @return \Illuminate\Http\Response
     */
    public function findByName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Yêu cầu từ khoá tối thiểu 3 ký tự');
        }

        $shopList = $this->_shopsInterface->getMore(
            array(
                'name' => $request->input('search'),
                'phone' => $request->input('search'),
                'email' => $request->input('search')
            )
        );

        return $this->_responseSuccess('Success', new FindShopsResource($shopList));
    }

    public function getListJson(Request $request)
    {
        $shopList = $this->_shopsInterface->getMore();
        $data = $shopList->map(function ($shop) {
            return $shop->name;
        })->toArray();
        return json_encode($data);
    }
}

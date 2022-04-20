<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

use App\Http\Controllers\Api\AbstractApiController;

use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;

use App\Modules\Orders\Models\Services\CalculatorFeeServices;

class CalculatorFeeController extends AbstractApiController
{

    protected $_calculatorFeeServices;
    protected $_shopAddressInterface;
    protected $_orderServiceInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CalculatorFeeServices $calculatorFeeServices,
                                ShopAddressInterface $shopAddressInterface,
                                OrderServiceInterface $orderServiceInterface)
    {
        parent::__construct();

        $this->_calculatorFeeServices = $calculatorFeeServices;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_orderServiceInterface = $orderServiceInterface;
    }

    public function calculator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'p_id_send' => 'required|exists:zone_provinces,id',
            'd_id_send' => 'required|exists:zone_districts,id',
            'p_id_receive' => 'required|exists:zone_provinces,id',
            'd_id_receive' => 'required|exists:zone_districts,id',
            'service' => 'required|exists:order_services,alias',
            'weight' => 'required|numeric|gt:0'
        ],
            [
                'weight.gt' => 'Khối lượng phải lớn hơn 0',
                'weight.required' => 'Vui lòng nhập khối lượng',
            ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $payload = $request->only(array('p_id_send', 'p_id_receive', 'd_id_receive', 'service', 'weight'));

        if ($request->has('senderId')) {
            $shopAddress = $this->_shopAddressInterface->getById((int)$request->input('senderId'));
            if (!$shopAddress) {
                return $this->_responseError($validator->errors());
            }
            $payload['p_id_send'] = $shopAddress->p_id;
        }

        $calculatedFee = $this->_calculatorFeeServices->calculatorFee($payload);

        return $this->_responseSuccess('Success', $calculatedFee);
    }

    public function calculatorByShop(Request $request)
    {
        $result = array();

        $validator = Validator::make($request->all(), [
            'p_id_receive' => 'required|exists:zone_provinces,id',
            'd_id_receive' => 'required|exists:zone_districts,id',
            'weight' => 'required|numeric|gt:0',
            'p_id_send' => 'required|exists:zone_provinces,id',
            'expect_pick' => 'required|date'
        ],
            [
                'weight.gt' => 'Khối lượng phải lớn hơn 0',
                'weight.required' => 'Vui lòng nhập khối lượng',
            ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        $payload = $request->only(array('p_id_send', 'p_id_receive', 'd_id_receive', 'weight'));

        $services = $this->_orderServiceInterface->getMore(array('status' => 1));

        foreach ($services as $service) {
            $payload['service'] = $service->alias;
            $calculator = $this->_calculatorFeeServices->calculatorFee($payload);

            $result[$service->alias] = array(
                'fee' => $calculator->result,
                'expect_pick' => date('d-m-Y H:i', strtotime($request->input('expect_pick') . " +". $calculator->timePick->to ." days") )
            );
        }

        return $this->_responseSuccess('Success', $result);
    }

}
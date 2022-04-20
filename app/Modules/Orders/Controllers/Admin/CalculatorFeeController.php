<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Admin;

use Session;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AbstractAdminController;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;

use App\Modules\Orders\Models\Services\CalculatorFeeServices;

class CalculatorFeeController extends AbstractAdminController
{
    protected $_provincesInterface;
    protected $_districtsInterface;
    protected $_calculatorFeeServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface,
                                OrderServiceInterface $orderServiceInterface,
                                CalculatorFeeServices $calculatorFeeServices)
    {
        parent::__construct();

        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
        $this->_orderServiceInterface = $orderServiceInterface;
        $this->_calculatorFeeServices = $calculatorFeeServices;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_calculator_fee_view'))
        {
            abort(403);
        }

        $provinces = $this->_provincesInterface->getMore();
        $services = $this->_orderServiceInterface->getMore(array('status' => 1));

        $p_id_send = Session::has('p_id_send') ? Session::get('p_id_send') : $provinces[0]->id;
        $districtSend = $this->_districtsInterface->getMore(array('p_id' => $p_id_send));

        $p_id_receive = Session::has('p_id_receive') ? Session::get('p_id_receive') : $provinces[0]->id;
        $districtReceiver = $this->_districtsInterface->getMore(array('p_id' => $p_id_receive));

        return view('Orders::orders.calculator-fee', [
            'provinces' => $provinces,
            'districtSend' => $districtSend,
            'districtReceiver' => $districtReceiver,
            'services' => $services
            ]
        );
    }

    public function calculator(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_calculator_fee_view'))
        {
            abort(403);
        }
        
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
            return Redirect::back()->withInput()->withErrors($validator->errors())->with($request->all());
        }

        $payload = $request->only(array('p_id_send', 'p_id_receive', 'd_id_receive', 'service', 'weight'));
        $calculatedFee = $this->_calculatorFeeServices->calculatorFee($payload);

        if ($calculatedFee->status) {
            $request->merge(['calculatedFee' => number_format($calculatedFee->result)]);
            return Redirect::back()->withInput()->with($request->all());
        } else {
            return Redirect::back()->withInput()->withErrors($calculatedFee->error)->with($request->all());
        }
    }
}

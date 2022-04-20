<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Admin;

use Auth;
use Session;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Admin\AbstractAdminController;

use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderFeeInterface;
use App\Modules\Orders\Models\Services\CashFlowServices;
use App\Modules\Orders\Models\Services\OrderServices;
use App\Modules\Orders\Exports\CashFlowNewExport;

class CashFlowController extends AbstractAdminController
{
    protected $_ordersInterface;
    protected $_orderFeeInterface;
    protected $_cashFlowServices;
    protected $_orderServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrdersInterface $ordersInterface,
                                OrderFeeInterface $orderFeeInterface,
                                CashFlowServices $cashFlowServices,
                                OrderServices $orderServices)
    {
        parent::__construct();

        $this->_ordersInterface = $ordersInterface;
        $this->_cashFlowServices = $cashFlowServices;
        $this->_orderFeeInterface = $orderFeeInterface;
        $this->_orderServices = $orderServices;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_cash_flow_view'))
        {
            abort(403);
        }

        $userReconcile = Auth::guard('admin')->id();

        $arrDataCash = collect(array());
        $calculatorDate = date('Y-m-d', strtotime('-2 day'));
        if ($request->has('date')) {
            $calculatorDate = date('Y-m-d', strtotime($request->input('date')));
        }

        $dataCash = $this->_cashFlowServices->calculator(array('calculatorDate' => $calculatorDate));

        if ($dataCash->status) {
            $arrDataCash = collect($dataCash->result);
        }

        return view('Orders::cash-flow.index', array(
            'arrDataCash' => $arrDataCash,
            'calculatorDate' => $calculatorDate,
            'userReconcile' => $userReconcile,
        ));
    }

    public function export(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_cash_flow_view'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'shopId' => 'required|numeric',
            'timeBegin' => 'required|numeric',
            'timeEnd' => 'required|numeric',
            'shopName' => 'required',
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        $fileName = 'orders.xlsx';
        $timeBegin = date('d-m-Y', strtotime($request->input('timeBegin')));
        $timeEnd = date('d-m-Y', strtotime($request->input('timeEnd')));

        $fileName = 'Đối-soát-shop-'. $request->input('shopName') .'-từ ' . $timeBegin . '-đến-' . $timeEnd. '.xlsx';
        $payload = [
            'timeBegin' => $request->input('timeBegin'),
            'timeEnd' => $request->input('timeEnd'),
            'shopId' => $request->input('shopId'),
        ];

        return Excel::download(new CashFlowNewExport($this->_orderFeeInterface, $this->_orderServices, $payload), $fileName);
    }
}

<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Web;

use Auth;
use Session;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Web\AbstractWebController;

use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderFeeInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingCodInterface;

use App\Modules\Orders\Models\Services\CashFlowServices;
use App\Modules\Orders\Models\Services\ShopReconcileServices;
use App\Modules\Orders\Models\Services\OrderServices;
use App\Modules\Orders\Exports\CashFlowNewExport;

class CashFlowController extends AbstractWebController
{
    protected $_ordersInterface;
    protected $_cashFlowServices;
    protected $_shopReconcileServices;
    protected $_orderServices;
    protected $_orderFeeInterface;
    protected $_orderSettingCodInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrdersInterface $ordersInterface,
                                ShopReconcileServices $shopReconcileServices,
                                OrderFeeInterface $orderFeeInterface,
                                OrderServices $orderServices,
                                OrderSettingCodInterface $orderSettingCodInterface,
                                CashFlowServices $cashFlowServices)
    {
        parent::__construct();

        $this->_ordersInterface = $ordersInterface;
        $this->_cashFlowServices = $cashFlowServices;
        $this->_shopReconcileServices = $shopReconcileServices;
        $this->_orderFeeInterface = $orderFeeInterface;
        $this->_orderServices = $orderServices;
        $this->_orderSettingCodInterface = $orderSettingCodInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $you = auth('shop')->user();
        $shop_id = $you->id;

        $shopId = (int)$request->input('shop_id', 0);
        $beginDate = $request->input('begin', date('d-m-Y', strtotime('-30 days')));
        $endDate = $request->input('end', date('d-m-Y'));

        $beginDate = (int)date('Ymd', strtotime($beginDate));
        $endDate = (int)date('Ymd', strtotime($endDate));

        $filter = array(
            'date_range' => array($beginDate, $endDate)
        );

        $arrDataCash = collect(array());
        $calculatorDate = date('Y-m-d');

        $dataCash = $this->_cashFlowServices->calculator(array('calculatorDate' => $calculatorDate, 'idOnce' => array($shop_id)));

        if ($dataCash->status) {
            $arrDataCash = collect($dataCash->result);
        }

        $payload = array(
            'shop_id' => $shop_id,
            'date_range' => array(
                $beginDate,
                $endDate
            )
        );

        $shopReconcile = $this->_shopReconcileServices->getReconcile($payload);

        return view('Shops::cash-flow.index', array(
            'arrDataCash' => $arrDataCash,
            'shopReconcile' => $shopReconcile,
            'shopId' => $shop_id,
            'filter' => $filter,
        ));
    }

    public function export(Request $request)
    {
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

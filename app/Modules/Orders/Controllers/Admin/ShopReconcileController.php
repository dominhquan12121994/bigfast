<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Admin;

use Auth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Redirect;

use App\Http\Controllers\Admin\AbstractAdminController;

use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;

use App\Modules\Orders\Models\Services\CalculatorFeeServices;

use App\Modules\Orders\Models\Services\ShopReconcileServices;

use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;

use App\Modules\Orders\Models\Services\CashFlowServices;

use Maatwebsite\Excel\Facades\Excel;
use App\Modules\Orders\Models\Repositories\Contracts\OrderFeeInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingCodInterface;
use App\Modules\Orders\Models\Services\OrderServices;
use App\Modules\Orders\Exports\CashFlowNewExport;

class ShopReconcileController extends AbstractAdminController
{

    protected $_calculatorFeeServices;
    protected $_shopReconcileServices;
    protected $_shopAddressInterface;
    protected $_cashFlowServices;
    protected $_orderServices;
    protected $_orderFeeInterface;
    protected $_orderSettingCodInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CalculatorFeeServices $calculatorFeeServices,
                                ShopReconcileServices $shopReconcileServices,
                                ShopAddressInterface $shopAddressInterface,
                                CashFlowServices $cashFlowServices,
                                OrderFeeInterface $orderFeeInterface,
                                OrderServices $orderServices,
                                OrderSettingCodInterface $orderSettingCodInterface,
                                ShopsInterface $shopsInterface)
    {
        parent::__construct();

        // $this->middleware(['auth:admin-api', 'scopes:admin']);

        $this->_calculatorFeeServices = $calculatorFeeServices;
        $this->_shopReconcileServices = $shopReconcileServices;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_cashFlowServices = $cashFlowServices;
        $this->_shopsInterface = $shopsInterface;
        $this->_orderFeeInterface = $orderFeeInterface;
        $this->_orderServices = $orderServices;
        $this->_orderSettingCodInterface = $orderSettingCodInterface;
    }

    public function getReconcile(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_reconcile_history_view'))
        {
            abort(403);
        }

        $shopId = (int)$request->input('shop_id', 0);
        $beginDate = $request->input('begin', date('Ymd', strtotime('-30 days')));
        $endDate = $request->input('end', date('Ymd'));

        $beginDate = (int)date('Ymd', strtotime($beginDate));
        $endDate = (int)date('Ymd', strtotime($endDate));

        $filter = array(
            'date_range' => array($beginDate, $endDate),
            'shop_id' => $shopId
        );

        if ($shopId) {
            $shopInfo = $this->_shopsInterface->getById($shopId);
        } else {
            $shopInfo = false;
        }

        $payload = array(
            'shop_id' => $shopId,
            'date_range' => array(
                $beginDate,
                $endDate
            )
        );

        $arrShop = $this->_shopReconcileServices->getReconcile($payload);

        return view('Orders::reconcile-history.index', array(
            'shopInfo' => $shopInfo,
            'arrShop' => $arrShop,
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

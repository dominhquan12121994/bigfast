<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Api;

use Auth;
use Session;
use Redirect;
use Validator;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AbstractApiController;

use App\Modules\Orders\Models\Services\CashFlowServices;
use App\Modules\Orders\Models\Services\ShopReconcileServices;

use App\Modules\Shops\Resources\CashFlowResource;
use App\Modules\Shops\Resources\ReconcileHistoryResource;

class CashFlowController extends AbstractApiController
{
    protected $_cashFlowServices;
    protected $_shopReconcileServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ShopReconcileServices $shopReconcileServices,
                                CashFlowServices $cashFlowServices)
    {
        parent::__construct();

        $this->_cashFlowServices = $cashFlowServices;
        $this->_shopReconcileServices = $shopReconcileServices;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shopId = auth()->id();

        $arrDataCash = collect(array());
        $calculatorDate = date('Y-m-d');

        $dataCash = $this->_cashFlowServices->calculator(array(
            'calculatorDate' => $calculatorDate,
            'idOnce' => array($shopId)
        ));

        if ($dataCash->status) {
            $arrDataCash = collect($dataCash->result);
        }

        $beginDate = (int)date('Ymd', strtotime('-7 days'));
        $endDate = (int)date('Ymd');

        $payload = array(
            'shop_id' => $shopId,
            'date_range' => array(
                $beginDate,
                $endDate
            )
        );

        $shopReconcile = $this->_shopReconcileServices->getReconcile($payload);

        $resourcePayload = array(
            'cash-flow' => $arrDataCash,
            'reconcile-history' => $shopReconcile
        );

        return $this->_responseSuccess('Success', new CashFlowResource($resourcePayload));
    }

    public function reconcileHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'begin' => 'required|date',
            'end' => 'required|date|after_or_equal:begin',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $maxDateRange = 31;
        $begin = $request->input('begin');
        $end = $request->input('end');
        if (date_diff(date_create($begin), date_create($end))->days > $maxDateRange) {
            return $this->_responseError(array(
                'Phạm vi tra cứu tối đa ' . $maxDateRange . ' ngày'
            ));
        }

        $shopId = auth()->id();

        $beginDate = (int)date('Ymd', strtotime($request->input('begin')));
        $endDate = (int)date('Ymd', strtotime($request->input('end')));

        $payload = array(
            'shop_id' => $shopId,
            'date_range' => array(
                $beginDate,
                $endDate
            )
        );

        $shopReconcile = $this->_shopReconcileServices->getReconcile($payload);

        return $this->_responseSuccess('Success', new ReconcileHistoryResource($shopReconcile));
    }
}

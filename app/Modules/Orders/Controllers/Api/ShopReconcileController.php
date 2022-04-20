<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Api;

use Auth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

use App\Http\Controllers\Api\AbstractApiController;

use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;

use App\Modules\Orders\Models\Services\CalculatorFeeServices;

use App\Modules\Orders\Models\Services\ShopReconcileServices;

use App\Modules\Orders\Models\Services\CashFlowServices;
use App\Modules\Systems\Events\CreateLogEvents;

class ShopReconcileController extends AbstractApiController
{

    protected $_calculatorFeeServices;
    protected $_shopReconcileServices;
    protected $_shopAddressInterface;
    protected $_cashFlowServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CalculatorFeeServices $calculatorFeeServices,
                                ShopReconcileServices $shopReconcileServices,
                                ShopAddressInterface $shopAddressInterface,
                                CashFlowServices $cashFlowServices)
    {
        parent::__construct();

        $this->_calculatorFeeServices = $calculatorFeeServices;
        $this->_shopReconcileServices = $shopReconcileServices;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_cashFlowServices = $cashFlowServices;
    }

    public function doReconcile(Request $request)
    {
        if ( !$request->user()->can('action_cash_flow_check') ) {
            return $this->_responseError('Bạn không có quyền đối soát');
        }

        $validator = Validator::make($request->all(), [
            'userReconcile' => 'required',
            'dateReconcile' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }
        
        $userReconcile = $request->userReconcile;
        $calculatorDate = date('Y-m-d', strtotime($request->dateReconcile));
        $payload['userReconcile'] = $userReconcile;
        $payload['calculatorDate'] = $calculatorDate;

        $arrShop = $request->arrShop;
        foreach ($arrShop as $index => $value) {
            $payload['ids'][$index] = $value;
        }

        $dataCash = $this->_cashFlowServices->calculator($payload);

        foreach ($dataCash->result as $key => $value) {
            $shopSelected[$key]['begin_date'] = $value->shop_bank->date_reconcile;
            $shopSelected[$key]['end_date'] = $calculatorDate;
            $shopSelected[$key]['shop_id'] = $value->shop->id;
            $shopSelected[$key]['total_fee'] = $value->total_fee;
            $shopSelected[$key]['total_cod'] = $value->total_cod;
            $shopSelected[$key]['money_indemnify'] = $value->money_indemnify;
            $shopSelected[$key]['total_du'] = $value->total_du;
            $shopSelected[$key]['user_reconcile'] = $userReconcile;
        }

        $didReconcile = $this->_shopReconcileServices->storeReconcile($shopSelected);

        //Thêm dữ liệu log
        $log_data[] = [
            'model' => $this->_shopReconcileServices,
        ];
        //Lưu log 
        event(new CreateLogEvents($log_data, 'cash_flow', 'cash_flow_check'));

        return $didReconcile;
    }
}

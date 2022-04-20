<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Web;

use Auth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Redirect;

use App\Http\Controllers\Web\AbstractWebController;

use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;

use App\Modules\Orders\Models\Services\CalculatorFeeServices;

use App\Modules\Orders\Models\Services\ShopReconcileServices;

use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;

use App\Modules\Orders\Models\Services\CashFlowServices;

class ShopReconcileController extends AbstractWebController
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
                                CashFlowServices $cashFlowServices,
                                ShopsInterface $shopsInterface)
    {
        parent::__construct();

        $this->_calculatorFeeServices = $calculatorFeeServices;
        $this->_shopReconcileServices = $shopReconcileServices;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_cashFlowServices = $cashFlowServices;
        $this->_shopsInterface = $shopsInterface;
    }

    public function getReconcile(Request $request)
    {
        $you = auth('shop')->user();

        $shopId = $you->id;

        $shopInfo = $this->_shopsInterface->getById($shopId);

        $payload = array(
            'shop_id' => $shopId,
        );

        $arrShop = $this->_shopReconcileServices->getReconcile($payload);

        return view('Shops::reconcile-history.index', array(
            'shopInfo' => $shopInfo,
            'arrShop' => $arrShop
        ));
    }
}

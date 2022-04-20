<?php

/**
 * Class Controller
 * @package App\Modules\Orders\Controllers\Web
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\AbstractAdminController;

use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingFeeInsuranceInterface;
use App\Modules\Systems\Events\CreateLogEvents;

class OrderFeeInsuranceController extends AbstractAdminController
{
    protected $_orderSettingFeeInsuranceInterface;

    public function __construct(OrderSettingFeeInsuranceInterface $orderSettingFeeInsuranceInterface)
    {
        parent::__construct();
        
        $this->_orderSettingFeeInsuranceInterface = $orderSettingFeeInsuranceInterface;
    }

    public function index() 
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_settings_insurance_view'))
        {
            abort(403);
        }

        $cods = $this->_orderSettingFeeInsuranceInterface->getMore();

        return view('Orders::settings.feeInsurance.list', [ 'cods'=> $cods ]);
    }

    public function destroy(Request $request, $id) 
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_settings_insurance_delete'))
        {
            abort(403);
        }

        $cod = $this->_orderSettingFeeInsuranceInterface->getById($id);
        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $cod,
        ];

        //Lưu log 
        event(new CreateLogEvents($log_data, 'settings_insurance', 'order_settings_insurance_delete'));
        
        if ($cod) {
            $cod->delete();
        }

        \Func::setToast('Thành công', 'Xoá thành công', 'notice');
        return redirect()->route('admin.fee-insurance.index');
    }
}

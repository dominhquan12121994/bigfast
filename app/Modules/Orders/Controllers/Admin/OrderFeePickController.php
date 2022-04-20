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

use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingFeePickInterface;
use App\Modules\Systems\Events\CreateLogEvents;

class OrderFeePickController extends AbstractAdminController
{
    protected $_orderSettingFeePickInterface;

    public function __construct(OrderSettingFeePickInterface $orderSettingFeePickInterface)
    {
        parent::__construct();
        
        $this->_orderSettingFeePickInterface = $orderSettingFeePickInterface;
    }

    public function index() 
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_settings_pick_view'))
        {
            abort(403);
        }

        $cods = $this->_orderSettingFeePickInterface->getMore();

        return view('Orders::settings.feePick.list', [ 'cods'=> $cods ]);
    }

    public function destroy(Request $request, $id) 
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_settings_pick_delete'))
        {
            abort(403);
        }

        $cod = $this->_orderSettingFeePickInterface->getById($id);
        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $cod,
        ];

        //Lưu log 
        event(new CreateLogEvents($log_data, 'settings_pick', 'order_settings_pick_delete'));
        
        if ($cod) {
            $cod->delete();
        }

        \Func::setToast('Thành công', 'Xoá thành công', 'notice');
        return redirect()->route('admin.fee-pick.index');
    }
}

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

use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingCodInterface;
use App\Modules\Systems\Events\CreateLogEvents;

class OrderCodController extends AbstractAdminController
{
    protected $_orderSettingCodInterface;

    public function __construct(OrderSettingCodInterface $orderSettingCodInterface)
    {
        parent::__construct();
        
        $this->_orderSettingCodInterface = $orderSettingCodInterface;
    }

    public function index() 
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_settings_cod_view'))
        {
            abort(403);
        }

        $cods = $this->_orderSettingCodInterface->getMore();

        return view('Orders::settings.cod.list', [ 'cods'=> $cods ]);
    }

    public function destroy(Request $request, $id) 
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_settings_cod_delete'))
        {
            abort(403);
        }

        $cod = $this->_orderSettingCodInterface->getById($id);
        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $cod,
        ];

        //Lưu log 
        event(new CreateLogEvents($log_data, 'settings_cod', 'order_settings_cod_delete'));

        if ($cod) {
            $cod->delete();
        }

        \Func::setToast('Thành công', 'Xoá thành công', 'notice');
        return redirect()->route('admin.cod.index');
    }
}

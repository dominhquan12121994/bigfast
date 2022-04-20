<?php

/**
 * Class Controller
 * @package App\Modules\Orders\Controllers\Web
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;
use App\Rules\ExceptSpecialCharRule;

use App\Http\Controllers\Admin\AbstractAdminController;

use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;

use App\Modules\Orders\Constants\FreeConstant;
use App\Modules\Systems\Events\CreateLogEvents;

class OrderServiceController extends AbstractAdminController
{
    protected $_orderServiceInterface;

    public function __construct(OrderServiceInterface $orderServiceInterface)
    {
        parent::__construct();

        $this->_orderServiceInterface = $orderServiceInterface;
    }

    public function index()
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_services_view'))
        {
            abort(403);
        }

        $paginate = 10;
        $parents = $this->_orderServiceInterface->getMore(array(), array(), $paginate);

        return view('Orders::services.index', ['parents' => $parents]);
    }

    public function store(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_services_create'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name'  => array('required', 'unique:order_services,name,NULL,id,deleted_at,NULL', new ExceptSpecialCharRule()),
            'alias' => array('required', 'unique:order_services,alias,NULL,id,deleted_at,NULL', new ExceptSpecialCharRule()),
            'description' => array('nullable', new ExceptSpecialCharRule()),
        ], [
            'required'  => ':attribute là bắt buộc!',
            'unique'    => ':attribute đã tồn tại!',
        ], [
            'name'  => 'Tên gói cước',
            'alias' => 'Tên định danh'
        ]);

        if ($validator->fails()) {
            \Func::setToast('Thất bại', 'Thêm mới gói cước thất bại', 'error');
            return redirect()->back()->withErrors($validator->errors())->with(['action'    => 'create']);
        }

        $data = $request->only('name');
        $data['status'] = 1;

        $orderService = $this->_orderServiceInterface->create($request->all());
        //Thêm dữ liệu log
        $log_data[] = [
            'model' => $orderService,
        ];

        //Lưu log 
        event(new CreateLogEvents($log_data, 'order_services', 'order_services_create'));

        \Func::setToast('Thành công', 'Thêm mới gói cước thành công', 'notice');
        return redirect()->route('admin.order-service.index');
    }

    public function update(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_services_update'))
        {
            abort(403);
        }

        $id = $request->id_edit;
        $validator = Validator::make($request->all(), [
            'name_edit'      => array('required', 'unique:order_services,name,'.$id.',id,deleted_at,NULL', new ExceptSpecialCharRule()),
            'alias_edit'      => array('required', 'unique:order_services,alias,'.$id.',id,deleted_at,NULL', new ExceptSpecialCharRule()),
            'description_edit' => array('nullable', new ExceptSpecialCharRule()),
        ], [
            'required'  => ':attribute là bắt buộc!',
            'unique'    => ':attribute đã tồn tại!',
        ], [
            'name_edit'     => 'Tên gói cước',
            'alias_edit'    => 'Tên định danh',
            'status_edit'   => 'Trạng thái'
        ]);

        if ($validator->fails()) {
            \Func::setToast('Thất bại', 'Sửa gói cước thật bại', 'error');
            return redirect()->back()->withErrors($validator->errors())->with(['action'    => 'edit']);
        }
        $fillData['name'] = $request->name_edit;
        $fillData['status'] = $request->status_edit;
        $fillData['description'] = $request->description_edit;
        $fillData['alias'] = $request->alias_edit;

        $orderService = $this->_orderServiceInterface->getById($id);
        $this->_orderServiceInterface->updateById($id, $fillData);
        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $orderService,
        ];

        //Lưu log 
        event(new CreateLogEvents($log_data, 'order_services', 'order_services_update'));
        
        \Func::setToast('Thành công', 'Sửa gói cước thành công !', 'notice');
        return redirect()->route('admin.order-service.index');
    }

    public function destroy(Request $request, $id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_services_delete'))
        {
            abort(403);
        }

        $shop = $this->_orderServiceInterface->getById($id);
        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $shop,
        ];
        //Lưu log 
        event(new CreateLogEvents($log_data, 'order_services', 'order_services_delete'));

        if ($shop) {
            $shop->delete();
        }

        \Func::setToast('Thành công', 'Xóa gói cước thành công!', 'notice');
        return redirect()->route('admin.order-service.index');
    }

}

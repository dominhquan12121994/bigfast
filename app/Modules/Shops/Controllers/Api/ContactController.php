<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Api;

use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AbstractApiController;

use App\Modules\Operators\Models\Repositories\Contracts\ContactsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsTypeInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;

use App\Modules\Shops\Resources\ContactResource;
use App\Modules\Operators\Models\Services\ContactsServices;
use App\Modules\Systems\Events\CreateLogEvents;

class ContactController extends AbstractApiController
{
    protected $_contactsInterface;
    protected $_contactsServices;
    protected $_contactsTypeInterface;
    protected $_ordersInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ContactsInterface $contactsInterface, 
                                ContactsTypeInterface $contactsTypeInterface,
                                ContactsServices $contactsServices,
                                OrdersInterface $ordersInterface)
    {
        parent::__construct();

        $this->_contactsInterface = $contactsInterface;
        $this->_contactsServices = $contactsServices;
        $this->_contactsTypeInterface = $contactsTypeInterface;
        $this->_ordersInterface = $ordersInterface;
    }

    public function getList(Request $request)
    {
        $shop = $request->user();

        $validator = Validator::make($request->all(), [
            'start_date' => 'date|before_or_equal:end_date',
            'end_date' => 'date|before:tomorrow',
            'status' => 'numeric',
            'page' => 'numeric'
        ], [
            'start_date.required' => 'Ngày bắt đầu bắt buộc',
            'end_date.required' => 'Ngày kết thúc bắt buộc',
            'date' => 'Sai định dạng ngày',
            'start_date.before_or_equal' => 'Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc',
            'end_date.before' => 'Ngày kết thúc phải nhỏ hơn hoặc bằng ngày hiện tại',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }
        
        $limit = 10;
        $endDate = date('Y-m-d');
        $beginDate = date('Y-m-d', strtotime('-14 day'));
        if ( $request->has('start_date') && $request->has('end_date') ) {
            $beginDate = date('Y-m-d', strtotime($request->input('start_date')) );
            $endDate = date('Y-m-d', strtotime($request->input('end_date')) );
        }
        $filter['created_range'] = array($beginDate, $endDate);
        $filter['shop_id'] = $shop->id;
        $filter['status'] = $request->input('status', null);
        $filter['lading_code'] = $request->input('lading_code', null);

        //Đk lấy dach sách còn lại
        $contacts = $this->_contactsInterface->customPaginate(
            $filter, 
            array(
                'with' => [
                    'typeContacts',
                    'assign',
                    'history',
                    'history.user'
                ], 
                'orderByMulti' => [
                    'contacts.expired',
                    'contacts.status',
                    'contacts.created_at',
                ], 
                'directionMulti' => [
                    'DESC',
                    'ASC',
                    'DESC',
                ]
            ),
            $limit
        );

        return $this->_responseSuccess('Success',  new ContactResource($contacts) ); 
    }

    public function store(Request $request)
    {
        $shop = $request->user();

        $validator = Validator::make($request->all(), [
            'lading_code'       => 'required|max:20',
            'detail'            => 'required|max:255',
            'file.*'            => 'mimes:jpg,jpeg,png,doc,xlsx,xls,docx|max:2048',
            'file'              => 'max:10',
            'contacts_type_id'  => 'integer',
        ], [
            'required' => ':attribute là bắt buộc!',
            'max' => ':attribute không được vượt quá :max ký tự!',
            'file.max' => ':attribute không được vượt quá 10 :attribute!',
            'file.*.max' => ':attribute không được vượt quá 2Mb!',
            'integer' => ':attribute phải là số!',
            'exists' => ':attribute phải tồn tại!',
            'mimes' => 'Định dạng :attribute là jpg,jpeg,png,doc,xlsx,xls,docx!'
        ], [
            'lading_code' => 'Mã đơn hàng',
            'detail' => 'Nội dung',
            'contacts_type_id' => 'Loại hỗ trợ',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        $order = $this->_ordersInterface->getOne(array('lading_code' => $request->input('lading_code')));

        if (!$order) {
            return $this->_responseError('Mã đơn hàng không tồn tại !');
        }
        
        if ($order->shop_id !== $shop->id) {
            return $this->_responseError('Đơn hàng không thuộc quản lý của shop !');
        }

        //Xử lý dữ liệu
        $data = $request->only(['lading_code', 'detail', 'file', 'contacts_type_id']);
        $data['shop_id'] = $shop->id;
        $data['order_id'] = $order->id;
        $data['status'] = 0;
        if ( array_key_exists('file', $data) ) {
            $data['file_path'] = $this->_contactsServices->handleUpload($data['file']);
        }
        $data['created_date'] = date('Y-m-d');
        $data['user_id'] = $shop->id;
        $data['type'] = 'shop';
        $data['last_update'] = json_encode(array(
            'id' => $data['user_id'],
            'type' => $data['type']
        ));

        $type = $this->_contactsTypeInterface->getOne(array('id' => $request->contacts_type_id));
        if ($type->sla) {
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime('+'.$type->sla.' minutes'));
        }
        $contacts = $this->_contactsInterface->create($data);

        return $this->_responseSuccess('Tạo mới yêu cầu thành công !');
    }

    public function getDownload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'numeric',
            'position' => 'numeric',
        ]);

        $id = $request->input('id', 0);
        $position = $request->input('position', 0);

        $contacts = $this->_contactsInterface->getById($id);
        if (!$contacts) {
            return $this->_responseError('Error', 'Hỗ trợ không tồn tại !' ); 
        }
        $file = null;
        if ($contacts) {
            $file_path = array_diff(explode(";", $contacts->file_path),array(""));
            $file = $file_path[$position];
        }
        if (!$file) {
            return $this->_responseError('Error', 'File tồn tại !' ); 
        }
        $public_path = public_path($file);
        
        return response()->download($public_path);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ],[
            'id.required' => 'Id yêu cầu hỗ trợ là bắt buộc',
            'id.numeric' => 'Id yêu cầu hỗ trợ phải là số'
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        $shop = $request->user();
        $last_update = json_encode(array(
            'id' => $shop->id,
            'type' => 'shop'
        ));

        $id = $request->id;

        $contacts = $this->_contactsInterface->getById((int)$id);
        if ($contacts) {
            if ( $contacts->shop_id != $shop->id) {
                return $this->_responseError('Yêu cầu hỗ trợ này không thuộc Shop quản lý !');
            }
            $contacts->last_update = $last_update;
            $contacts->save();
            $contacts->delete();
        } else {
            return $this->_responseError('Không tồn tại yêu cầu hỗ trợ !');
        }

        //Lưu log 
        event(new CreateLogEvents([], 'contacts', 'contacts_delete'));
                
        return $this->_responseSuccess('Xóa yêu cầu thành công !');
    }
}
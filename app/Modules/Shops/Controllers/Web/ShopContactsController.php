<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Shops\Controllers\Web;

use Auth;
use Validator;
use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Web\AbstractWebController;
use App\Rules\ExceptSpecialCharRule;

use App\Modules\Operators\Models\Repositories\Contracts\ContactsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsTypeInterface;

use App\Modules\Operators\Models\Services\ContactsServices;

use App\Modules\Operators\Constants\ContactsConstant;

use App\Modules\Systems\Models\Entities\User;
use App\Modules\Systems\Events\CreateLogEvents;

class ShopContactsController extends AbstractWebController
{

    protected $_contactsServices;
    protected $_contactsInterface;
    protected $_contactsTypeInterface;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ContactsInterface $contactsInterface,
                                ContactsTypeInterface $contactsTypeInterface,
                                ContactsServices $contactsServices)
    {
        parent::__construct();
        $this->_contactsInterface = $contactsInterface;
        $this->_contactsServices = $contactsServices;
        $this->_contactsTypeInterface = $contactsTypeInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $you = auth('shop')->user();
        $id = $you->id;

        $paginate = 10;

        //Xử lý search
        $filter = $request->only([
            'assign_id',
            'contacts_type_id',
            'status',
            'lading_code',
            'filter_daterange'
        ]);
        $filter['shop'] = $id;
        $endDate = date('Y-m-d');
        $beginDate = date('Y-m-d', strtotime('-14 day'));
        if (isset( $filter['filter_daterange'] )) {
            $getDate = explode(' - ', $filter['filter_daterange']);
            $beginDate = date('Y-m-d', strtotime($getDate[0]));
            $endDate = date('Y-m-d', strtotime($getDate[1]));
        }
        $filter['created_range'] = array($beginDate, $endDate);
        //Đk lấy dach sách còn lại
        $contacts = $this->_contactsInterface->getMore(
            $filter,
            array(
                'with' => [
                    'typeContacts',
                    'orderShop',
                    'user',
                    'assign'
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
            $paginate
        );
        //Đk lấy danh sách quá hạn
        $filter['null'] = 'done_at';
        $filter['not_null'] = 'contacts_type.sla';
        $filter['expired_at'] = Carbon::now()->toDateTimeString();

        $listContact = $this->_contactsTypeInterface->getMore();

        return view('Shops::contacts.list',
            [
                'contacts'      => $contacts,
                'level'         => ContactsConstant::level,
                'listContact'   => $listContact,
                'status'        => ContactsConstant::status,
                'filter'        => $filter,
                'shop'          => $you
            ]
        );
    }

    public function store(Request $request)
    {
        $you = auth('shop')->user();
        $id = $you->id;

        $validator = Validator::make($request->all(), [
            'lading_code'       => 'required|max:255|exists:orders,lading_code',
            'shop'              => 'required|max:255',
            'detail'            => array('required', 'max:255', new ExceptSpecialCharRule()),
            'file.*'            => 'mimes:jpg,jpeg,bmp,png,doc,csv,xlsx,xls,docx,ppt,odt,ods,odp|max:2048',
            'file'              => 'max:10',
            'shop_id'           => 'required|integer',
            'order_id'          => 'required|integer',
            'contacts_type_id'  => 'integer',
            'route'             => 'required'
        ], [
            'required' => ':attribute là bắt buộc!',
            'max' => ':attribute không được vượt quá :max ký tự!',
            'file.max' => ':attribute không được vượt quá 10 :attribute!',
            'file.*.max' => ':attribute không được vượt quá 2Mb!',
            'integer' => ':attribute phải là số!',
            'exists' => ':attribute phải tồn tại!',
            'mimes' => 'Định dạng :attribute phải là jpg,jpeg,bmp,png,doc,csv,xlsx,xls,docx,ppt,odt,ods,odp!'
        ], [
            'lading_code' => 'Mã đơn hàng',
            'shop' => 'Cửa hàng',
            'detail' => 'Nội dung',
            'shop_id' => 'Id cửa hàng',
            'order_id' => 'Id đơn hàng',
            'contacts_type_id' => 'Loại hỗ trợ',
            'route' => 'Đường dẫn',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        //Xử lý dữ liệu
        $data = $request->all();
        if ( !array_key_exists('status', $data) ) {
            $data['status'] = 0;
        }
        if ( !array_key_exists('level', $data) ) {
            $data['level'] = 0;
        }
        if ( array_key_exists('file', $data) ) {
            $data['file_path'] = $this->_contactsServices->handleUpload($data['file']);
        }
        $data['created_date'] = date('Y-m-d');
        $data['user_id'] = $id;
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

        //Thêm dữ liệu log
        $log_data[] = [
            'model' => $contacts,
        ];
        //Lưu log 
        event(new CreateLogEvents($log_data, 'contacts', 'contacts_create'));

        \Func::setToast('Thành công', 'Thêm mới thành công hỗ trợ', 'notice');

        return redirect()->route($request->route);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $you = auth('shop')->user();

        $contacts = $this->_contactsInterface->getOne(array( 'id' => $id ));
        $listContact = $this->_contactsTypeInterface->getMore();

        $file_path = array_diff(explode(";", $contacts->file_path),array(""));

        return view('Shops::contacts.edit', 
            [
                'listContact'   => $listContact,
                'users'         => User::all(),
                'level'         => ContactsConstant::level,
                'status'        => ContactsConstant::status,
                'contacts'      => $contacts,
                'file_path'     => $file_path
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $you = auth('shop')->user();
        $shop_id = $you->id;

        $validator = Validator::make($request->all(), [
            'contacts_type_id'  => 'integer',
            'detail'            => array('required', 'max:255', new ExceptSpecialCharRule()),
            'file.*'            => 'mimes:jpg,jpeg,bmp,png,doc,csv,xlsx,xls,docx,ppt,odt,ods,odp|max:2048',
            'file'              => 'max:10',
            'status'            => 'integer',
            'assign_id'         => 'integer'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        //Xử lý dữ liệu
        $data = $request->all();
        $listFile = [];
        if ( array_key_exists('fileedit', $data) ) {
            foreach ( $data['fileedit'] as $fileedit ) {
                $listFile[] = $fileedit;
            }
        }
        if ( array_key_exists('file', $data) ) {
            $data['file_path'] = $this->_contactsServices->handleUpload($data['file'], $listFile);
        }
        $data['last_update'] = json_encode(array(
            'id' => $shop_id,
            'type' => 'shop'
        ));
        //Lưu thời gian hết hạn
        if ( $data['status'] == 2 ) {
            $data['done_at'] = now();
        }

        $old_data = $this->_contactsInterface->getById((int)$id);
        $this->_contactsInterface->updateById((int)$id, $data);
        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $old_data,
        ];

        //Lưu lý do từ chối
        if ( $request->status == 3) {
            $data['user_id'] = $shop_id;
            $data['contact_id'] = $id;
            $data['reason'] = $request['reason'];
            $contactsRefuse = $this->_contactsRefuseInterface->create($data);
            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $contactsRefuse,
            ];
        }
        \Func::setToast('Thành công', 'Cập nhật thành công hỗ trợ', 'notice');

        //Lưu log 
        event(new CreateLogEvents($log_data, 'contacts', 'contacts_update'));

        return redirect()->route('shop.contacts.index');
    }

    public function getDownload($id = 0, $position = 0)
    {
        $contacts = $this->_contactsInterface->getById($id);
        $file = '';
        if ($contacts) {
            $file_path = array_diff(explode(";", $contacts->file_path),array(""));
            $file = $file_path[$position];
        }
        $public_path = public_path($file);

        return response()->download($public_path);
    }

    public function checkAllContacts(Request $request)
    {
        $you = auth('shop')->user();
        $shop_id = $you->id;

        $data = $request->only(['userChecked', 'idChecked']);
        $condition = explode(',', $data['idChecked']);
        $filldata['assign_id'] = $data['userChecked'];
        $filldata['last_update'] = json_encode(array(
            'id' => $shop_id,
            'type' => 'shop'
        ));

        foreach ( $condition as $value) {
            $this->_contactsInterface->updateById($value, $filldata);
        }

        return redirect()->route('shop.contacts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $contacts = $this->_contactsInterface->getById($id);
        if ($contacts) {
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $contacts,
            ];
            //Lưu log 
            event(new CreateLogEvents($log_data, 'contacts', 'contacts_delete'));
            \Func::setToast('Thành công', 'Xoá thành công hỗ trợ', 'notice');
            $contacts->delete();
        }
        return redirect()->route('shop.contacts.index');
    }

}

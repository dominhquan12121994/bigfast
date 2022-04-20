<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Controllers\Admin;

use Auth;
use Validator;
use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Rules\ExceptSpecialCharRule;
use App\Http\Controllers\Admin\AbstractAdminController;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsTypeInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsHistoryInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsRefuseInterface;
use App\Modules\Operators\Constants\ContactsConstant;
use App\Modules\Systems\Models\Entities\User;
use App\Modules\Operators\Models\Services\ContactsServices;
use App\Modules\Orders\Models\Services\ShopServices;
use App\Modules\Operators\Events\ContactsHistory;
use App\Modules\Operators\Models\Entities\Contacts;
use App\Modules\Systems\Events\CreateLogEvents;

class ContactsController extends AbstractAdminController
{

    protected $_contactsServices;
    protected $_shopServices;
    protected $_contactsInterface;
    protected $_contactsTypeInterface;
    protected $_contactsHistoryInterface;
    protected $_contactsRefuseInterface;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ContactsInterface $contactsInterface,
                                ContactsTypeInterface $contactsTypeInterface,
                                ContactsHistoryInterface $contactsHistoryInterface,
                                ContactsRefuseInterface $contactsRefuseInterface,
                                ShopServices $shopServices,
                                ContactsServices $contactsServices)
    {
        parent::__construct();
        $this->_contactsInterface = $contactsInterface;
        $this->_contactsServices = $contactsServices;
        $this->_contactsTypeInterface = $contactsTypeInterface;
        $this->_contactsHistoryInterface = $contactsHistoryInterface;
        $this->_shopServices = $shopServices;
        $this->_contactsRefuseInterface = $contactsRefuseInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_contacts_view'))
        {
            abort(403);
        }

        $paginate = 10;

        //Xử lý search
        $filter = $request->only([
            'shop',
            'assign_id',
            'contacts_type_id',
            'status',
            'lading_code',
            'filter_daterange'
        ]);
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

        $listContact = $this->_contactsTypeInterface->getMore();
        $shop = null;
        if (isset( $filter['shop'] )) {
            $shop = $this->_shopServices->getById($filter['shop']);
        }

        return view('Operators::contacts.list',
            [
                'contacts'      => $contacts,
                'level'         => ContactsConstant::level,
                'listContact'   => $listContact,
                'status'        => ContactsConstant::status,
                'shop'          => $shop,
                'filter'        => $filter,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $you = auth('admin')->user();
        if (!$you->can('action_contacts_create'))
        {
            abort(403);
        }

        $contacts = $this->_contactsTypeInterface->getMore();

        return view('Operators::contacts.create',
            [
                'contacts'  => $contacts,
                'users'     => User::all(),
                'level'     => ContactsConstant::level
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_contacts_create'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'lading_code'       => 'required|max:255|exists:orders,lading_code',
            'shop'              => 'required|max:255',
            'detail'            => array('required', 'max:255', new ExceptSpecialCharRule()),
            'file.*'            => 'mimes:jpg,jpeg,png,doc,xlsx,xls,docx|max:2048',
            'file'              => 'max:10',
            'shop_id'           => 'required|integer',
            'order_id'          => 'required|integer',
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
            'shop' => 'Cửa hàng',
            'detail' => 'Nội dung',
            'shop_id' => 'Id cửa hàng',
            'order_id' => 'Id đơn hàng',
            'contacts_type_id' => 'Loại hỗ trợ',
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
        $data['user_id'] = Auth::guard()->id();
        $data['type'] = 'admin';
        $data['last_update'] = json_encode(array(
            'id' => $data['user_id'],
            'type' => $data['type']
        ));

        $type = $this->_contactsTypeInterface->getOne(array('id' => $request->contacts_type_id));
        if ($type->sla) {
            $data['expired_at'] = date('Y-m-d H:i:s', strtotime('+'.$type->sla.' minutes'));
        }
        $contacts = $this->_contactsInterface->create($data);
        \Func::setToast('Thành công', 'Thêm mới thành công hỗ trợ', 'notice');

        //Thêm dữ liệu log
        $log_data[] = [
            'model' => $contacts
        ];

        //Lưu log 
        event(new CreateLogEvents($log_data, 'contacts', 'contacts_create'));

        if ($request->route) {
            return redirect()->route($request->route);
        }
        return redirect()->route('admin.contacts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_contacts_view'))
        {
            abort(403);
        }

        $contacts = $this->_contactsInterface->getOne(array( 'id' => $id ));
        $listContact = $this->_contactsTypeInterface->getMore();

        $file_path = array_diff(explode(";", $contacts->file_path),array(""));

        return view('Operators::contacts.view', 
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
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_contacts_update'))
        {
            abort(403);
        }

        $contacts = $this->_contactsInterface->getOne(array( 'id' => $id ));
        $listContact = $this->_contactsTypeInterface->getMore();

        $file_path = array_diff(explode(";", $contacts->file_path),array(""));

        return view('Operators::contacts.edit', 
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
        $you = auth('admin')->user();
        if (!$you->can('action_contacts_update'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'contacts_type_id'  => 'integer',
            'detail'            => array('required', 'max:255', new ExceptSpecialCharRule()),
            'file.*'            => 'mimes:jpg,jpeg,bmp,png,doc,csv,xlsx,xls,docx,ppt,odt,ods,odp|max:20480',
            'file'              => 'max:10',
            'status'            => 'integer',
            'assign_id'         => 'integer'
        ], [
            'required' => ':attribute là bắt buộc!',
            'max' => ':attribute không được vượt quá :max ký tự!',
            'file.max' => ':attribute không được vượt quá 10 :attribute!',
            'file.*.max' => ':attribute không được vượt quá 2Mb!',
            'integer' => ':attribute phải là số!',
            'mimes' => 'Định dạng :attribute là jpg,jpeg,png,doc,xlsx,xls,docx!'
        ], [
            'detail' => 'Nội dung',
            'contacts_type_id' => 'Loại hỗ trợ',
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
            'id' => Auth::guard('admin')->id(),
            'type' => 'admin'
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
            $data['user_id'] = Auth::guard('admin')->id();
            $data['contact_id'] = $id;
            $data['reason'] = $request['reason'];
            $this->_contactsRefuseInterface->create($data);
        }
        \Func::setToast('Thành công', 'Cập nhật thành công hỗ trợ', 'notice');
        
        //Lưu log 
        event(new CreateLogEvents($log_data, 'contacts', 'contacts_update'));

        return redirect()->route('admin.contacts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_contacts_delete'))
        {
            abort(403);
        }

        $data['last_update'] = json_encode(array(
            'id' => Auth::id(),
            'type' => 'admin'
        ));

        $contacts = $this->_contactsInterface->updateById((int)$id, $data);
        if ($contacts) {
            \Func::setToast('Thành công', 'Xoá thành công hỗ trợ', 'notice');
            $contacts->delete();
        }

        //Lưu log 
        event(new CreateLogEvents([], 'contacts', 'contacts_delete'));
                
        return redirect()->route('admin.contacts.index');
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
        $you = auth('admin')->user();
        $log_data = [];

        $data = $request->only(['userChecked', 'idChecked']);
        $condition = explode(',', $data['idChecked']);
        $filldata['assign_id'] = $data['userChecked'];
        $filldata['last_update'] = json_encode(array(
            'id' => Auth::guard('admin')->id(),
            'type' => 'admin'
        ));

        foreach ( $condition as $value) {
            $old_data =  $this->_contactsInterface->getById($value);
            $this->_contactsInterface->updateById($value, $filldata);
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $old_data,
            ];
        }

        //Lưu log 
        event(new CreateLogEvents($log_data, 'contacts', 'contacts_update'));

        return redirect()->route('admin.contacts.index');
    }

    public function contactsHistory (Request $request, $id = 1)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_contacts_view'))
        {
            abort(403);
        }

        $history = $this->_contactsHistoryInterface->getMore(
            array('contacts_id' => $id),
            array('with' => 'user', 'orderBy' => 'created_at')
        );
        foreach ( $history as $value ) {
            $jsonDetail = json_decode($value->detail);
            $type = $value->type;
            if ($type == 'admin') {
                $value->name = 'User: '.$value->user->name;
            } else {
                $value->name = 'Shop: '.$value->shop->name;
            }
            $value->action = property_exists($jsonDetail, 'action') ? $jsonDetail->action : '';
            $value->column = property_exists($jsonDetail, 'column') ? $jsonDetail->column : '';
            $value->old = property_exists($jsonDetail, 'old') ? $jsonDetail->old : '';
            $value->new = property_exists($jsonDetail, 'new') ? $jsonDetail->new : '';
        }

        return view('Operators::contacts.history', [
            'history'   => $history,
            'level'     => ContactsConstant::level,
            'status'    => ContactsConstant::status,
        ]);
    }
}

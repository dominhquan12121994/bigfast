<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AbstractAdminController;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsTypeInterface;
use App\Modules\Operators\Constants\ContactsConstant;
use App\Modules\Operators\Models\Services\ContactsServices;
use App\Modules\Systems\Models\Entities\User;
use App\Modules\Systems\Events\CreateLogEvents;

class ContactsTypeController extends AbstractAdminController
{
    protected $_contactsTypeInterface;
    protected $_contactsServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ContactsTypeInterface $contactsTypeInterface, 
                                ContactsServices $contactsServices)
    {
        parent::__construct();
        $this->_contactsTypeInterface = $contactsTypeInterface;
        $this->_contactsServices = $contactsServices;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_contacts_type_view'))
        {
            abort(403);
        }

        $list = $this->_contactsTypeInterface->getMore();

        foreach ($list as $val) {
            $val->sla = $this->_contactsServices->convertMinToDay((int) $val->sla);
        }

        return view('Operators::contactstype.list', [ 
            'listContactType'   => ContactsConstant::constant_parent,
            'level'             => ContactsConstant::level,
            'list'              => $list
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_contacts_type_delete'))
        {
            abort(403);
        }

        $contacts = $this->_contactsTypeInterface->getById($id);
        if ($contacts) {
            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $contacts,
            ];
            //Lưu log 
            event(new CreateLogEvents($log_data, 'contacts_type', 'contacts_type_delete'));

            $contacts->delete();
        }
        return redirect()->route('admin.contacts-type.index');
    }
}

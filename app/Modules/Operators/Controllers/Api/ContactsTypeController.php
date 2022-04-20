<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use App\Rules\ExceptSpecialCharRule;
use App\Http\Controllers\Api\AbstractApiController;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsTypeInterface;
use App\Modules\Operators\Models\Services\ContactsServices;
use App\Modules\Operators\Requests\ContactsTypeRequest;
use App\Modules\Systems\Events\CreateLogEvents;

class ContactsTypeController extends AbstractApiController
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

    public function storeContactsType(ContactsTypeRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => array('required', new ExceptSpecialCharRule()),
            'sla' => 'required',
            'level' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $data = $request->only(['name', 'sla', 'level']);
        $data['parent_id'] = 0;

        $name = $data['name'];
        $parent_id = $data['parent_id'];

        if ( !$data['sla'] ) {
            $data['sla'] = 0;
        }

        $created = $this->_contactsTypeInterface->create($data);
        if($created) {
            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $created,
            ];

            //Lưu log 
            event(new CreateLogEvents($log_data, 'contacts_type', 'contacts_type_create'));

            $created->sla = $this->_contactsServices->convertMinToDay((int)$created->sla);
            return $this->_responseSuccess('Success', $created);
        }
        return $this->_responseError('Error');
    }

    public function findContactType(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $id = $request->id;
        $data = $this->_contactsTypeInterface->getById($id);

        if ($data) {
            return $this->_responseSuccess('Success', $data);
        }
        return $this->_responseError('Error');
    }

    public function updateContactType(ContactsTypeRequest $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'name' => array('required', new ExceptSpecialCharRule()),
            'sla' => 'required',
            'level' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $id = $request['id'];
        $data = $request->only(['name', 'sla', 'level']);

        $old_data = $this->_contactsTypeInterface->getById((int)$id);
        $update = $this->_contactsTypeInterface->updateById((int)$id, $data);
        if($update) {
            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $old_data,
            ];

            //Lưu log 
            event(new CreateLogEvents($log_data, 'contacts_type', 'contacts_type_update'));

            $update->sla = $this->_contactsServices->convertMinToDay((int)$update->sla);
            return $this->_responseSuccess('Success', $update);
        }

        return $this->_responseSuccess('Error', $update);
    }
}

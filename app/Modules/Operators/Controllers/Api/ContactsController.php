<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Controllers\Api;

use Throwable;
use Validator;
use View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AbstractApiController;

use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsHistoryInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsRefuseInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShipAssignedInterface;
use App\Modules\Systems\Services\NotificationServices;

use App\Modules\Orders\Models\Services\ShopServices;

use App\Modules\Orders\Resources\ShopsResource;

use App\Modules\Operators\Constants\ContactsConstant;

use App\Modules\Systems\Models\Entities\User;
use App\Modules\Systems\Events\CreateLogEvents;

class ContactsController extends AbstractApiController
{

    protected $_ordersInterface;
    protected $_shopServices;
    protected $_contactsInterface;
    protected $_contactsHistoryInterface;
    protected $_contactsRefuseInterface;
    protected $_orderShipAssignedInterface;
    protected $_notificationServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrdersInterface $ordersInterface,
                                ShopServices $shopServices,
                                ContactsHistoryInterface $contactsHistoryInterface,
                                ContactsRefuseInterface $contactsRefuseInterface,
                                OrderShipAssignedInterface $orderShipAssignedInterface,
                                NotificationServices $notificationServices,
                                ContactsInterface $contactsInterface)
    {
        parent::__construct();

        $this->_ordersInterface = $ordersInterface;
        $this->_shopServices = $shopServices;
        $this->_contactsInterface = $contactsInterface;
        $this->_contactsHistoryInterface = $contactsHistoryInterface;
        $this->_contactsRefuseInterface = $contactsRefuseInterface;
        $this->_orderShipAssignedInterface = $orderShipAssignedInterface;
        $this->_notificationServices = $notificationServices;
    }

    public function findShop(Request $request) {
        $validator = Validator::make($request->all(), [
            'lading_code' => 'required',
            'shop_id' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $fillter = [
            'lading_code' => $request->lading_code,
        ];
        if (isset($request->shop_id)) {
            $fillter['shop_id'] = $request->shop_id;
        }
        $orders = $this->_ordersInterface
            ->getOne( $fillter );

        if ($orders) {
            return $this->_responseSuccess('Success', [
                'name' => $orders->orderShop->name,
                'id' => $orders->orderShop->id,
                'order_id' => $orders->id
            ]);
        }

        return $this->_responseError('Error');
    }

    public function listShop(Request $request) {
        $data = [];
        $orders = $this->_shopServices->getSearch($request->search, 10);

        if ($orders) {
            return $this->_responseSuccess('Success', new ShopsResource($orders));
        }

        return $this->_responseError('Error');
    }

    public function changeStatus(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'status' => 'required|numeric',
            'user' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $id = $request['id'];
        $data = $request->only(['status']);
        $data['last_update'] = json_encode(array(
            'id' => $request['user'],
            'type' => 'admin',
        ));

        //Lưu thời gian hết hạn
        if ( $data['status'] == 2 ) {
            $data['done_at'] = now();
        }

        $old_data = $this->_contactsInterface->getById((int)$id);
        $update = $this->_contactsInterface->updateById((int)$id, $data);

        if ($update->contacts_type_id == 2 && $data['status'] == 2) {
            $orderId = $update->order_id;
            $orderShipAssign = $this->_orderShipAssignedInterface->getOne(array(
                'order_id' => $orderId
            ));
            if ($orderShipAssign) {
                $payloadNoti = array(
                    'sender_id' => auth()->id(),
                    'user_id' => $orderShipAssign->user_id,
                    'content_data' => array(
                        6, $update->lading_code
                    ),
                );
                $this->_notificationServices->sendToUser($payloadNoti);
            }
        }

        //Thêm dữ liệu log
        $log_data[] = [
            'old_data' => $old_data,
        ];

        //Lưu log
        event(new CreateLogEvents($log_data, 'contacts', 'contacts_update'));

        if ($update) {
            return $this->_responseSuccess('Success');
        }

        return $this->_responseError('Error');
    }

    public function refuseContact(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'status' => 'required|numeric',
            'user' => 'required|numeric',
            'reason' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        try {
            DB::beginTransaction();

            $id = $request['id'];
            $data = $request->only(['status']);
            $data['last_update'] = json_encode(array(
                'id' => $request['user'],
                'type' => 'admin',
            ));
            //Lấy người assign cho người từ chối
            $user_assign = $this->_contactsHistoryInterface->getOne(
                array('contacts_id' => $id ,'search' => '{"action":"update","column":"assign_id"'),
                array('orderBy' => 'id')
            );
            $assign_id = null;
            if ($user_assign) {
                $data['assign_id'] = $user_assign->user_id;
                $assign_id = $data['assign_id'];
            }

            $old_data = $this->_contactsInterface->getById((int)$id);
            $update = $this->_contactsInterface->updateById((int)$id, $data);
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $old_data,
            ];

            $assign = User::find($assign_id);
            $assign_name = $assign ? $assign->name : 'N/A';
            if ( $request->status == 3) {
                $data['user_id'] = $request['user'];
                $data['contact_id'] = $id;
                $data['reason'] = $request['reason'];
                $ontactsRefuse = $this->_contactsRefuseInterface->create($data);
                //Thêm dữ liệu log
                $log_data[] = [
                    'model' => $ontactsRefuse,
                ];
            }
            DB::commit();

            //Lưu log
            event(new CreateLogEvents($log_data, 'contacts', 'contacts_refuse'));

            return $this->_responseSuccess('Success', ['assign' => $assign_name]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->_responseError('Error', ['message' => $e->getMessage() ]);
        }
    }

    public function find(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $id = $request['id'];

        $data = $this->_contactsInterface->getById((int)$id);

        if ($data) {
            $data->ordershop;
            $data->typeContacts;
            $data->assign;
            $data->status = ContactsConstant::status[ $data->status];

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
                //Bổ sung lý do từ chối
                if ($value->column == 'status' && $value->new == 3) {
                    $refuses = $this->_contactsRefuseInterface->getMore(array('contact_id' => $value->contacts_id , 'user_id' => $value->user_id ));
                    if ($refuses)  {
                        $html = '';
                        foreach ($refuses as $kr => $r) {
                            $html .= '<p> Lý do: '.$r->reason.'</p>';
                        }
                        $value->refuses = $html;
                    }
                }
            }
            $data->html = View::make('Operators::contacts.shared.popup-history', [
                'history'   => $history,
                'level'     => ContactsConstant::level,
                'status'    => ContactsConstant::status,
            ])->render();

            return $this->_responseSuccess('Success', $data);
        }

        return $this->_responseError('Error');
    }
}

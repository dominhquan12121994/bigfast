<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Admin;

use Session;
use Redirect;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\AbstractAdminController;

/**  */
use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Operators\Models\Repositories\Contracts\WardsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShipAssignedInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderProductInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderFeeInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderExtraInterface;
use App\Modules\Orders\Models\Services\CalculatorFeeServices;
use App\Modules\Orders\Models\Services\OrderServices;

use App\Modules\Operators\Models\Entities\PostOffice;
use App\Modules\Orders\Models\Entities\Orders;
use App\Modules\Systems\Models\Entities\User;

class AssignShipController extends AbstractAdminController
{
    protected $_calculatorFee;
    protected $_orderServices;
    protected $_orderFeeInterface;
    protected $_orderShipAssignedInterface;
    protected $_orderProductInterface;
    protected $_provincesInterface;
    protected $_districtsInterface;
    protected $_wardsInterface;
    protected $_ordersInterface;
    protected $_orderExtraInterface;
    protected $_shopsInterface;
    protected $_shopAddressInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrdersInterface $ordersInterface,
                                CalculatorFeeServices $calculatorFee,
                                OrderServices $orderServices,
                                OrderFeeInterface $orderFeeInterface,
                                OrderExtraInterface $orderExtraInterface,
                                OrderShipAssignedInterface $orderShipAssignedInterface,
                                OrderProductInterface $orderProductInterface,
                                ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface,
                                ShopAddressInterface $shopAddressInterface,
                                ShopsInterface $shopsInterface,
                                WardsInterface $wardsInterface)
    {
        parent::__construct();

        $this->_orderServices = $orderServices;
        $this->_calculatorFee = $calculatorFee;
        $this->_ordersInterface = $ordersInterface;
        $this->_orderFeeInterface = $orderFeeInterface;
        $this->_orderExtraInterface = $orderExtraInterface;
        $this->_orderShipAssignedInterface = $orderShipAssignedInterface;
        $this->_orderProductInterface = $orderProductInterface;
        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
        $this->_wardsInterface = $wardsInterface;
        $this->_shopsInterface = $shopsInterface;
        $this->_shopAddressInterface = $shopAddressInterface;
    }

    public function scanBarcode(Request $request)
    {
        $order = null;
        $shopAddress = null;
        $products = array();
        $countProduct = 0;
        $shopAddressAll = array();
        $you = auth('admin')->user();
        $arrRoleAssign = array('pickup', 'refund', 'shipper');
        $type = $request->input('type', 'pickup');
        $user_selected = (int)$request->input('user', 0);
        $office_selected = (int)$request->input('office', 0);
        $lading_code = $request->input('lading_code', '');
        $check_barcode = $request->input('check_barcode', 0);

        if (!in_array($type, $arrRoleAssign)) {
            abort(403);
        }

        $postOffices = PostOffice::get();

        if ($type === 'shipper') {
            $users = User::role('shipper')->get();
        }
        if ($type === 'refund') {
            $users = User::role('refund')->get();
        }
        if ($type === 'pickup') {
            $users = User::role('pickup')->get();
        }

        if ($request->has('lading_code') && $user_selected) {
            if (strlen($lading_code) === 12 && substr($lading_code, 0, 1) === 'B') {
                $order = $this->_ordersInterface->getOne(
                    array(
                        'lading_code' => $request->input('lading_code')
                    ),
                    array('with' => array('servicetype', 'shop.getAddress'))
                );
            } else {
                $orderAgo = $this->_ordersInterface->getOne(array(
                    'created_range' => array(
                        (int)date('Ymd', strtotime('-7 day')),
                        (int)date('Ymd')
                    )
                ));
                if (!$orderAgo) {
                    return redirect()->back();
                }
                $orderExtra = $this->_orderExtraInterface->getOne(
                    array(
                        'client_code' => $request->input('lading_code'),
                        'id_min' => $orderAgo->id
                    )
                );
                if (!$orderExtra) {
                    Session::flash('message', 'Barcode không khả dụng');
                    Session::flash('alert-class', 'alert-danger');
                } else {
                    $order = $this->_ordersInterface->getById($orderExtra->id,
                        array('with' => array('servicetype', 'shop.getAddress'))
                    );
                }
            }

            if ($order) {
                try {
                    DB::beginTransaction();
                    //
                    $user = User::find($user_selected);
                    if (!$user) {
                        throw new \Exception('Nhân viên không khả dụng');
                    }
                    if ($type === 'shipper') {
                        if (!in_array($order->status_detail, array(22, 41, 34))) {
                            throw new \Exception('Trạng thái đơn hàng không khả dụng');
                        }

                        $payload = array(
                            'status_detail' => 23,
                            'orders' => array($order->id),
                            'select_shipper' => $user_selected,
                            'user_type' => 'user',
                            'user_id' => $you->id
                        );
                        $response = $this->_orderServices->assignShipperSend(array($order->id), $payload);
                    }
                    if ($type === 'refund') {
                        if (!in_array($order->status_detail, array(31, 36, 74))) {
                            throw new \Exception('Trạng thái đơn hàng không khả dụng');
                        }
                        $payload = array(
                            'status_detail' => 32,
                            'orders' => array($order->id),
                            'select_shipper' => $user_selected,
                            'user_type' => 'user',
                            'user_id' => $you->id
                        );
                        $response = $this->_orderServices->assignRefund(array($order->id), $payload);
                    }
                    if ($type === 'pickup') {
                        if (!in_array($order->status_detail, array(12))) {
                            throw new \Exception('Trạng thái đơn hàng không khả dụng');
                        }
                        $payload = array(
                            'status_detail' => 22,
                            'orders' => array($order->id),
                            'post_office' => $office_selected,
                            'warehouse_note' => '',
                            'select_user_receiver' => $you->id,
                            'user_type' => 'user',
                            'user_id' => $you->id
                        );
                        $response = $this->_orderServices->wareHouse(array($order->id), $payload);
                    }
                    //
                    DB::commit();

                    if ($response->result) {
                        $order = $response->orders[0];
                        Session::flash('message', 'Gán đơn thành công!');
                        Session::flash('alert-class', 'alert-info');
                    } else {
                        Session::flash('message', $response->error);
                        Session::flash('alert-class', 'alert-danger');
                    }
                } catch (\Throwable $e) {
                    DB::rollBack();
                    $message = $e->getMessage();
                    Session::flash('message', $message);
                    Session::flash('alert-class', 'alert-danger');
                }

                $products = $this->_orderProductInterface->getMore(array('order_id' => $order->id));
                $countProduct = (count($products) > 0) ? count($products) : 1;

                $shopAddressAll = $order->shop->getAddress;
                $shopAddress = (count($shopAddressAll) > 0) ? $shopAddressAll[0] : null;
            } else {
                Session::flash('message', 'Mã đơn không khả dụng');
                Session::flash('alert-class', 'alert-danger');
            }
        }

        return view('Orders::assign-ship.scan-barcode', array(
            'type' => $type,
            'order' => $order,
            'users' => $users,
            'products' => $products,
            'countProduct' => $countProduct,
            'shopAddress' => $shopAddress,
            'shopAddressAll' => $shopAddressAll,
            'user_selected' => $user_selected,
            'lading_code' => $lading_code,
            'postOffices' => $postOffices,
            'office_selected' => $office_selected,
        ));
    }

    public function show(Request $request)
    {
        $you = auth('admin')->user();
        $userId = $you->id;
        $userType = 'user';
        $shopDetail = null;
        if ($request->has('shop')) {
            $shopId = $request->shop;

            $shopDetail = $this->_shopsInterface->getById($shopId);

            if (!$shopDetail) {
                return abort(404);
            }
        }
        /*
         * Get data to view
         */
        if ($request->has('province') && $request->has('district')) {
            $provinces = $this->_provincesInterface->getMore();
            $districts = $this->_districtsInterface->getMore(array('p_id' => (int)$request->input('province')));
            $wards = $this->_wardsInterface->getMore(array('d_id' => (int)$request->input('district')));
        } else {
            $provinces = $this->_provincesInterface->getMore();
            $districts = $this->_districtsInterface->getMore(array('p_id' => 1));
            $wards = $this->_wardsInterface->getMore(array('d_id' => 1));
        }

        $arrRoleAssign = array('pickup' => 'pickup', 'refund' => 'refund', 'shipper' => 'shipper');
        $type = $request->input('type', 'pickup');

        if (!in_array($type, $arrRoleAssign)) {
            abort(403);
        }

        if ($type === 'pickup') {
            $filter['status'] = 1;
            $filter['status_detail'] = 11;
        }
        if ($type === 'shipper') {
            $filter['status'] = 2;
            $filter['status_detail'] = 22;
        }
        if ($type === 'refund') {
            $filter['status'] = 3;
            $filter['status_detail'] = array(31, 36);
        }

        $filter['pick_province_id'] = $request->input('province', 0);
        $filter['pick_district_id'] = $request->input('district', 0);
        $filter['pick_ward_id'] = $request->input('ward', 0);
        $arrData = array();

        if ($type === 'shipper') {
            $ordersList = Orders::with('receiver.provinces', 'receiver.districts', 'receiver.wards', 'shop');
            if ($shopDetail) {
                $ordersList = $ordersList->where('shop_id', $request->shop);
            }
            $ordersList = $ordersList->where('status', $filter['status'])
                ->where('status_detail', $filter['status_detail'])
                ->whereHas('receiver', function($query) use ($filter)  {
                    if ( $filter['pick_province_id'] != 0)  $query->where('p_id', '=', $filter['pick_province_id']);
                    if ( $filter['pick_district_id'] != 0)  $query->where('d_id', '=', $filter['pick_district_id']);
                    if ( $filter['pick_ward_id'] != 0)      $query->where('w_id', '=', $filter['pick_ward_id']);
                })
                ->get();

            $ordersList = $ordersList->groupBy(array('shop_id', 'receiver.w_id'));
            foreach ($ordersList as $shop_id => $orderByShop) {
                foreach ($orderByShop as $w_id => $orderByWard) {
                    $shop = $orderByWard[0]->shop;
                    $receiver = $orderByWard[0]->receiver;
                    $countOrder = count($orderByWard);
                    $orderIds = array();
                    foreach ($orderByWard as $order) {
                        $orderIds[] = $order->id;
                    }

                    $item = new \stdClass();
                    $item->shop = $shop;
                    $item->receiver = $receiver;
                    $item->orderIds = $orderIds;
                    $item->countOrder = $countOrder;
                    $item->totalWeight = $orderByWard->sum('weight');
                    $arrData[] = $item;
                }
            }
        } elseif ($type === 'refund') {
            $ordersList = Orders::with('refund.provinces', 'refund.districts', 'refund.wards', 'shop');
            if ($shopDetail) {
                $ordersList = $ordersList->where('shop_id', $request->shop);
            }
            $ordersList = $ordersList->where('status', $filter['status'])
                ->whereIn('status_detail', $filter['status_detail'])
                ->whereHas('refund', function($query) use ($filter)  {
                    if ( $filter['pick_province_id'] != 0)  $query->where('p_id', '=', $filter['pick_province_id']);
                    if ( $filter['pick_district_id'] != 0)  $query->where('d_id', '=', $filter['pick_district_id']);
                    if ( $filter['pick_ward_id'] != 0)      $query->where('w_id', '=', $filter['pick_ward_id']);
                })
                ->get();
            $ordersList = $ordersList->groupBy(array('shop_id', 'refund.w_id'));
            foreach ($ordersList as $shop_id => $orderByShop) {
                foreach ($orderByShop as $w_id => $orderByWard) {
                    $shop = $orderByWard[0]->shop;
                    $refund = $orderByWard[0]->refund;
                    $countOrder = count($orderByWard);
                    $orderIds = array();
                    foreach ($orderByWard as $order) {
                        $orderIds[] = $order->id;
                    }

                    $item = new \stdClass();
                    $item->shop = $shop;
                    $item->refund = $refund;
                    $item->orderIds = $orderIds;
                    $item->countOrder = $countOrder;
                    $item->totalWeight = $orderByWard->sum('weight');
                    $arrData[] = $item;
                }
            }
        } elseif ($type === 'pickup') {
            $ordersList = Orders::with('sender.provinces', 'sender.districts', 'sender.wards', 'shop');
            if ($shopDetail) {
                $ordersList = $ordersList->where('shop_id', $request->shop);
            }
            $ordersList = $ordersList->where('status', $filter['status'])
                ->where('status_detail', $filter['status_detail'])
                ->whereHas('sender', function($query) use ($filter)  {
                    if ( $filter['pick_province_id'] != 0)  $query->where('p_id', '=', $filter['pick_province_id']);
                    if ( $filter['pick_district_id'] != 0)  $query->where('d_id', '=', $filter['pick_district_id']);
                    if ( $filter['pick_ward_id'] != 0)      $query->where('w_id', '=', $filter['pick_ward_id']);
                })
                ->get();
            $ordersList = $ordersList->groupBy(array('shop_id', 'sender.w_id'));
            foreach ($ordersList as $shop_id => $orderByShop) {
                foreach ($orderByShop as $w_id => $orderByWard) {
                    $shop = $orderByWard[0]->shop;
                    $sender = $orderByWard[0]->sender;
                    $countOrder = count($orderByWard);
                    $orderIds = array();
                    foreach ($orderByWard as $order) {
                        $orderIds[] = $order->id;
                    }

                    $item = new \stdClass();
                    $item->shop = $shop;
                    $item->sender = $sender;
                    $item->orderIds = $orderIds;
                    $item->countOrder = $countOrder;
                    $item->totalWeight = $orderByWard->sum('weight');
                    $arrData[] = $item;
                }
            }
        }

        $arrData = collect($arrData);
        session()->flashInput($request->input());
        return view('Orders::assign-ship.show', array(
            'userType' => $userType,
            'userId' => $userId,
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards,
            'type' => $type,
            'arrData' => $arrData,
            'arrRoleAssign' => $arrRoleAssign,
            'shop' => $shopDetail
        ));
    }
}

<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Web;

use App\Modules\Orders\Constants\OrderConstant;
use Illuminate\Support\Facades\Auth;
use Validator;
use Exception;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use App\Modules\Systems\Models\Entities\User;

use App\Http\Controllers\Web\AbstractWebController;
use App\Modules\Orders\Constants\ShopConstant;
use App\Modules\Orders\Imports\OrdersImport;
use App\Modules\Orders\Imports\OrderSearchImport;
use App\Helpers\FileUpload;
use App\Rules\PhoneRule;
use App\Rules\ExceptSpecialCharRule;

/**  */
use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderLogInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderQueueInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderProductInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;


/**  */
use App\Modules\Operators\Models\Repositories\Contracts\WardsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsTypeInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderStatusOvertimeInterface;

use App\Modules\Orders\Models\Services\ShopServices;
use App\Modules\Orders\Models\Services\OrderServices;
use App\Modules\Orders\Models\Services\CalculatorFeeServices;
use App\Modules\Orders\Models\Services\OrderShipAssignedServices;
use App\Modules\Orders\Models\Services\OrderFeeServices;

use App\Modules\Orders\Exports\OrderSheet;
use Maatwebsite\Excel\Facades\Excel;

use App\Modules\Systems\Events\CreateLogEvents;

class OrdersController extends AbstractWebController
{
    protected $_orderProductInterface;
    protected $_orderServiceInterface;
    protected $_orderQueueInterface;
    protected $_orderLogInterface;
    protected $_ordersInterface;
    protected $_shopsInterface;
    protected $_shopBankInterface;
    protected $_shopAddressInterface;
    protected $_provincesInterface;
    protected $_districtsInterface;
    protected $_wardsInterface;
    protected $_shopServices;
    protected $_orderServices;
    protected $_contactsTypeInterface;
    protected $_calculatorFee;
    protected $_orderFeeServices;
    protected $_orderShipAssignedServices;
    protected $_orderStatusOvertimeInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrdersInterface $ordersInterface,
                                OrderLogInterface $orderLogInterface,
                                OrderQueueInterface $orderQueueInterface,
                                OrderServiceInterface $orderServiceInterface,
                                OrderProductInterface $orderProductInterface,
                                CalculatorFeeServices $calculatorFee,
                                ShopsInterface $shopsInterface,
                                ShopBankInterface $shopBankInterface,
                                ShopAddressInterface $shopAddressInterface,
                                ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface,
                                WardsInterface $wardsInterface,
                                ShopServices $shopServices,
                                ContactsTypeInterface $contactsTypeInterface,
                                OrderStatusOvertimeInterface $orderStatusOvertimeInterface,
                                OrderFeeServices $orderFeeServices,
                                OrderShipAssignedServices $orderShipAssignedServices,
                                OrderServices $orderServices)
    {
        parent::__construct();

        $this->_orderProductInterface = $orderProductInterface;
        $this->_orderServiceInterface = $orderServiceInterface;
        $this->_orderQueueInterface = $orderQueueInterface;
        $this->_orderLogInterface = $orderLogInterface;
        $this->_ordersInterface = $ordersInterface;
        $this->_calculatorFee = $calculatorFee;
        $this->_shopsInterface = $shopsInterface;
        $this->_shopBankInterface = $shopBankInterface;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
        $this->_wardsInterface = $wardsInterface;
        $this->_contactsTypeInterface = $contactsTypeInterface;
        $this->_shopServices = $shopServices;
        $this->_orderServices = $orderServices;
        $this->_orderFeeServices = $orderFeeServices;
        $this->_orderShipAssignedServices = $orderShipAssignedServices;
        $this->_orderStatusOvertimeInterface = $orderStatusOvertimeInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shop = auth('shop')->user();
        $arrShopSelected = array();
        $filter = $search = array();
        $staffsInfo = array();
        $staffType = null;
        $storeDay = null;
        $incurre_fee_list = array();

        $shopId = $shop->id;
        $filter['shop_id'] = $shopId;
        /*
         * Gen key draft orders by User
         */
        $keyRedis = ':shop:'.$shopId.':draft_order';
        $draftList = Redis::lrange($keyRedis, 0, -1);
        $keyRedisDraft = $keyRedis . '_' . (count($draftList) + 1);
        if (isset($_COOKIE[$keyRedisDraft])) {
            Redis::rpush($keyRedis, $_COOKIE[$keyRedisDraft]);
        }

        /*
         * Delete cookie draft, update cookie
         */
        $this->_orderServices->updateCookieDraft($keyRedis, $draftList);

        /*
         * Get draft data
         */
        $keyRedis = ':shop:'.$shopId.':draft_order';
        $draftList = Redis::lrange($keyRedis, 0, -1);
        $draftList = $this->_orderServices->filterDraft($draftList);
        $countDraft = count($draftList);

        /*
         * Count order by status
         */
        $countStatus = $this->_orderServices->countByStatus($shopId);

        $status = (int)$request->input('status', 1);
        $status_detail = (int)$request->input('status_detail', 0);
        $filter['status'] = $status;
        $filter['status_detail'] = $status_detail;

        $endDate = date('Ymd');
        $beginDate = date('Ymd', strtotime('-30 day'));

        if ($request->has('begin') && $request->has('end')) {
            $beginDate = date('Ymd', strtotime($request->input('begin')));
            $endDate = date('Ymd', strtotime($request->input('end')));
        }
        $filter['created_range'] = array((int)$beginDate, (int)$endDate);

        if ( in_array($status_detail, array(25, 34)) ) {
            $filter['created_store_range'] = array((int)$beginDate, (int)$endDate);
            if ($request->has('store_begin') && $request->has('store_end')) {
                $store_begin = $request->input('store_begin') ? date('Ymd', strtotime($request->input('store_begin'))) : 0;
                $store_end = $request->input('store_end') ? date('Ymd', strtotime($request->input('store_end'))) : 0;
                $search['created_store_range'] = array((int)$store_begin, (int)$store_end);
                if ($store_begin && $store_end) $filter['created_store_range'] = array((int)$store_begin, (int)$store_end);
            }
            //Lấy danh sách quá hạn lưu kho theo khoảng thời gian
            $orderOver = $this->_orderStatusOvertimeInterface->getMore([
                'status_detail' => $status_detail,
                'end_date_null' => true,
                'start_date' => $filter['created_store_range']
            ]);

            //Số ngày lưu kho
            $storeDay = $orderOver->mapWithKeys(function ($item) {
                return array(
                    $item->order_id => (int)ceil((time() - strtotime($item->start_date)) / 86400)
                );
            })->toArray();

            $filter['id'] = $orderOver->pluck('order_id')->toArray();
        }

        $search['code'] = $request->input('code', '');
        $search['status_detail'] = array();
        $search['created_range'] = array((int)$beginDate, (int)$endDate);
        $search['send_success_range'] = array(0, 0);
        $search['collect_money_range'] = array(0, 0);
        $search['reconcile_send_range'] = array(0, 0);
        $search['reconcile_refund_range'] = array(0, 0);

        if ($request->has('search')) {
            $status_arr = $request->input('status_arr', null);
            if ($status_arr) {
                $status_arr = explode(',', $status_arr);
                $search['status_detail'] = $status_arr;
            }
        }

        $send_success_from = 0;
        $send_success_to = 0;
        if ($request->has('send_success_from') && $request->has('send_success_to')) {
            $send_success_from = $request->input('send_success_from') ? date('Ymd', strtotime($request->input('send_success_from'))) : 0;
            $send_success_to = $request->input('send_success_to') ? date('Ymd', strtotime($request->input('send_success_to'))) : 0;
            $search['send_success_range'] = array((int)$send_success_from, (int)$send_success_to);
            if ($send_success_from && $send_success_to) $filter['send_success_range'] = array((int)$send_success_from, (int)$send_success_to);
        }
        if ($request->has('collect_money_from') && $request->has('collect_money_to')) {
            $collect_money_from = $request->input('collect_money_from') ? date('Ymd', strtotime($request->input('collect_money_from'))) : 0;
            $collect_money_to = $request->input('collect_money_to') ? date('Ymd', strtotime($request->input('collect_money_to'))) : 0;
            $search['collect_money_range'] = array((int)$collect_money_from, (int)$collect_money_to);
            if ($collect_money_from && $collect_money_to) $filter['collect_money_range'] = array((int)$collect_money_from, (int)$collect_money_to);
        }
        if ($request->has('reconcile_send_from') && $request->has('reconcile_send_to')) {
            $reconcile_send_from = $request->input('reconcile_send_from') ? date('Ymd', strtotime($request->input('reconcile_send_from'))) : 0;
            $reconcile_send_to = $request->input('reconcile_send_to') ? date('Ymd', strtotime($request->input('reconcile_send_to'))) : 0;
            $search['reconcile_send_range'] = array((int)$reconcile_send_from, (int)$reconcile_send_to);
            if ($reconcile_send_from && $reconcile_send_to) $filter['reconcile_send_range'] = array((int)$reconcile_send_from, (int)$reconcile_send_to);
        }
        if ($request->has('reconcile_refund_from') && $request->has('reconcile_refund_to')) {
            $reconcile_refund_from = $request->input('reconcile_refund_from') ? date('Ymd', strtotime($request->input('reconcile_refund_from'))) : 0;
            $reconcile_refund_to = $request->input('reconcile_refund_to') ? date('Ymd', strtotime($request->input('reconcile_refund_to'))) : 0;
            $search['reconcile_refund_range'] = array((int)$reconcile_refund_from, (int)$reconcile_refund_to);
            if ($reconcile_refund_from && $reconcile_refund_to) $filter['reconcile_refund_range'] = array((int)$reconcile_refund_from, (int)$reconcile_refund_to);
        }

        if (!isset(OrderConstant::status[$status])) {
            abort(404);
        }
        $arrStatusDetail = OrderConstant::status[$status]['detail'];

        /*
         * Get data
         */
        $conditions = $filter;
        $statusActive = $status;
        if ($request->has('search')) {
            unset($conditions['status']);
            $statusActive = -1;
        }
        if (!empty($search['status_detail'])) {
            if ($filter['status_detail']) {
                array_push($search['status_detail'], $filter['status_detail']);
            }
            if (count($search['status_detail']) > 0) {
                foreach ($search['status_detail'] as $key => $value) {
                    $search['status_detail'][$key] = (int)$value;
                }
            }
            $conditions['status_detail'] = $search['status_detail'];
        }
        if (!empty($search['code'])) {
            $arrCode = array();
            $arrCodeSearch = array();
            $strCode = trim($search['code']);
            if (strlen($strCode) > 12) {
                if (strpos($strCode, ';') > 0) {
                    $arrCode = explode(';', $strCode);
                } elseif (strpos($strCode, ',') > 0) {
                    $arrCode = explode(',', $strCode);
                } elseif (strpos($strCode, '|') > 0) {
                    $arrCode = explode('|', $strCode);
                } elseif (strpos($strCode, ' ') > 0) {
                    $arrCode = explode(' ', $strCode);
                }
                if (count($arrCode) > 0) {
                    foreach ($arrCode as $code) {
                        if (strlen(trim($code)) === 12)
                            $arrCodeSearch[] = trim($code);
                    }
                }
            } elseif (strlen($strCode) === 12) {
                $arrCodeSearch[] = $strCode;
            }
            $conditions['lading_code'] = array_slice($arrCodeSearch, 0, 100);
        }

        $checkSession = $request->session()->has('order-search-excel');
        if ($request->has('searchs') && $checkSession) {
            unset($conditions['status']);
            $search['searchs'] = 1;
            $statusActive = -1;
            $conditions = array();
            $arrCodeSearch = $request->session()->get('order-search-excel');
            $conditions['lading_code'] = $arrCodeSearch;
            if (!empty($filter['shop_id'])) $conditions['shop_id'] = $filter['shop_id'];
        } elseif ($checkSession) {
            $request->session()->forget('order-search-excel');
        }

        if ($status > 0) {
            if ($status === 5) {
                $conditions['status'] = array($status, 9);
                if ($status_detail === 51)
                    $conditions['status_detail'] = array($status_detail, 91);
            }

            $viewFile = 'Shops::orders.list';
            $limit = $request->input('limit', config('options.limit'));
            $ordersList = $this->_ordersInterface->getMore(
                $conditions,
                array(
                    'with' => array('shop', 'extra', 'sender.provinces', 'receiver.provinces'),
                    'orderBy' => array('status_detail', 'updated_at'),
                    'direction' => array('ASC')
                ), $limit);
            //Bổ sung thông tin staff(shipper, refund, pickup), tiền bồi hoàn
            if (!$request->has('search')) {
                if ($filter['status_detail'] === 12 ) $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'pickup');
                if ( in_array($filter['status_detail'], array(23, 34)) ) $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'shipper');
                if ( in_array($filter['status'], array(4, 5)) ) $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'shipper');
                if ($filter['status_detail'] === 32 ) $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'refund');
                if ( in_array($filter['status_detail'], array(83, 84, 73, 74) ) ) $incurre_fee_list[] = 'incurred_money_indemnify';
            }
            $filter['limit'] = $limit;
            $filter['status'] = $status;
            $filter['status_detail'] = $status_detail;
        } else {
//            unset($filter['status']);
            $viewFile = 'Shops::orders.queue';
            $limit = $request->input('limit', config('options.limit'));
            $ordersList = $this->_orderQueueInterface->getMore(
                $filter,
                array(
                    'with' => array('shop'),
                    'orderBy' => array('created_at')
                ), $limit);
            $filter['limit'] = $limit;
        }

        $fileFails = '';
        if ($request->session()->has('orders-fails-excel')) {
            $fileFails = $request->session()->get('orders-fails-excel');
        }

        $totalOrder = $ordersList->total();
        $listContact = $this->_contactsTypeInterface->getMore();

       //Phí vận chuyển phát sinh, Phí thu hộ phát sinh, Tiền thu hộ phát sinh
       $incurre_fee_list[] = 'incurred_fee_transport';
       $incurre_fee_list[] = 'incurred_fee_cod';
       $incurre_fee_list[] = 'incurred_total_cod';

       $incurred_fee = $this->_orderFeeServices->getFeeIncurred($ordersList, $incurre_fee_list);

        //Bổ sung thông tin staff(shipper, refund, pickup), tiền bồi hoàn
        $orderStatusDetail = $ordersList->map(function ($order) { return $order->status_detail; })->toArray();
        if ($request->has('search')) {
            if ( count($ordersList) > 0 && count(array_diff($orderStatusDetail, [12])) === 0 ) {
                $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'pickup');
                $staffType = 'Nhân viên lấy';
            }
            if ( count($ordersList) > 0 && count(array_diff($orderStatusDetail, [32])) === 0 ) {
                $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'refund');
                $staffType = 'Nhân viên hoàn';
            }
            if ($send_success_from && $send_success_to) {
                $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'shipper');
                $staffType = 'Nhân viên giao';
            } else {
                if ( count($ordersList) > 0 && count(array_diff($orderStatusDetail, [23, 41, 51, 34])) === 0 ) {
                    $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'shipper');
                    $staffType = 'Nhân viên giao';
                }
            }
        }
        return view($viewFile, [
            'shop' => $shop,
            'filter' => $filter,
            'search' => $search,
            'orders' => $ordersList,
            'statusActive' => $statusActive,
            'countDraft' => $countDraft,
            'countStatus' => $countStatus,
            'arrShopSelected' => $arrShopSelected,
            'arrStatusDetail' => $arrStatusDetail,
            'users'         => User::all(),
            'listContact'   => $listContact,
            'fileFails' => $fileFails,
            'total' => $totalOrder,
            'staffsInfo' => $staffsInfo,
            'staffType' => $staffType,
            'incurred_fee' => $incurred_fee,
            'storeDay' => $storeDay
        ]);
    }

    public function staff(Request $request) {
        $staff = auth('shopStaff')->user();
        $arrShopSelected = array();
        $filter = $search = array();
        $staffsInfo = array();
        $incurre_fee_list = array();

        $shopId = $shop->id;
        $filter['shop_id'] = $shopId;
        /*
         * Gen key draft orders by User
         */
        $keyRedis = ':shop:'.$shopId.':draft_order';
        $draftList = Redis::lrange($keyRedis, 0, -1);
        $keyRedisDraft = $keyRedis . '_' . (count($draftList) + 1);
        if (isset($_COOKIE[$keyRedisDraft])) {
            Redis::rpush($keyRedis, $_COOKIE[$keyRedisDraft]);
        }

        /*
         * Delete cookie draft, update cookie
         */
        $this->_orderServices->updateCookieDraft($keyRedis, $draftList);

        /*
         * Get draft data
         */
        $keyRedis = ':shop:'.$shopId.':draft_order';
        $draftList = Redis::lrange($keyRedis, 0, -1);
        $draftList = $this->_orderServices->filterDraft($draftList);
        $countDraft = count($draftList);

        /*
         * Count order by status
         */
        $countStatus = $this->_orderServices->countByStatus($shopId);

        $status = (int)$request->input('status', 1);
        $status_detail = (int)$request->input('status_detail', 0);
        $filter['status'] = $status;
        $filter['status_detail'] = $status_detail;

        $endDate = date('Ymd');
        $beginDate = date('Ymd', strtotime('-14 day'));

        if ($request->has('begin') && $request->has('end')) {
            $beginDate = date('Ymd', strtotime($request->input('begin')));
            $endDate = date('Ymd', strtotime($request->input('end')));
        }
        $filter['created_range'] = array((int)$beginDate, (int)$endDate);

        $search['code'] = $request->input('code', '');
        $search['status_detail'] = array();
        $search['created_range'] = array((int)$beginDate, (int)$endDate);
        $search['send_success_range'] = array(0, 0);
        $search['collect_money_range'] = array(0, 0);
        $search['reconcile_send_range'] = array(0, 0);
        $search['reconcile_refund_range'] = array(0, 0);

        if ($request->has('search')) {
            $status_arr = $request->input('status_arr', null);
            if ($status_arr) {
                $status_arr = explode(',', $status_arr);
                $search['status_detail'] = $status_arr;
            }
        }

        if ($request->has('send_success_from') && $request->has('send_success_to')) {
            $send_success_from = $request->input('send_success_from') ? date('Ymd', strtotime($request->input('send_success_from'))) : 0;
            $send_success_to = $request->input('send_success_to') ? date('Ymd', strtotime($request->input('send_success_to'))) : 0;
            $search['send_success_range'] = array((int)$send_success_from, (int)$send_success_to);
            if ($send_success_from && $send_success_to) $filter['send_success_range'] = array((int)$send_success_from, (int)$send_success_to);
        }
        if ($request->has('collect_money_from') && $request->has('collect_money_to')) {
            $collect_money_from = $request->input('collect_money_from') ? date('Ymd', strtotime($request->input('collect_money_from'))) : 0;
            $collect_money_to = $request->input('collect_money_to') ? date('Ymd', strtotime($request->input('collect_money_to'))) : 0;
            $search['collect_money_range'] = array((int)$collect_money_from, (int)$collect_money_to);
            if ($collect_money_from && $collect_money_to) $filter['collect_money_range'] = array((int)$collect_money_from, (int)$collect_money_to);
        }
        if ($request->has('reconcile_send_from') && $request->has('reconcile_send_to')) {
            $reconcile_send_from = $request->input('reconcile_send_from') ? date('Ymd', strtotime($request->input('reconcile_send_from'))) : 0;
            $reconcile_send_to = $request->input('reconcile_send_to') ? date('Ymd', strtotime($request->input('reconcile_send_to'))) : 0;
            $search['reconcile_send_range'] = array((int)$reconcile_send_from, (int)$reconcile_send_to);
            if ($reconcile_send_from && $reconcile_send_to) $filter['reconcile_send_range'] = array((int)$reconcile_send_from, (int)$reconcile_send_to);
        }
        if ($request->has('reconcile_refund_from') && $request->has('reconcile_refund_to')) {
            $reconcile_refund_from = $request->input('reconcile_refund_from') ? date('Ymd', strtotime($request->input('reconcile_refund_from'))) : 0;
            $reconcile_refund_to = $request->input('reconcile_refund_to') ? date('Ymd', strtotime($request->input('reconcile_refund_to'))) : 0;
            $search['reconcile_refund_range'] = array((int)$reconcile_refund_from, (int)$reconcile_refund_to);
            if ($reconcile_refund_from && $reconcile_refund_to) $filter['reconcile_refund_range'] = array((int)$reconcile_refund_from, (int)$reconcile_refund_to);
        }

        if (!isset(OrderConstant::status[$status])) {
            abort(404);
        }
        $arrStatusDetail = OrderConstant::status[$status]['detail'];

        /*
         * Get data
         */
        $conditions = $filter;
        $statusActive = $status;
        if ($request->has('search')) {
            unset($conditions['status']);
            $statusActive = -1;
        }
        if (!empty($search['status_detail'])) {
            if ($filter['status_detail']) {
                array_push($search['status_detail'], $filter['status_detail']);
            }
            if (count($search['status_detail']) > 0) {
                foreach ($search['status_detail'] as $key => $value) {
                    $search['status_detail'][$key] = (int)$value;
                }
            }
            $conditions['status_detail'] = $search['status_detail'];
        }
        if (!empty($search['code'])) {
            $arrCode = array();
            $arrCodeSearch = array();
            $strCode = trim($search['code']);
            if (strlen($strCode) > 12) {
                if (strpos($strCode, ';') > 0) {
                    $arrCode = explode(';', $strCode);
                } elseif (strpos($strCode, ',') > 0) {
                    $arrCode = explode(',', $strCode);
                } elseif (strpos($strCode, '|') > 0) {
                    $arrCode = explode('|', $strCode);
                } elseif (strpos($strCode, ' ') > 0) {
                    $arrCode = explode(' ', $strCode);
                }
                if (count($arrCode) > 0) {
                    foreach ($arrCode as $code) {
                        if (strlen(trim($code)) === 12)
                            $arrCodeSearch[] = trim($code);
                    }
                }
            } elseif (strlen($strCode) === 12) {
                $arrCodeSearch[] = $strCode;
            }
            $conditions['lading_code'] = array_slice($arrCodeSearch, 0, 100);
        }

        $checkSession = $request->session()->has('order-search-excel');
        if ($request->has('searchs') && $checkSession) {
            unset($conditions['status']);
            $search['searchs'] = 1;
            $statusActive = -1;
            $conditions = array();
            $arrCodeSearch = $request->session()->get('order-search-excel');
            $conditions['lading_code'] = $arrCodeSearch;
            if (!empty($filter['shop_id'])) $conditions['shop_id'] = $filter['shop_id'];
        } elseif ($checkSession) {
            $request->session()->forget('order-search-excel');
        }

        if ($status > 0) {
            if ($status === 5) {
                $conditions['status'] = array($status, 9);
                if ($status_detail === 51)
                    $conditions['status_detail'] = array($status_detail, 91);
            }

            $viewFile = 'Shops::orders.list';
            $limit = $request->input('limit', config('options.limit'));
            $ordersList = $this->_ordersInterface->getMore(
                $conditions,
                array(
                    'with' => array('shop', 'extra', 'sender.provinces', 'receiver.provinces'),
                    'orderBy' => array('status_detail', 'updated_at'),
                    'direction' => array('ASC')
                ), $limit);
            //Bổ sung thông tin staff(shipper, refund, pickup), tiền bồi hoàn
            if ($filter['status_detail'] === 12 ) $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'pickup');
            if ( in_array($filter['status_detail'], array(23, 34)) ) $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'shipper');
            if ( in_array($filter['status'], array(4, 5)) ) $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'shipper');
            if ($filter['status_detail'] === 32 ) $this->_orderShipAssignedServices->getStaffInfo($staffsInfo, $ordersList, 'refund');
            if ( in_array($filter['status_detail'], array(83, 84, 73, 74) ) ) $incurre_fee_list[] = 'incurred_money_indemnify';
            $filter['limit'] = $limit;
            $filter['status'] = $status;
            $filter['status_detail'] = $status_detail;
        } else {
//            unset($filter['status']);
            $viewFile = 'Shops::orders.queue';
            $limit = $request->input('limit', config('options.limit'));
            $ordersList = $this->_orderQueueInterface->getMore(
                $filter,
                array(
                    'with' => array('shop'),
                    'orderBy' => array('created_at')
                ), $limit);
            $filter['limit'] = $limit;
        }

        $fileFails = '';
        if ($request->session()->has('orders-fails-excel')) {
            $fileFails = $request->session()->get('orders-fails-excel');
        }

        //Phí vận chuyển phát sinh, Phí thu hộ phát sinh, Tiền thu hộ phát sinh
        $incurre_fee_list[] = 'incurred_fee_transport';
        $incurre_fee_list[] = 'incurred_fee_cod';
        $incurre_fee_list[] = 'incurred_total_cod';

        $incurred_fee = $this->_orderFeeServices->getFeeIncurred($ordersList, $incurre_fee_list);

        $totalOrder = $ordersList->total();
        $listContact = $this->_contactsTypeInterface->getMore();

        return view($viewFile, [
            'shop' => $shop,
            'filter' => $filter,
            'search' => $search,
            'orders' => $ordersList,
            'statusActive' => $statusActive,
            'countDraft' => $countDraft,
            'countStatus' => $countStatus,
            'arrShopSelected' => $arrShopSelected,
            'arrStatusDetail' => $arrStatusDetail,
            'users'         => User::all(),
            'listContact'   => $listContact,
            'fileFails' => $fileFails,
            'total' => $totalOrder,
            'staffsInfo' => $staffsInfo,
            'incurred_fee' => $incurred_fee,
        ]);
    }

    /*
    * Display a listing of orders draft
    */
    public function drafts(Request $request)
    {
        $you = auth('shop')->user();
        $shopId = $you->id;

        $keyRedis = ':shop:'.$shopId.':draft_order';
        if ($request->has('rm')) {
            $draftList = Redis::lrange($keyRedis, 0, -1);
            $arrDraftDelete = explode(',', $request->input('rm'));
            foreach ($arrDraftDelete as $draftKey) {
                if ($draftList[$draftKey]) {
                    Redis::lset($keyRedis, $draftKey, null);
                    setcookie($keyRedis . '_' . ($draftKey + 1), null, -1, '/');
                    \Func::setToast('Thành công', 'Đã xoá đơn nháp', 'notice');
                }
            }
            return redirect()->route('shop.orders-drafts');
        }

        $draftList = Redis::lrange($keyRedis, 0, -1);
        $draftList = $this->_orderServices->filterDraft($draftList);
        $countDraft = count($draftList);

        $shop = $this->_shopsInterface->getById($shopId);
        $countStatus = $this->_orderServices->countByStatus($shopId);

        return view('Shops::orders.draft', [
            'shop' => $shop,
            'draftList' => $draftList,
            'countDraft' => $countDraft,
            'countStatus' => $countStatus,
            'statusActive' => -2
        ]);
    }

    public function export(Request $request)
    {
        $you = auth('shop')->user();
        $shop_id = $you->id;

        $validator = Validator::make($request->all(), [
            'orders' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back();
        }

        $arrData = array();
        $orderStr = $request->input('orders');
        $ordersId = explode(',', $orderStr);
        if (count($ordersId) > 0 && substr_count($orderStr,  ',') === count($ordersId)) {
            foreach ($ordersId as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if (!$order) {
                    return redirect()->back();
                }

                if ($order->shop_id !== $shop_id) {
                    return redirect()->back();
                }
            }
        }

        //Lưu log
        event(new CreateLogEvents([], 'orders', 'orders_export'));

        return Excel::download(new OrderSheet($ordersId, $this->_orderServices), 'orders.xlsx');
    }

    public function download(Request $request)
    {
        if ($request->session()->has('orders-fails-excel')) {
            $fileFails = $request->session()->get('orders-fails-excel');
            $request->session()->forget('orders-fails-excel');
            return Storage::download($fileFails);
        }
        return false;
    }

    public function import(Request $request)
    {
        $you = auth('shop')->user();
        $shop_id = $you->id;

        $file = $request->file('fileImport');
        if(!$file) return response('Không tìm thấy file', 400);

        $uploadOptions = array(
            'file_types' => 'orders',
            'file_extension' => array('xls', 'xlsx')
        );

        try {
            $fileUpload = FileUpload::doUpload($file, $uploadOptions);
            if($fileUpload['success']){
                $fileFails = time() . '-shop-'.$shop_id.'-orders-fails.xlsx';
                request()->merge(['file_path' => "/public/".$fileUpload['file_path']]);
                Excel::import(new OrdersImport($shop_id, 'shop', $shop_id, $fileFails), $fileUpload['file_path']);
            } else {
                \Func::setToast('Thất bại', $fileUpload['msg'], 'error');
            }
        } catch (\Exception $ex){
//            \Func::setToast('Thất bại', 'Upload file thất bại', 'error');
        }

        //Lưu log
        event(new CreateLogEvents([], 'orders', 'orders_import'));

        return redirect()->route('shop.orders.index');
    }

    public function search(Request $request)
    {
        $arrParam = array();
        if ($request->has('shop')) {
            $arrParam['shop'] = $request->input('shop');
        }
        if ($request->has('fileSearch')) {
            $file = $request->file('fileSearch');
            if(!$file) return response('Không tìm thấy file', 400);

            $uploadOptions = array(
                'file_types' => 'search-orders',
                'file_extension' => array('xls', 'xlsx')
            );
            try {
                $fileUpload = FileUpload::doUpload($file, $uploadOptions);
                if ($fileUpload['success']) {
                    Excel::import(new OrderSearchImport, $fileUpload['file_path']);
                    if ($request->session()->has('order-search-excel')) {
                        $arrParam['searchs'] = 1;
                    }
                } else {
                    \Func::setToast('Thất bại', 'Upload file thất bại', 'error');
                }
            } catch (\Exception $ex){
                \Func::setToast('Thất bại', 'Upload file thất bại', 'error');
            }
        } else {
            $arrParam['code'] = $request->input('code', '');
            $arrParam['search'] = $request->input('search', '');
            $arrParam['status_arr'] = $request->input('status_arr', '');
            $arrParam['created_from'] = $request->input('created_from', '');
            $arrParam['created_to'] = $request->input('created_to', '');
            $arrParam['send_success_from'] = $request->input('send_success_from', '');
            $arrParam['send_success_to'] = $request->input('send_success_to', '');
            $arrParam['collect_money_from'] = $request->input('collect_money_from', '');
            $arrParam['collect_money_to'] = $request->input('collect_money_to', '');
            $arrParam['reconcile_send_from'] = $request->input('reconcile_send_from', '');
            $arrParam['reconcile_send_to'] = $request->input('reconcile_send_to', '');
            $arrParam['reconcile_refund_from'] = $request->input('reconcile_refund_from', '');
            $arrParam['reconcile_refund_to'] = $request->input('reconcile_refund_to', '');
        }

        return redirect()->route('shop.orders.index', $arrParam);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $you = auth('shop')->user();
        $shopId = $you->id;

        $inputView = array(
            'receiverProvince' => 0,
            'receiverDistrict' => 0,
            'receiverWard' => 0
        );
        $shop = $this->_shopsInterface->getById($shopId);
        if (!$shop) {
            return abort(404);
        }

        /*
         * Gen key draft orders by User
         */
        $keyRedis = ':shop:'.$shopId.':draft_order';
        $draftList = Redis::lrange($keyRedis, 0, -1);

        /*
         * Save draft cookie
         */
        $keyRedisDraft = $keyRedis . '_' . (count($draftList) + 1);
        if (isset($_COOKIE[$keyRedisDraft])) {
            Redis::rpush($keyRedis, $_COOKIE[$keyRedisDraft]);
            $keyRedisDraft = $keyRedis . '_' . (count($draftList) + 2);
        }

        /*
         * Delete cookie draft, update cookie
         */
        $this->_orderServices->updateCookieDraft($keyRedis, $draftList);

        /*
         * Get draft data from redis
         */
        $draftData = array(
            'senderId'          => 0,
            'addProductName'    => array(),
            'addProductPrice'   => array(),
            'addProductSlg'     => array(),
            'addProductCode'    => array(),
            'quantity_products' => 1,
            'receiverName'      => '',
            'receiverPhone'     => '',
            'receiverAddress'   => '',
            'receiverProvinces' => 1,
            'receiverDistricts' => 1,
            'receiverWards'     => 1,
            'weight'            => 500,
            'length'            => 10,
            'width'             => 10,
            'height'            => 10,
            'cod'               => 0,
            'insurance_value'   => 0,
            'service_type'      => 'ghn',
            'note1'             => 'choxemhang',
            'note2'             => '',
            'client_code'       => ''
        );

        $draftKey = 0;
        if ($request->has('draft')) {
            $draftKey = $request->input('draft');
            $draftList = Redis::lrange($keyRedis, 0, -1);
            $numDraft = (int)$request->input('draft') + 1;
            $draftData = json_decode($draftList[$request->input('draft')], true);
            $draftData['quantity_products'] = count($draftData['addProductName']);
            $keyRedisDraft = $keyRedis . '_' . $numDraft;
        }

        /*
         * Get data to view
         */
        if ($request->has('province') && $request->has('district')) {
            $provinces = $this->_provincesInterface->getMore();
            $districts = $this->_districtsInterface->getMore(array('p_id' => (int)$request->input('province')));
            $wards = $this->_wardsInterface->getMore(array('d_id' => (int)$request->input('district')));
            $inputView = array(
                'receiverProvince' => $request->input('province'),
                'receiverDistrict' => $request->input('district'),
                'receiverWard' => $request->input('ward')
            );
        } else {
            $provinces = $this->_provincesInterface->getMore();
            $districts = $this->_districtsInterface->getMore(array('p_id' => $draftData['receiverProvinces']));
            $wards = $this->_wardsInterface->getMore(array('d_id' => $draftData['receiverDistricts']));
        }
        $orderServices = $this->_orderServiceInterface->getMore(array('status' => 1));
        $shopAddressAll = $this->_shopAddressInterface->getMore(array('shop_id' => $shopId));
        $shopAddress = (count($shopAddressAll) > 0) ? $shopAddressAll[0] : null;

        $currentHour = date('H');
        if ($currentHour < 12) {
            $arrExpectPick = array(
                date('d-m-Y') . ' 12:00:00' => 'Ca lấy ' . date('d-m-Y') . ' (12h00 - 18h00)',
                date('d-m-Y', strtotime('+1 day')) . ' 7:00:00' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (7h00 - 12h00)',
                date('d-m-Y', strtotime('+1 day')) . ' 12:00:00' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (12h00 - 18h00)',
            );
        } else {
            $arrExpectPick = array(
                date('d-m-Y', strtotime('+1 day')) . ' 7:00:00' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (7h00 - 12h00)',
                date('d-m-Y', strtotime('+1 day')) . ' 12:00:00' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (12h00 - 18h00)',
                date('d-m-Y', strtotime('+2 day')) . ' 7:00:00' => 'Ca lấy ' . date('d-m-Y', strtotime('+2 day')) . ' (7h00 - 12h00)',
            );
        }
        $arrNote1 = OrderConstant::notes;
        $arrPayfee = OrderConstant::payfees;

        // Tam tinh phi van chuyen
        $arrFeeExpertPick = array();
        if (count($shopAddressAll) > 0) {
            if (count($orderServices) > 0) {
                foreach ($orderServices as $key => $service) {
                    $payloadFee = array(
                        'p_id_send' => $shopAddress->p_id,
                        'p_id_receive' => $draftData['receiverProvinces'],
                        'd_id_receive' => $draftData['receiverDistricts'],
                        'service' => $service->alias,
                        'weight' => $draftData['weight']
                    );
                    $check_total_fee = $this->_calculatorFee->calculatorFee($payloadFee);
                    if (!$check_total_fee->status) {
                        unset($orderServices[$key]);
                        continue;
//                        throw new \Exception('Dữ liệu không chính xác');
                    }
                    $total_fee = $check_total_fee->result;
                    $expertPick = date('d-m-Y H:i', strtotime('+ ' . $check_total_fee->timePick->to . ' day', strtotime(array_keys($arrExpectPick)[0])));

                    $item = new \stdClass();
                    $item->fee = $total_fee;
                    $item->timePick = $expertPick;

                    $arrFeeExpertPick[] = $item;
                }
            }
        }

        return view('Shops::orders.create', [
            'shop' => $shop,
            'shopAddress' => $shopAddress,
            'shopAddressAll' => $shopAddressAll,
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards,
            'inputView' => $inputView,
            'keyRedisDraft' => $keyRedisDraft,
            'orderServices' => $orderServices,
            'arrPayfee' => $arrPayfee,
            'arrExpectPick' => $arrExpectPick,
            'arrFeeExpertPick' => $arrFeeExpertPick,
            'arrNote1' => $arrNote1,
            'draftKey' => $draftKey,
            'draftData' => $draftData
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $you = auth('shop')->user();

        $validator = Validator::make($request->all(), [
            'shopId' => 'required|numeric',
            'senderId' => 'required|numeric',
            'receiverName' => array('required', new ExceptSpecialCharRule()),
            'receiverPhone' => array('required', new PhoneRule()),
            'receiverAddress' => array('required', new ExceptSpecialCharRule()),
            'receiverProvinces' => 'required|numeric',
            'receiverDistricts' => 'required|numeric',
            'receiverWards' => 'required|numeric',
            'service_type' => 'required||exists:order_services,alias',
            'weight' => 'required|numeric',
            'cod' => 'numeric|min:0',
            'insurance_value' => 'numeric|min:0',
            'expect_pick' => 'required',
            'payfee' => 'required||in:payfee_sender,payfee_receiver',
            'note1' => 'required||in:choxemhang,choxemhangkhongthu,khongchoxemhang',
            'addProductCode.*' => array('nullable', new ExceptSpecialCharRule()),
            'client_code' => array('nullable', new ExceptSpecialCharRule()),
            'note2' => array('nullable', new ExceptSpecialCharRule())
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('shop.orders.create-by-shop', array(
                    'shop_id' => $request->input('shopId'),
                    'province' => $request->input('receiverProvinces'),
                    'district' => $request->input('receiverDistricts'),
                    'ward' => $request->input('receiverWards')
                ))
                ->withInput()
                ->withErrors($validator->errors());
        }

        $payload = $request->only(array(
            'keyRedisDraft',
            'shopId',
            'senderId',
            'receiverName',
            'receiverPhone',
            'receiverAddress',
            'receiverProvinces',
            'receiverDistricts',
            'receiverWards',
            'address_refund',
            'quantity_products',
            'addProductName',
            'addProductPrice',
            'addProductSlg',
            'addProductCode',
            'weight',
            'length',
            'width',
            'height',
            'cod',
            'insurance_value',
            'service_type',
            'expect_pick',
            'payfee',
            'client_code',
            'note1',
            'note2'
        ));
        $payload['user_id'] = Auth::guard('shop')->id();
        $payload['user_type'] = 'shop';

        $dataRes = $this->_orderServices->crudStore($payload);
        if (!$dataRes->result) {
            return redirect()->back()->withInput()->withErrors($dataRes->error);
        }

        \Func::setToast('Thành công', 'Thêm mới thành công đơn hàng: ' . $dataRes->order->lading_code, 'notice');
        return redirect()->route('shop.orders.index', array('shop' => $payload['shopId']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        /*
         * Get data to view
         */
        if ($request->has('lading_code')) {
            $order = $this->_ordersInterface->getOne(array('lading_code' => $request->input('lading_code')));
        } else {
            $order = $this->_ordersInterface->getById($id);
        }

        if (!$order) {
            return redirect()->back();
        }

        $shopId = $order->shop_id;
        $shop = $this->_shopsInterface->getById($shopId);
        if (!$shop) {
            return redirect()->back();
        }

        /*
         * Order log
         */
        $logsData = array();
        $logs = $this->_orderLogInterface->getMore(array('order_id' => $order->id));
        if ($logs) {
            foreach ($logs as $log) {
                $dt = strtotime($log->timer);
                $getThu = strtolower(date('l', $dt));
                $getDate = OrderConstant::weekday[$getThu] . ' ' . date('d-m-Y', $dt);

                $logsData[$getDate] = isset($logsData[$getDate]) ? $logsData[$getDate] : array();
                array_unshift($logsData[$getDate], $log);
            }
        }

        /*
         * Get product info
         */
        $products = $this->_orderProductInterface->getMore(array('order_id' => $order->id));
        $countProduct = (count($products) > 0) ? count($products) : 1;

        /*
         * Get receiver info
         */
        $provinces = $this->_provincesInterface->getMore();
        $districts = $this->_districtsInterface->getMore(array('p_id' => $order->receiver->p_id));
        $wards = $this->_wardsInterface->getMore(array('d_id' => $order->receiver->d_id));

        /*
         * Get shop info
         */
        $shopAddressAll = $this->_shopAddressInterface->getMore(array('shop_id' => $shopId));
        $shopAddress = (count($shopAddressAll) > 0) ? $shopAddressAll[0] : null;

        //Phí vận chuyển phát sinh, Phí thu hộ phát sinh, Tiền thu hộ phát sinh
       $incurre_fee_list[] = 'incurred_fee_transport';
       $incurre_fee_list[] = 'incurred_fee_cod';
       $incurre_fee_list[] = 'incurred_total_cod';

       $incurred_fee = $this->_orderFeeServices->getFeeIncurred($order, $incurre_fee_list);

        return view('Shops::orders.show', [
            'logs' => $logsData,
            'shop' => $shop,
            'order' => $order,
            'products' => $products,
            'countProduct' => $countProduct,
            'shopAddress' => $shopAddress,
            'shopAddressAll' => $shopAddressAll,
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards,
            'incurred_fee' => $incurred_fee,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $you = auth('shop')->user();

        /*
         * Get data to view
         */
        $order = $this->_ordersInterface->getById($id);
        if (!$order) {
            return redirect()->back();
        }

        $shopId = $order->shop_id;
        $shop = $this->_shopsInterface->getById($shopId);
        if (!$shop) {
            return redirect()->back();
        }

        /*
         * Get product info
         */
        $products = $this->_orderProductInterface->getMore(array('order_id' => $order->id));
        $countProduct = (count($products) > 0) ? count($products) : 1;

        /*
         * Get receiver info
         */
        $provinces = $this->_provincesInterface->getMore();
        $districts = $this->_districtsInterface->getMore(array('p_id' => $order->receiver->p_id));
        $wards = $this->_wardsInterface->getMore(array('d_id' => $order->receiver->d_id));

        /*
         * Get shop info
         */
        $orderServices = $this->_orderServiceInterface->getMore(array('status' => 1));
        $shopAddressAll = $this->_shopAddressInterface->getMore(array('shop_id' => $shopId));
        $shopAddress = (count($shopAddressAll) > 0) ? $shopAddressAll[0] : null;

        $currentHour = date('H');
        if ($currentHour < 12) {
            $arrExpectPick = array(
                date('d-m-Y') . ' 12:00:00' => 'Ca lấy ' . date('d-m-Y') . ' (12h00 - 18h00)',
                date('d-m-Y', strtotime('+1 day')) . ' 7:00:00' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (7h00 - 12h00)',
                date('d-m-Y', strtotime('+1 day')) . ' 12:00:00' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (12h00 - 18h00)',
            );
        } else {
            $arrExpectPick = array(
                date('d-m-Y', strtotime('+1 day')) . ' 7:00:00' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (7h00 - 12h00)',
                date('d-m-Y', strtotime('+1 day')) . ' 12:00:00' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (12h00 - 18h00)',
                date('d-m-Y', strtotime('+2 day')) . ' 7:00:00' => 'Ca lấy ' . date('d-m-Y', strtotime('+2 day')) . ' (7h00 - 12h00)',
            );
        }
        $arrNote1 = OrderConstant::notes;
        $arrPayfee = OrderConstant::payfees;

        // Tam tinh phi van chuyen
        $arrFeeExpertPick = array();
        if (count($shopAddressAll) > 0) {
            if (count($orderServices) > 0) {
                foreach ($orderServices as $service) {
                    $payloadFee = array(
                        'p_id_send' => $order->sender_id,
                        'p_id_receive' => $order->receiver->p_id,
                        'd_id_receive' => $order->receiver->d_id,
                        'service' => $service->alias,
                        'weight' => $order->weight
                    );
                    $check_total_fee = $this->_calculatorFee->calculatorFee($payloadFee);
                    if (!$check_total_fee->status) {
                        throw new \Exception('Dữ liệu không chính xác');
                    }
                    $total_fee = $check_total_fee->result;
                    $expertPick = date('d-m-Y H:i', strtotime('+ ' . $check_total_fee->timePick->to . ' day', strtotime(array_keys($arrExpectPick)[0])));

                    $item = new \stdClass();
                    $item->fee = $total_fee;
                    $item->timePick = $expertPick;

                    $arrFeeExpertPick[] = $item;
                }
            }
        }

        return view('Shops::orders.edit', [
            'shop' => $shop,
            'order' => $order,
            'products' => $products,
            'countProduct' => $countProduct,
            'shopAddress' => $shopAddress,
            'shopAddressAll' => $shopAddressAll,
            'orderServices' => $orderServices,
            'arrExpectPick' => $arrExpectPick,
            'arrFeeExpertPick' => $arrFeeExpertPick,
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards,
            'arrNote1' => $arrNote1,
            'arrPayfee' => $arrPayfee,
        ]);
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

        $validator = Validator::make($request->all(), [
            'orderId' => 'required|exists:orders,id',
            'shopId' => 'required|numeric',
            'shopId' => 'required|numeric',
            'senderId' => 'required|numeric',
            'receiverName' => array('required', new ExceptSpecialCharRule()),
            'receiverPhone' => array('required', new PhoneRule()),
            'receiverAddress' => array('required', new ExceptSpecialCharRule()),
            'receiverProvinces' => 'required|numeric',
            'receiverDistricts' => 'required|numeric',
            'receiverWards' => 'required|numeric',
            'weight' => 'required|numeric',
            'cod' => 'numeric|min:0',
            'insurance_value' => 'numeric|min:0',
            'service_type' => 'required||exists:order_services,alias',
            'addProductCode.*' => array('nullable', new ExceptSpecialCharRule()),
            'client_code' => array('nullable', new ExceptSpecialCharRule()),
            'note2' => array('nullable', new ExceptSpecialCharRule())
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('shop.orders.edit', array(
                    'order' => $id
                ))
                ->withInput()
                ->withErrors($validator->errors());
        }

        $payload = $request->only(array(
            'orderId',
            'shopId',
            'senderId',
            'receiverName',
            'receiverPhone',
            'receiverAddress',
            'receiverProvinces',
            'receiverDistricts',
            'receiverWards',
            'address_refund',
            'quantity_products',
            'addProductId',
            'addProductName',
            'addProductPrice',
            'addProductSlg',
            'addProductCode',
            'weight',
            'length',
            'width',
            'height',
            'cod',
            'insurance_value',
            'service_type',
            'expect_pick',
            'payfee',
            'client_code',
            'note1',
            'note2'
        ));
        $payload['user_id'] = Auth::guard('shop')->id();
        $payload['user_type'] = 'shop';

        $dataRes = $this->_orderServices->crudUpdate($payload);
        if (!$dataRes->result) {
            return redirect()
                ->route('shop.orders.edit', array(
                    'order' => $id
                ))
                ->withInput()
                ->withErrors($dataRes->error);
        }

        \Func::setToast('Thành công', 'Cập nhật thành công đơn hàng', 'notice');
        return redirect()->route('shop.orders.index', array('shop' => $payload['shopId']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $you = auth('shop')->user();

        $shop = $this->_shopsInterface->getById($id);
        if ($shop) {
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $shop,
            ];
            //Lưu log
            event(new CreateLogEvents($log_data, 'orders', 'orders_delete'));

            $shop->delete();
        }
        return redirect()->route('admin.shops.index');
    }
}

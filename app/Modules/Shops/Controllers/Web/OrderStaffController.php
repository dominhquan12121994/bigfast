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

use App\Modules\Orders\Models\Services\ShopServices;
use App\Modules\Orders\Models\Services\OrderServices;
use App\Modules\Orders\Models\Services\CalculatorFeeServices;
use App\Modules\Orders\Models\Services\OrderShipAssignedServices;
use App\Modules\Orders\Models\Services\OrderFeeServices;

use App\Modules\Orders\Exports\OrderSheet;
use Maatwebsite\Excel\Facades\Excel;

use App\Modules\Systems\Events\CreateLogEvents;

class OrderStaffController extends AbstractWebController
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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $staff = auth('shopStaff')->user();
        if (!$staff) {
            abort(404);
        }

        $shop = $this->_shopsInterface->getById($staff->shop_id);

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

        if ($status > 0 || 1===1) {
            if ($status === 5) {
                $conditions['status'] = array($status, 9);
                if ($status_detail === 51)
                    $conditions['status_detail'] = array($status_detail, 91);
            }

            $viewFile = 'Shops::order-staff.list';
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
            $viewFile = 'Shops::order-staff.queue';
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
                $getDate = OrderConstant::weekday[$getThu] . ' ' . date('d/m/Y', $dt);

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

        return view('Shops::order-staff.show', [
            'logs' => $logsData,
            'shop' => $shop,
            'order' => $order,
            'products' => $products,
            'countProduct' => $countProduct,
            'shopAddress' => $shopAddress,
            'shopAddressAll' => $shopAddressAll,
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards
        ]);
    }

    public function export(Request $request)
    {
        $you = auth('shopStaff')->user();
        if (!$you) {
            abort(404);
        }
        $shop_id = $you->shop_id;

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

        return redirect()->route('shop.order-staff.index', $arrParam);
    }
}

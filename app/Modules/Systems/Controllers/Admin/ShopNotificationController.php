<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Controllers\Admin;

use Redirect;
use Validator;
use Illuminate\Http\Request;
use App\Rules\ExceptSpecialCharRule;
use App\Http\Controllers\Admin\AbstractAdminController;
use DB;
use Auth;
use App\Modules\Operators\Models\Repositories\Contracts\WardsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Operators\Models\Repositories\Contracts\PostOfficesInterface;
use App\Modules\Orders\Constants\ShopConstant;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationInterface;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;
use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Systems\Events\ShopNotificationEvent;
use App\Modules\Systems\Services\NotificationServices;

class ShopNotificationController extends AbstractAdminController
{
    protected $_postOfficesInterface;
    protected $_provincesInterface;
    protected $_districtsInterface;
    protected $_wardsInterface;
    protected $_notificationInterface;
    protected $_notificationSendInterface;
    protected $_shopsInterface;
    protected $_notificationServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PostOfficesInterface $postOfficesInterface,
                                ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface,
                                NotificationInterface $notificationInterface,
                                NotificationSendInterface $notificationSendInterface,
                                ShopsInterface $shopsInterface,
                                NotificationServices $notificationServices,
                                WardsInterface $wardsInterface)
    {
        parent::__construct();

        $this->_postOfficesInterface = $postOfficesInterface;
        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
        $this->_wardsInterface = $wardsInterface;
        $this->_notificationInterface = $notificationInterface;
        $this->_notificationSendInterface = $notificationSendInterface;
        $this->_shopsInterface = $shopsInterface;
        $this->_notificationServices = $notificationServices;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_shop_notification_view')) {
            abort(403);
        }

        $shopId = (int)$request['shop_id'];
        $shopInfo = '';
        $beginDate = $request->input('begin', date('d-m-Y', strtotime('-30 days')));
        $endDate = $request->input('end', date('d-m-Y'));

        if ($shopId) {
            $validator = Validator::make($request->all(), [
                'shop_id' => 'exists:order_shops,id',
            ],
            [
                'shop_id.exists' => 'Không tồn tại shop',
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator->errors());
            }
            $shopInfo = $this->_shopsInterface->getById($shopId);
            $arrNotification = $this->_notificationSendInterface->getMore(
                array(
                    'shop_id' => $shopId,
                    'date_range' => array(
                        date('Ymd', strtotime($beginDate)),
                        date('Ymd', strtotime($endDate)),
                    ),
                ),
                array(
                    'with' => array(
                        'notification',
                        'shop',
                        'user',
                    ),
                    'withTrashed' => true,
                    'orderBy' => 'id',
                    'direction' => 'DESC'
                ),
                10
            );
            foreach ($arrNotification as $value) {
                $notification = $value->notification;

                if ($notification->receiver_quantity === 1) {
                    if ($value->shop) {
                        $value['receiver_name'] = $value->shop->name;
                    } else {
                        $value['receiver_name'] = $value->user->name;
                    }
                }

                if ($notification->selected_purpose) {
                    $selectedPurposes = explode(',', $notification->selected_purpose);
                    $arrPurpose = array_map(function ($element) {
                        return ShopConstant::purposes[$element];
                    }, $selectedPurposes);
                    $notification['selected_purpose'] = implode(', ', $arrPurpose);
                }

                if ($notification->selected_branch) {
                    $selectedBranchs = explode(',', $notification->selected_branch);
                    $arrBranch = array_map(function ($element) {
                        return ShopConstant::branchs[$element]['name'];
                    }, $selectedBranchs);
                    $notification['selected_branch'] = implode(', ', $arrBranch);
                }

                if ($notification->selected_scale) {
                    $selectedScales = explode(',', $notification->selected_scale);
                    $arrScale = array_map(function ($element) {
                        return ShopConstant::scales[$element];
                    }, $selectedScales);
                    $notification['selected_scale'] = implode(', ', $arrScale);
                }
                $arrNotification->each(function ($item) {
                    return $item->content = $this->_notificationServices->generateContent(array(
                        'content_data' => json_decode($item->notification->content_data)
                    ));
                });
            }
        } else {
            $conditions = array(
                'date_range' => array(
                    date('Ymd', strtotime($beginDate)),
                    date('Ymd', strtotime($endDate)),
                ),
            );
            $fetchOptions = array(
                'with' => array(
                    'receiveNotification',
                    'receiveNotification.shop',
                    'receiveNotification.user',
                ),
                'withTrashed' => true,
                'orderBy' => 'id',
                'direction' => 'DESC'
            );
            $arrNotification = $this->_notificationInterface->getMore($conditions, $fetchOptions, 10);

            foreach ($arrNotification as $notification) {
                if ($notification->receiver_quantity === 1) {
                    if ($notification->receiveNotification[0]->shop) {
                        $notification->receiveNotification[0]['receiver_name'] = $notification->receiveNotification[0]->shop->name;
                    } else {
                        $notification->receiveNotification[0]['receiver_name'] = $notification->receiveNotification[0]->user->name;
                    }
                }

                if ($notification->selected_purpose) {
                    $selectedPurposes = explode(',', $notification->selected_purpose);
                    $arrPurpose = array_map(function ($element) {
                        return ShopConstant::purposes[$element];
                    }, $selectedPurposes);
                    $notification['selected_purpose'] = implode(', ', $arrPurpose);
                }

                if ($notification->selected_branch) {
                    $selectedBranchs = explode(',', $notification->selected_branch);
                    $arrBranch = array_map(function ($element) {
                        return ShopConstant::branchs[$element]['name'];
                    }, $selectedBranchs);
                    $notification['selected_branch'] = implode(', ', $arrBranch);
                }

                if ($notification->selected_scale) {
                    $selectedScales = explode(',', $notification->selected_scale);
                    $arrScale = array_map(function ($element) {
                        return ShopConstant::scales[$element];
                    }, $selectedScales);
                    $notification['selected_scale'] = implode(', ', $arrScale);
                }
            }
            $arrNotification->each(function ($item) {
                return $item->content = $this->_notificationServices->generateContent(array(
                    'content_data' => json_decode($item->content_data)
                ));
            });
        }


        $filter = array(
            'date_range' => array($beginDate, $endDate)
        );

        return view('Orders::shop-notification.list', [
            'arrNotification' => $arrNotification,
            'filter' => $filter,
            'shopInfo' => $shopInfo,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_shop_notification_create')) {
            abort(403);
        }

        $arrShopSelected = array();

        $filter = array(
            'shop_name' => $request->input('shop_name'),
            'purposes' => $request->input('shop_purpose', array()),
            'scales' => $request->input('shop_scale', array()),
        );

        $shopPurposes = array();
        $shopScales = array();
        $shopNames = array();
        if ($request->has('shop_purpose')) {
            $shopPurposes = $request->input('shop_purpose');
        }
        if ($request->has('shop_scale')) {
            $shopScales = $request->input('shop_scale');
        }
        if ($request->filled('shop_name')) {
            $shopNames = array_map('trim', explode(',', $request->input('shop_name')));
        }

        if ($shopPurposes || $shopScales || $shopNames) {
            $query = DB::table('order_shops')
                ->join('order_shops_bank', 'order_shops.id', '=', 'order_shops_bank.id')
                ->whereNotNull('order_shops.device_token');

            if (!empty($shopPurposes)) {
                $query->whereIn('order_shops_bank.purpose', $shopPurposes);
            }
            if (!empty($shopScales)) {
                $query->whereIn('order_shops_bank.scale', $shopScales);
            }
            if (!empty($shopNames)) {
                $query->whereIn('order_shops.name', $shopNames);
            }
            $arrShopSelected = $query->get();
        }

        $branchs = ShopConstant::branchs;
        $scales = ShopConstant::scales;
        $purposes = ShopConstant::purposes;

        return view('Orders::shop-notification.create', [
            'filter' => $filter,
            'branchs' => $branchs,
            'scales' => $scales,
            'purposes' => $purposes,
            'arrShopSelected' => $arrShopSelected,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $you = auth('admin')->user();
            if (!$you->can('action_shop_notification_create')) {
                abort(403);
            }

            $validator = Validator::make($request->all(), [
                'notification_content' => array('string', new ExceptSpecialCharRule()),
                'notification_link' => 'nullable|url',
                'cbx_shop_id' => 'required',
                'cbx_shop_id.*' => 'required|numeric',
                'cbx_device_token' => 'required',
                'cbx_device_token.*' => 'required|string',
                'shop_purpose' => 'nullable',
                'shop_branch' => 'nullable',
                'shop_scale' => 'nullable',
            ],[
                'cbx_shop_id.required' => 'Vui lòng chọn shop',
                'notification_link.url' => 'Vui lòng điền đúng đường dẫn',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }

            $notificationContent = $request->input('notification_content');
            $notificationLink = $request->input('notification_link');
            $shopList = $request->input('cbx_shop_id');
            $deviceTokenList = $request->input('cbx_device_token');
            $dateCreate = (int)date('Ymd');

            $payload = array(
                'content_data' => json_encode(array(
                    0,
                    $notificationContent
                )),
                'link' => $notificationLink,
                'created_date' => $dateCreate,
                'receiver_quantity' => count($shopList),
                'selected_purpose' => $request->input('shop_purpose'),
                'selected_branch' => $request->input('shop_branch'),
                'selected_scale' => $request->input('shop_scale'),
                'sender_id' => $you->id,
            );

            DB::beginTransaction();

            $success = $this->_notificationInterface->create($payload);

            $arrShopNotification = array();
            $arrDeviceToken = array();
            foreach ($shopList as $key => $shopId) {
                array_push($arrShopNotification, array(
                    'notification_id' => $success->id,
                    'shop_id' => $shopId,
                    'is_read' => false,
                    'created_date' => $dateCreate,
                    'created_time' => time(),
                ));
                array_push($arrDeviceToken, $deviceTokenList[$shopId]);
            }

            $this->_notificationSendInterface->insert($arrShopNotification);

            DB::commit();

            \Func::setToast('Thành công', 'Thêm mới thông báo và gửi đi thành công');
        } catch (Throwable $e) {
            DB::rollBack();
            \Func::setToast('Thất bại', 'Thêm mới thông báo và gửi đi thất bại');
        }

        return redirect()->route('admin.shop-notification.create');
    }
}

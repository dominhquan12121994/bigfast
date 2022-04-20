<?php

/**
 * Class Controller
 * @package App\Modules\Orders\Controllers\Web
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\AbstractAdminController;

use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingInterface;

use App\Modules\Orders\Models\Services\SettingServices;

use App\Modules\Orders\Constants\OrderSettingConstant;

class OrderSettingController extends AbstractAdminController
{
    protected $_orderServiceInterface;
    protected $_orderSettingInterface;
    protected $_settingServices;

    public function __construct(SettingServices $settingServices,
                                OrderServiceInterface $orderServiceInterface,
                                OrderSettingInterface $orderSettingInterface)
    {
        parent::__construct();
        
        $this->_orderServiceInterface = $orderServiceInterface;
        $this->_orderSettingInterface = $orderSettingInterface;
        $this->_settingServices = $settingServices;
    }

    public function index() 
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_settings_fee_view'))
        {
            abort(403);
        }

        $routes = OrderSettingConstant::route;
        $regions = OrderSettingConstant::region;
        $extra = OrderSettingConstant::extra;
        $fee_type = OrderSettingConstant::fee_type;
        $colors = OrderSettingConstant::color;

        $orderSettings = $this->_orderSettingInterface->getMore(
            array(),
            array('with' => 'orderService')
        );

        //Xử lý dữ liệu đổ ra        
        $data = [];
        foreach ( $routes as $index => $item) {
            foreach ( $orderSettings as $orderSetting) {
                if ($orderSetting->route == $index ) {
                    $data[] = $orderSetting;
                }
            }
        }

        return view("Orders::settings.index", [
            'regions'           => $regions,
            'extra'             => $extra,
            'routes'            => $routes,
            'orderSettings'     => $data,
            'colors'            => $colors
        ]);
    }

    public function edit() 
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_settings_fee_update'))
        {
            abort(403);
        }

        $routes = OrderSettingConstant::route;
        $regions = OrderSettingConstant::region;
        $extra = OrderSettingConstant::extra;
        $fee_type = OrderSettingConstant::fee_type;
        $colors = OrderSettingConstant::color;
        
        $orderSettings = $this->_orderSettingInterface->getMore(
            array(),
            array('with' => 'orderService')
        );

        $orderServices = $this->_orderServiceInterface->getMore(array('status' => 1));
        
        if ( count($orderServices) == 0) {
            return redirect()->route('order-service.index');
        } 

        //Xử lý dữ liệu đổ ra
        $data = [];
        foreach ( $routes as $index => $item) {
            foreach ( $orderServices as $orderService) {
                $dataFilter = [];
                $dataFilter = array_filter($orderSettings->toArray(), function ($var) use ($index, $orderService) {
                    return ( $var['route'] == $index && $var['service'] == $orderService->alias );
                });
                if ( count ($dataFilter) > 0) {
                    $dataFilter = reset($dataFilter);
                    $dataFilter['order_service'] = $orderService;
                } else {
                    $aryRegion = [];
                    foreach ($regions as $key => $val) {
                        $aryRegion2 = [];
                        foreach ($fee_type as $f => $fv) {
                            $aryRegion2[] = '"'.$f.'":""';
                        }
                        $textRegion = implode(',', $aryRegion2);
                        $aryRegion[] = '"'.$key.'":{'.$textRegion.'}';
                    }
                    $textRegion = implode(',', $aryRegion);
                    $dataFilter['route'] = $index;
                    $dataFilter['order_service'] = $orderService;
                    $dataFilter['result'] = '{"weight":{"from":"","to":""},"region":{'.$textRegion.'},"extra":"","time":{"from":"","to":""}}';
                    $dataFilter['disable'] = 'on';
                }
                $data[] = $dataFilter;
            }
        }

        return view("Orders::settings.edit", [
            'routes'    => $routes,
            'regions'   => $regions,
            'extra'     => $extra,
            'data'      => $data,
            'fee_type'  => $fee_type,
            'colors'    => $colors
        ]);
    }

    public function update(Request $request) 
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_settings_fee_update'))
        {
            abort(403);
        }
        
        $validator = Validator::make($request->all(), [
            'weight'        => 'array',
            'weight.*'      => 'array',
            'weight.*.*'    => 'array',
            'weight.*.*.*'  => 'integer',
            'region'        => 'array',
            'region.*'      => 'array',
            'region.*.*'    => 'array',
            'region.*.*.*'  => 'array',
            'region.*.*.*.*'=> 'integer',
            'extra'         => 'array',
            'extra.*'       => 'array',
            'extra.*.*'     => 'integer',
            'time'          => 'array',
            'time.*'        => 'array',
            'time.*.*'      => 'array',
            'time.*.*.*'    => 'String',
            'show'          => 'array',
            'show.*'        => 'array',
            'show.*.*'      => 'accepted',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $data = [
            'weights' => $request->input('weight', []),
            'shows' => $request->input('show', []),
            'region' => $request->input('region', []),
            'extra' => $request->input('extra', []),
            'time' => $request->input('time', []),
        ];

        $this->_settingServices->updateSetting($data);
        \Func::setToast('Thành công', 'Sửa biểu phí thành công !', 'notice');
        return redirect()->route('admin.order-setting.index');
    }
}

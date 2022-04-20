<?php

/**
 * Class FeeController
 * @package App\Modules\Orders\Controllers\Api
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use App\Modules\Orders\Requests\SettingCodRequest;
use App\Modules\Orders\Requests\SettingFeePickRequest;
use App\Modules\Orders\Requests\SettingFeeInsuranceRequest;

use App\Http\Controllers\Api\AbstractApiController;

use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingCodInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingFeePickInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingFeeInsuranceInterface;

use App\Modules\Systems\Events\CreateLogEvents;

class OrderServiceController extends AbstractApiController
{
    protected $_orderServiceInterface;
    protected $_orderSettingCodInterface;
    protected $_orderSettingFeePickInterface;
    protected $_orderSettingFeeInsuranceInterface;

    public function __construct(OrderServiceInterface $orderServiceInterface, 
                                OrderSettingCodInterface $orderSettingCodInterface, 
                                OrderSettingFeePickInterface $orderSettingFeePickInterface,
                                OrderSettingFeeInsuranceInterface $orderSettingFeeInsuranceInterface)
    {
        parent::__construct();

        $this->_orderServiceInterface = $orderServiceInterface;
        $this->_orderSettingCodInterface = $orderSettingCodInterface;
        $this->_orderSettingFeePickInterface = $orderSettingFeePickInterface;
        $this->_orderSettingFeeInsuranceInterface = $orderSettingFeeInsuranceInterface;
    }

    public function find(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }
        
        $area = $this->_orderServiceInterface->getOne($request);

        if ($area) {
            return $this->_responseSuccess('Success', $area);
        }
        return $this->_responseError('Error');
    }

    public function storeOrderCod(SettingCodRequest $request) 
    {
        $validator = Validator::make($request->all(), [
            'max' => 'required',
            'min' => 'required',
            'type' => 'required',
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $data = $request->only(['min', 'max', 'type', 'value']);

        $created = $this->_orderSettingCodInterface->create($data);

        if ($created) {
            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $created,
            ];

            //Lưu log 
            event(new CreateLogEvents( $log_data, 'settings_cod', 'settings_cod_create' ));

            return $this->_responseSuccess('Success', $created); 
        }
        return $this->_responseError('Error');
    }

    public function findOrderCod(Request $request) {
        $id = $request->id;
        $data = $this->_orderSettingCodInterface->getById($id);

        if ($data) {
            return $this->_responseSuccess('Success', $data); 
        }
        return $this->_responseError('Error');
    }

    public function updateOrderCod(SettingCodRequest $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'max' => 'required',
            'min' => 'required',
            'type' => 'required',
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $id = $request['id'];
        $data = $request->only(['min', 'max', 'type', 'value']);

        $old_data = $this->_orderSettingCodInterface->getById((int)$id);
        $update = $this->_orderSettingCodInterface->updateById((int)$id, $data);

        if ($update) {
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $old_data,
            ];

            //Lưu log 
            event(new CreateLogEvents( $log_data, 'settings_cod', 'settings_cod_update' ));

            return $this->_responseSuccess('Success', $update); 
        }
        return $this->_responseError('Error');
    }
    public function storeOrderFeePick(SettingFeePickRequest $request) 
    {
        $validator = Validator::make($request->all(), [
            'max' => 'required',
            'min' => 'required',
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $data = $request->only(['min', 'max', 'value']);

        $created = $this->_orderSettingFeePickInterface->create($data);

        if ($created) {
            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $created,
            ];

            //Lưu log 
            event(new CreateLogEvents( $log_data, 'settings_pick', 'settings_pick_create' ));

            return $this->_responseSuccess('Success', $created); 
        }
        return $this->_responseError('Error');
    }

    public function findOrderFeePick(Request $request) {
        $id = $request->id;
        $data = $this->_orderSettingFeePickInterface->getById($id);

        if ($data) {
            return $this->_responseSuccess('Success', $data); 
        }
        return $this->_responseError('Error');
    }

    public function updateOrderFeePick(SettingFeePickRequest $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'max' => 'required',
            'min' => 'required',
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $id = $request['id'];
        $data = $request->only(['min', 'max', 'value']);

        $old_data = $this->_orderSettingFeePickInterface->getById((int)$id);
        $update = $this->_orderSettingFeePickInterface->updateById((int)$id, $data);

        if ($update) {
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $old_data,
            ];

            //Lưu log 
            event(new CreateLogEvents( $log_data, 'settings_pick', 'settings_pick_update' ));

            return $this->_responseSuccess('Success', $update); 
        }
        return $this->_responseError('Error');
    }

    public function storeOrderFeeInsurance(SettingFeeInsuranceRequest $request) 
    {
        $validator = Validator::make($request->all(), [
            'max' => 'required',
            'min' => 'required',
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $data = $request->only(['min', 'max', 'value']);

        $created = $this->_orderSettingFeeInsuranceInterface->create($data);

        if ($created) {
            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $created,
            ];

            //Lưu log 
            event(new CreateLogEvents( $log_data, 'settings_insurance', 'settings_insurance_create' ));

            return $this->_responseSuccess('Success', $created); 
        }
        return $this->_responseError('Error');
    }

    public function findOrderFeeInsurance(Request $request) {
        $id = $request->id;
        $data = $this->_orderSettingFeeInsuranceInterface->getById($id);
        if ($data) {
            return $this->_responseSuccess('Success', $data); 
        }
        return $this->_responseError('Error');
    }

    public function updateOrderFeeInsurance(SettingFeeInsuranceRequest $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'max' => 'required',
            'min' => 'required',
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $id = $request['id'];
        $data = $request->only(['min', 'max', 'value']);

        $old_data = $this->_orderSettingFeeInsuranceInterface->getById((int)$id);
        $update = $this->_orderSettingFeeInsuranceInterface->updateById((int)$id, $data);
        if ($update) {
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $old_data,
            ];

            //Lưu log 
            event(new CreateLogEvents( $log_data, 'settings_insurance', 'settings_insurance_update' ));

            return $this->_responseSuccess('Success', $update); 
        }
        return $this->_responseError('Error');
    }
}

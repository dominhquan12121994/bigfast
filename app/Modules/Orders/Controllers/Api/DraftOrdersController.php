<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Api;

use Auth;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

use App\Http\Controllers\Api\AbstractApiController;

use App\Modules\Orders\Constants\OrderConstant;
use App\Modules\Orders\Models\Services\OrderServices;

class DraftOrdersController extends AbstractApiController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrderServices $orderServices)
    {
        parent::__construct();
        $this->_orderServices = $orderServices;
    }

    public function list()
    {
        $shopId = auth()->id();

        $keyRedis = ':shop:'.$shopId.':draft_order';

        $draftList = Redis::lrange($keyRedis, 0, -1);

        if (!empty($draftList)) {
            $draftList = $this->_orderServices->filterDraft($draftList);

            $draftList = collect($draftList);

            $draftList->transform(function ($item, $id) {
                if (!empty($item)) {
                    $item->id = $id;
                    return $item;
                }
            });

            $draftList = array_values($draftList->toArray());

            return $this->_responseSuccess('Success', $draftList);
        }

        return $this->_responseSuccess('Success', array());
    }

    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $shopId = auth()->id();

        $draftId = $request->input('id');

        $keyRedis = ':shop:'.$shopId.':draft_order';

        $draftOrder = Redis::lindex($keyRedis, $draftId);

        if ($draftOrder) {
            $payload = json_decode($draftOrder, true);

            $payload['user_type'] = 'shop';
            $payload['user_id'] = $shopId;

            $validatorOrder = Validator::make($payload, [
                'shopId' => 'required|numeric',
                'senderId' => 'required|numeric',
                'receiverName' => 'required',
                'receiverPhone' => 'required',
                'receiverAddress' => 'required',
                'addProductName' => 'required',
                'addProductName.*' => 'required',
                'addProductCode' => 'nullable',
                'addProductPrice' => 'required',
                'addProductPrice.*' => 'required|numeric',
                'addProductSlg' => 'required',
                'addProductSlg.*' => 'required|numeric',
                'receiverProvinces' => 'required|numeric',
                'receiverDistricts' => 'required|numeric',
                'receiverWards' => 'required|numeric',
                'weight' => 'required|numeric',
                'length' => 'required|numeric',
                'width' => 'required|numeric',
                'height' => 'required|numeric',
                'cod' => 'required|numeric',
                'insurance_value' => 'required|numeric',
                'client_code' => 'nullable',
                'address_refund' => 'required',
                'expect_pick' => 'required',
                'payfee' => 'required',
                'note1' => 'required',
                'note2' => 'nullable',
                'service_type' => 'required|exists:order_services,alias',
                'quantity_products' => 'required|numeric',
            ],
                [
                    'receiverName.required' => 'Thiếu thông tin tên người nhận',
                    'receiverPhone.required' => 'Thiếu thông tin số điện thoại người nhận',
                    'receiverAddress.required' => 'Thiếu thông tin địa chỉ người nhận',
                    'addProductName.*.required' => 'Thiếu thông tin tên sản phẩm',
                    'addProductPrice.*.required' => 'Thiếu thông tin giá sản phẩm',
                    'addProductSlg.*.required' => 'Thiếu thông tin số lượng sản phẩm',
                    'weight.required' => 'Thiếu thông tin khối lượng của sản phẩm',
                    'length.required' => 'Thiếu thông tin kích thước chiều dài của sản phẩm',
                    'width.required' => 'Thiếu thông tin kích thước chiều rộng của sản phẩm',
                    'height.required' => 'Thiếu thông tin kích thước chiều cao của sản phẩm',
                    'cod.required' => 'Thiếu thông tin tiền thu hộ của đơn hàng',
                    'insurance_value.required' => 'Thiếu thông tin khai giá của đơn hàng',
                    'quantity_products.required' => 'Thiếu thông tin số lượng sản phẩm trong đơn hàng',
                ]);

            if ($validatorOrder->fails()) {
                $validateError = collect($validatorOrder->errors());
                $validateError = $validateError->map(function($item) {
                    return $item[0];
                });
                return $this->_responseError('Error', array_values($validateError->toArray()));
            }

            $dataRes = $this->_orderServices->crudStore($payload);

            if ($dataRes) {
                Redis::lset($keyRedis, $draftId, null);
                return $this->_responseSuccess('Success', $payload);
            }

            return $this->_responseError('Đã xảy ra lỗi, không thể tạo mới đơn hàng');
        }

        return $this->_responseError('Không tìm thấy đơn nháp');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'addProductPrice.*' => 'nullable|numeric',
            'addProductSlg.*' => 'nullable|numeric',
            'senderId' => 'nullable|numeric',
            'receiverProvinces' => 'required|numeric',
            'receiverDistricts' => 'required|numeric',
            'receiverWards' => 'required|numeric',
            'weight' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'cod' => 'nullable|numeric',
            'insurance_value' => 'nullable|numeric',
            'payfee' => array(
                Rule::in(array_keys(OrderConstant::payfees)),
            ),
            'note1' => array(
                Rule::in(array_keys(OrderConstant::notes)),
            ),
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $shopId = auth()->id();

        $request->merge(array(
            'shopId' => $shopId
        ));

        $draftOrder = json_encode($request->all());

        $keyRedis = ':shop:'.$shopId.':draft_order';

        $draftList = Redis::lrange($keyRedis, 0, -1);

        Redis::rpush($keyRedis, $draftOrder);

        return $this->_responseSuccess('Success', array(
            "id" => count($draftList)
        ));
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $shopId = auth()->id();

        $request->merge(array(
            'shopId' => $shopId
        ));

        $orderId = $request->input('id');

        $keyRedis = ':shop:'.$shopId.':draft_order';

        $order = Redis::lindex($keyRedis, $orderId);

        if (!empty($order)) {
            $order = json_decode($order);
            return $this->_responseSuccess('Success', $order);
        }

        return $this->_responseError('Không tìm thấy đơn nháp');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:0',
            'addProductPrice.*' => 'nullable|numeric',
            'addProductSlg.*' => 'nullable|numeric',
            'senderId' => 'nullable|numeric',
            'receiverProvinces' => 'required|numeric',
            'receiverDistricts' => 'required|numeric',
            'receiverWards' => 'required|numeric',
            'weight' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'cod' => 'nullable|numeric',
            'insurance_value' => 'nullable|numeric',
            'payfee' => array(
                Rule::in(array_keys(OrderConstant::payfees)),
            ),
            'note1' => array(
                Rule::in(array_keys(OrderConstant::notes)),
            ),
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $shopId = auth()->id();

        $request->merge(array(
            'shopId' => $shopId
        ));

        $draftOrder = $request->all();

        $orderId = $draftOrder['id'];

        unset($draftOrder['id']);

        $draftJson = json_encode($draftOrder);

        $keyRedis = ':shop:'.$shopId.':draft_order';

        Redis::lset($keyRedis, $orderId, $draftJson);

        return $this->_responseSuccess('Success', 'Cập nhật đơn nháp thành công');
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'arr_id' => 'required',
            'arr_id.*' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $shopId = auth()->id();

        $arrId = $request->input('arr_id');

        $keyRedis = ':shop:'.$shopId.':draft_order';

        foreach ($arrId as $id) {
            Redis::lset($keyRedis, $id, null);
        }

        return $this->_responseSuccess('Success', 'Xóa đơn nháp thành công');
    }
}

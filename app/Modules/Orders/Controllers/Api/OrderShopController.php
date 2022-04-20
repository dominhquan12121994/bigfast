<?php

/**
 * Class OrderShopController
 * @package App\Modules\Orders\Controllers\Api
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Controllers\Api;

use App\Http\Controllers\Api\AbstractApiController;

use Validator;
use Illuminate\Http\Request;

use App\Rules\PhoneRule;
use App\Helpers\AddressHelper;
use App\Modules\Orders\Jobs\CreateOrdersApi;

use App\Modules\Orders\Models\Entities\Orders;
use App\Modules\Orders\Models\Entities\OrderQueue;

use App\Modules\Orders\Models\Services\OrderServices;
use App\Modules\Orders\Models\Services\CalculatorFeeServices;
use App\Modules\Orders\Models\Services\OrderFeeServices;

use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingCodInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingFeeInsuranceInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderExtraInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsTypeInterface;

use App\Modules\Orders\Constants\OrderConstant;
use App\Modules\Orders\Resources\OrdersShopResource;
use App\Modules\Orders\Resources\SearchOrdersShopResource;

class OrderShopController extends AbstractApiController
{
    protected $_ordersInterface;
    protected $_orderServices;
    protected $_calculatorFee;
    protected $_orderSettingCodInterface;
    protected $_orderSettingFeeInsuranceInterface;
    protected $_orderFeeServices;
    protected $_orderServiceInterface;
    protected $_contactsTypeInterface;
    protected $_orderExtraInterface;
    protected $_shopBankInterface;

    public function __construct(OrdersInterface $ordersInterface,
                                OrderServices $orderServices,
                                CalculatorFeeServices $calculatorFee,
                                OrderSettingCodInterface $orderSettingCodInterface,
                                OrderSettingFeeInsuranceInterface $orderSettingFeeInsuranceInterface,
                                OrderServiceInterface $orderServiceInterface,
                                OrderFeeServices $orderFeeServices,
                                ShopBankInterface $shopBankInterface,
                                ContactsTypeInterface $contactsTypeInterface,
                                OrderExtraInterface $orderExtraInterface)
    {
        parent::__construct();

        $this->_ordersInterface = $ordersInterface;
        $this->_orderServices = $orderServices;
        $this->_calculatorFee = $calculatorFee;
        $this->_orderSettingCodInterface = $orderSettingCodInterface;
        $this->_orderSettingFeeInsuranceInterface = $orderSettingFeeInsuranceInterface;
        $this->_orderFeeServices = $orderFeeServices;
        $this->_orderServiceInterface = $orderServiceInterface;
        $this->_contactsTypeInterface = $contactsTypeInterface;
        $this->_orderExtraInterface = $orderExtraInterface;
        $this->_shopBankInterface = $shopBankInterface;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiverName' => 'required',
            'receiverPhone' => array('required', new PhoneRule()),
            'receiverAddress' => 'required',
            'receiverProvince' => 'required',
            'receiverDistrict' => 'required',
            'receiverWard' => 'required',
            'refundPhone' => array(new PhoneRule()),
            'senderName' => 'required',
            'senderPhone' => array('required', new PhoneRule()),
            'senderAddress' => 'required',
            'senderProvince' => 'required',
            'senderDistrict' => 'required',
            'senderWard' => 'required',
            'service_type' => 'required|exists:order_services,alias',
            'weight' => 'required|numeric',
            'cod_amount' => 'numeric|min:0',
            'insurance_value' => 'numeric|min:0',
            'payment_type' => 'required|in:sender,receiver',
            'required_note' => 'required|in:choxemhang,choxemhangkhongthu,khongchoxemhang'
        ], [
            'receiverName.required' => 'thiếu tên người nhận',
            'receiverPhone.required' => 'thiếu số điện thoại người nhận',
            'receiverAddress.required' => 'thiếu địa chỉ người nhận',
            'receiverProvince.required' => 'thiếu tỉnh/thành người nhận',
            'receiverDistrict.required' => 'thiếu quận/huyện người nhận',
            'receiverWard.required' => 'thiếu xã/phường người nhận',
            'senderName.required' => 'thiếu tên người gửi',
            'senderPhone.required' => 'thiếu số điện thoại người gửi',
            'senderAddress.required' => 'thiếu địa chỉ người gửi',
            'senderProvince.required' => 'thiếu tỉnh/thành người gửi',
            'senderDistrict.required' => 'thiếu quận/huyện người gửi',
            'senderWard.required' => 'thiếu xã/phường người gửi',
            'service_type.required' => 'thiếu thông tin gói cước',
            'service_type.exists' => 'gói cước không khả dụng',
            'weight.required' => 'thiếu trọng lượng sản phẩm',
            'payment_type.required' => 'thiếu thông tin bên trả phí',
            'required_note.required' => 'thiếu thông tin ghi chú',
            'required_note.in' => 'thiếu thông tin ghi chú',
            'cod_amount.numeric' => 'số tiền thu hộ phải là số',
            'insurance_value.numeric' => 'giá trị bảo hiểm phải là số',
            'cod_amount.min' => 'số tiền thu hộ không âm',
            'insurance_value.min' => 'giá trị bảo hiểm không âm',
        ]);

        if ($validator->fails()) {
            $messages = array();
            foreach ($validator->errors()->all() as $message) {
                $messages[] = $message;
            }
            return $this->_responseError(implode(', ', $messages));
        }

        $checkZone = false;
        $receiverAddress = $request->input('receiverWard') . ', ' . $request->input('receiverDistrict') . ', ' . $request->input('receiverProvince');
        $receiverZone = AddressHelper::mappingAddress($receiverAddress);
        if ($receiverZone) { if (count($receiverZone) === 3) { $checkZone = true; } }
        if (!$checkZone) {
            return $this->_responseError('Không tìm thấy địa chỉ nhận hàng');
        }

        $checkZone = false;
        $senderAddress = $request->input('senderWard') . ', ' . $request->input('senderDistrict') . ', ' . $request->input('senderProvince');
        $senderZone = AddressHelper::mappingAddress($senderAddress);
        if ($senderZone) { if (count($senderZone) === 3) { $checkZone = true; } }
        if (!$checkZone) {
            return $this->_responseError('Không tìm thấy địa chỉ lấy hàng');
        }

        $shop = auth('shop-token')->user();
        $service_type = $request->input('service_type');
        $serviceDetail = $this->_orderServiceInterface->getOne(array('alias' => $service_type));
        if (!$serviceDetail->status) {
            $shopBank = $this->_shopBankInterface->getById($shop->id);
            $servicesShop = explode(',', $shopBank->services);
            if (!in_array($service_type, $servicesShop)) {
                return $this->_responseError('Gói cước không khả dụng');
            }
        }

        $payload = array(
            'user_type' => 'shop',
            'user_id' => $shop->id,
            'shop_id' => $shop->id,
            // sender
            'senderName' => $request->input('senderName'),
            'senderPhone' => $request->input('senderPhone'),
            'senderAddress' => $request->input('senderAddress'),
            'senderProvinces' => $senderZone[0],
            'senderDistricts' => $senderZone[1],
            'senderWards' => $senderZone[2],
            //
            'created_date' => (int)date('Ymd'),
            'cod' => $request->input('cod_amount'),
            'insurance_value' => $request->input('insurance_value'),
            // receiver
            'receiverName' => $request->input('receiverName'),
            'receiverPhone' => $request->input('receiverPhone'),
            'receiverAddress' => $request->input('receiverAddress'),
            'receiverProvinces' => $receiverZone[0],
            'receiverDistricts' => $receiverZone[1],
            'receiverWards' => $receiverZone[2],
            //
            // refund
            'address_refund' => 0,
            'quantity_products' => 1,
            'weight' => $request->input('weight'),
            'length' => $request->input('length'),
            'width' => $request->input('width'),
            'height' => $request->input('height'),
            'service_type' => $request->input('service_type'),
            'payment_type' => $request->input('payment_type'),
            'required_note' => $request->input('required_note'),
            'note' => $request->input('note'),
            'client_order_code' => $request->input('client_order_code', ''),
        );
        if ($request->has('returnWard') && $request->has('returnDistrict') && $request->has('returnProvince') &&
            $request->has('returnName') && $request->has('returnPhone') && $request->has('returnAddress')) {
            $checkZone = false;
            $refundAddress = $request->input('returnWard') . ', ' . $request->input('returnDistrict') . ', ' . $request->input('returnProvince');
            $refundZone = AddressHelper::mappingAddress($refundAddress);
            if ($refundZone) { if (count($refundZone) === 3) { $checkZone = true; } }
            if (!$checkZone) {
                return $this->_responseError('Không tìm thấy địa chỉ hoàn hàng');
            } else {
                $payload['refundName'] = $request->input('returnName');
                $payload['refundPhone'] = $request->input('returnPhone');
                $payload['refundAddress'] = $request->input('returnAddress');
                $payload['refundProvinces'] = $refundZone[0];
                $payload['refundDistricts'] = $refundZone[1];
                $payload['refundWards'] = $refundZone[2];
            }
        }
        if (count($request->input('products')) > 0) {
            $payload['quantity_products'] = count($request->input('products'));
            foreach ($request->input('products') as $product) {
                $price = 0;
                if (is_numeric($product['price'])) {
                    if ($product['price'] > 0) {
                        $price = (int)$product['price'];
                    }
                }
                $quantity = 1;
                if (is_numeric($product['quantity'])) {
                    if ($product['price'] > 1) {
                        $quantity = (int)$product['quantity'];
                    }
                }
                $payload['addProductName'][] = $product['name'];
                $payload['addProductPrice'][] = $price;
                $payload['addProductSlg'][] = $quantity;
                $payload['addProductCode'][] = $product['code'];
            }
        }
        $orderQueue = OrderQueue::create([
            'status'            => 0,
            'shop_id'           => $payload['shop_id'],
            'created_date'      => $payload['created_date'],
            'receiver_name'     => $payload['receiverName'],
            'receiver_phone'    => $payload['receiverPhone'],
            'receiver_address'  => $payload['receiverAddress'],
            'cod'               => $payload['cod'],
            'client_code'       => $payload['client_order_code'],
            'created_at'        => now(),
            'updated_at'        => now()
        ]);
        $payload['queue_id'] = $orderQueue->id;
        $lading_code = $this->_orderServices->generateLandingCode();
        $payload['lading_code'] = $lading_code;

        CreateOrdersApi::dispatch($payload)->onQueue('createOrdersApi');

        // tinh phi
        $payloadFee = array(
            'p_id_send' => $senderZone[0],
            'p_id_receive' => $payload['receiverProvinces'],
            'd_id_receive' => $payload['receiverDistricts'],
            'service' => $payload['service_type'],
            'weight' => $payload['weight']
        );
        $check_transport_fee = $this->_calculatorFee->calculatorFee($payloadFee);
        if (!$check_transport_fee->status) {
            return $this->_responseError('Không đủ dữ liệu tính phí');
        }

        // phi van chuyen
        $transport_fee = $check_transport_fee->result;

        // phi thu cod
        $cod_fee = 0;
        if (substr($payload['service_type'], 0, 2) === 'dg' && substr($payload['service_type'], -1) === 'k') {
            $cod_fee = 0;
        } else {
            $check_cod_fee = $this->_orderSettingCodInterface->getOne(array('cod' => $payload['cod']));
            if ($check_cod_fee) {
                // percent | money
                if ($check_cod_fee->type === 'money')
                    $cod_fee = $check_cod_fee->value;
                else
                    $cod_fee = ($check_cod_fee->value / 100) * $payload['cod'];
            }
        }

        // phi bao hiem
        $insurance_fee = 0;
        if (substr($payload['service_type'], 0, 2) === 'dg' && substr($payload['service_type'], -1) === 'k') {
            $insurance_fee = 0;
        } else {
            $check_insurance_fee = $this->_orderSettingFeeInsuranceInterface->getOne(array('insurance' => $payload['insurance_value']));
            if ($check_insurance_fee) {
                $insurance_fee = $check_insurance_fee->value;
            }
        }

        $total_fee = $transport_fee + $cod_fee + $insurance_fee;

        $dataResponse = array('lading_code' => $lading_code, 'total_fee' => $total_fee);
        return $this->_responseSuccess('Success', $dataResponse);
    }

    public function cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lading_codes' => 'required',
            'lading_codes.*' => 'required|size:12|starts_with:B',
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Mã vận đơn không khả dụng');
        }

        $statusDetailAllow = array(11, 12, 13);

        $arrCancelId = array();
        $shop_id = auth('shop-token')->id();
        $lading_codes = $request->input('lading_codes');
        foreach ($lading_codes as $key => $lading_code) {
            $order = $this->_ordersInterface->getOne(array('shop_id' => $shop_id, 'lading_code' => $lading_code), array('orderBy' => 'id'));
            if (!$order) {
                return $this->_responseError('Mã vận đơn không khả dụng: ' . $lading_code);
            }

            if ($order->status_detail === 61) {
                return $this->_responseError('Đơn đã được huỷ: ' . $lading_code);
            }

            if (!in_array($order->status_detail, $statusDetailAllow)) {
                return $this->_responseError('Không thể huỷ đơn đang di chuyển: ' . $lading_code);
            }

            $arrCancelId[] = $order->id;
        }

        if (!empty($arrCancelId)) {
            Orders::whereIn('id', $arrCancelId)
                ->update(['status' => 6, 'status_detail' => 61]);

            return $this->_responseSuccess('Huỷ đơn thành công');
        }

        return $this->_responseError('Mã vận đơn không khả dụng');
    }

    public function getByShop(Request $request)
    {
        $shop = $request->user();
        $validator = Validator::make($request->all(), [
            'start_date' => 'date|before_or_equal:end_date',
            'end_date' => 'date|before:tomorrow',
            'status' => 'numeric',
            'status_detail' => 'numeric',
            'page' => 'numeric',
            'limit' => 'numeric'
        ], [
            'start_date.required' => 'Ngày bắt đầu bắt buộc',
            'end_date.required' => 'Ngày kết thúc bắt buộc',
            'date' => 'Sai định dạng ngày',
            'start_date.before_or_equal' => 'Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc',
            'end_date.before' => 'Ngày kết thúc phải nhỏ hơn hoặc bằng ngày hiện tại',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        $limit = $request->input('limit', 10);
        $conditions['shop_id'] = $shop->id;
        $start_date = date('Ymd', strtotime('-30 day'));
        $end_date = date('Ymd');
        $statusList = OrderConstant::statusShopApi;
        $status = $request->input('status', $statusList[0] );
        $statusActive = $request->input('status', $statusList[0] );
        $status_detail = $request->input('status_detail', null);

        if ( $request->has('start_date') && $request->has('end_date') ) {
            $start_date = date('Ymd', strtotime($request->input('start_date')));
            $end_date = date('Ymd', strtotime($request->input('end_date')));
        }
        $conditions['created_range'] = array( $start_date, $end_date );
        if ($status === 5) {
            $status = array(5, 9);
        }
        if ($status_detail === 51) {
            $status_detail = array(51, 91);
        }
        $conditions['status'] = $status;
        $conditions['status_detail'] = $status_detail;
        $conditions['lading_code'] = $request->input('lading_code', null);

        $ordersList = $this->_ordersInterface->customPaginate(
            $conditions,
            array(
                'with' => array('shop', 'extra', 'sender.provinces', 'receiver.provinces', 'contacts'),
                'orderBy' => array('status_detail', 'updated_at'),
                'direction' => array('ASC')
            ), $limit);

        //Phí vận chuyển phát sinh, Phí thu hộ phát sinh, Tiền thu hộ phát sinh, Phí bảo hiểm
        $incurre_fee_list[] = array('incurred_fee_transport', 'insurance_value', 'incurred_total_cod', 'insurance' );
        $incurred_fee = $this->_orderFeeServices->getFeeIncurred($ordersList, $incurre_fee_list, 'customPaginate');

        $ordersList['incurred_fee'] = $incurred_fee;
        $payload = array(
            'created_range' => array( $start_date, $end_date ),
            'shop_id' => $shop->id,
            'aryStatus' => $statusList,
        );
        $ordersList['statusList'] = $statusList;
        $ordersList['countAryStatus'] = $this->_orderServices->countStatusByCondititon($payload);
        $ordersList['totalOrders'] = $this->_ordersInterface->checkExist($conditions);
        $ordersList['statusActive'] = $statusActive;
        //Status được hiển thị filter ngày
        $ordersList['status_date_allow'] = array();

        return $this->_responseSuccess('Success', new OrdersShopResource($ordersList) );
    }

    public function settingByShop() {
        $result = array();

        $currentHour = date('H');
        if ($currentHour < 12) {
            $result['expect_pick'] = array(
                array(
                    'name' => 'Ca lấy ' . date('d-m-Y') . ' (12h00 - 18h00)',
                    'value' => date('d-m-Y') . ' 12:00:00'
                ),
                array(
                    'name' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (7h00 - 12h00)',
                    'value' => date('d-m-Y', strtotime('+1 day')) . ' 7:00:00'
                ),
                array(
                    'name' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (12h00 - 18h00)',
                    'value' => date('d-m-Y', strtotime('+1 day')) . ' 12:00:00'
                )
            );
        } else {
            $result['expect_pick'] = array(
                array(
                    'name' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (7h00 - 12h00)',
                    'value' => date('d-m-Y', strtotime('+1 day')) . ' 7:00:00'
                ),
                array(
                    'name' => 'Ca lấy ' . date('d-m-Y', strtotime('+1 day')) . ' (12h00 - 18h00)',
                    'value' => date('d-m-Y', strtotime('+1 day')) . ' 12:00:00'
                ),
                array(
                    'name' => 'Ca lấy ' . date('d-m-Y', strtotime('+2 day')) . ' (7h00 - 12h00)',
                    'value' => date('d-m-Y', strtotime('+2 day')) . ' 7:00:00'
                )
            );
        }

        $result['service'] = $this->_orderServiceInterface->getMore(array('status' => 1))->transform(function ($item) {
            return array(
                'name' => $item->name,
                'value' => $item->alias
            );
        });

        $arrNote1 = OrderConstant::notes;
        foreach ($arrNote1 as $key => $item) {
            $result['note1'][] = array(
                'name' => $item,
                'value' => $key
            );
        }

        $payfees = OrderConstant::payfees;
        foreach ($payfees as $key => $item) {
            $result['payfees'][] = array(
                'name' => $item,
                'value' => $key
            );
        }

        $result['contact_types'] = $this->_contactsTypeInterface->getMore()->transform(function ($item){
            return array(
                'name' => $item->name,
                'value' => $item->id
            );
        });

        return $this->_responseSuccess('Success', $result);
    }

    public function store(Request $request) {
        $shop = $request->user();

        $validator = Validator::make($request->all(), [
            'senderId' => 'required|numeric',
            'receiverName' => 'required',
            'receiverPhone' => array('required', new PhoneRule()),
            'receiverAddress' => 'required',
            'receiverProvinces' => 'required|numeric',
            'receiverDistricts' => 'required|numeric',
            'receiverWards' => 'required|numeric',
            'service_type' => 'required||exists:order_services,alias',
            'weight' => 'required|numeric',
            'cod' => 'numeric|min:0',
            'insurance_value' => 'numeric|min:0',
            'expect_pick' => 'required',
            'payfee' => 'required||in:payfee_sender,payfee_receiver',
            'note1' => 'required||in:choxemhang,choxemhangkhongthu,khongchoxemhang'
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        $payload = $request->only(array(
            'senderId',
            'receiverName',
            'receiverPhone',
            'receiverAddress',
            'receiverProvinces',
            'receiverDistricts',
            'receiverWards',
            'refundName',
            'refundPhone',
            'refundAddress',
            'refundProvinces',
            'refundDistricts',
            'refundWards',
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
        $payload['shopId'] = $shop->id;
        $payload['user_id'] = $shop->id;
        $payload['user_type'] = 'shop';

        $dataRes = $this->_orderServices->crudStore($payload);
        if (!$dataRes->result) {
            return $this->_responseError($dataRes->message);
        }

        return $this->_responseSuccess('Success', 'Tạo đơn thành công !');
    }

    public function searchByShop(Request $request)
    {
        $shop = $request->user();
        $validator = Validator::make($request->all(), [
            'start_date' => 'date|before_or_equal:end_date',
            'end_date' => 'date|before:tomorrow',
            'page' => 'numeric',
            'limit' => 'numeric'
        ], [
            'start_date.required' => 'Ngày bắt đầu bắt buộc',
            'end_date.required' => 'Ngày kết thúc bắt buộc',
            'date' => 'Sai định dạng ngày',
            'start_date.before_or_equal' => 'Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc',
            'end_date.before' => 'Ngày kết thúc phải nhỏ hơn hoặc bằng ngày hiện tại',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        $limit = $request->input('limit', 10);
        $conditions['shop_id'] = $shop->id;
        $start_date = date('Ymd', strtotime('-30 day'));
        $end_date = date('Ymd');

        if ( $request->has('start_date') && $request->has('end_date') ) {
            $start_date = date('Ymd', strtotime($request->input('start_date')));
            $end_date = date('Ymd', strtotime($request->input('end_date')));
        }
        $conditions['created_range'] = array( $start_date, $end_date );

        if ($request->filled('lading_code')) {
            $lading_code = $request->input('lading_code', null);

            if (strlen($lading_code) === 12 && strtolower(substr($lading_code, 0, 1)) === 'b') {
                $conditions['lading_code'] = $request->input('lading_code', null);

                $ordersList = $this->_ordersInterface->customPaginate(
                    $conditions,
                    array(
                        'with' => array('shop', 'extra', 'sender.provinces', 'receiver.provinces', 'contacts'),
                        'orderBy' => array('status_detail', 'updated_at'),
                        'direction' => array('ASC')
                    ), $limit);
            } else {
                $orderExtra_id = 0;
                $orderExtra = $this->_orderExtraInterface->getOne(
                    array(
                        'client_code' => $request->input('lading_code')
                    )
                );
                if ($orderExtra) {
                    $orderExtra_id = $orderExtra->id;
                }
                $conditions['id'] = $orderExtra_id;
                $ordersList = $this->_ordersInterface->customPaginate(
                    $conditions,
                    array(
                        'with' => array('shop', 'extra', 'sender.provinces', 'receiver.provinces', 'contacts'),
                        'orderBy' => array('status_detail', 'updated_at'),
                        'direction' => array('ASC')
                    ), $limit);
            }
        } else {
            $ordersList = $this->_ordersInterface->customPaginate(
                $conditions,
                array(
                    'with' => array('shop', 'extra', 'sender.provinces', 'receiver.provinces', 'contacts'),
                    'orderBy' => array('status_detail', 'updated_at'),
                    'direction' => array('ASC')
                ), $limit);
        }

        //Phí vận chuyển phát sinh, Phí thu hộ phát sinh, Tiền thu hộ phát sinh, Phí bảo hiểm
        $incurre_fee_list[] = array('incurred_fee_transport', 'insurance_value', 'incurred_total_cod', 'insurance' );
        $incurred_fee = $this->_orderFeeServices->getFeeIncurred($ordersList, $incurre_fee_list, 'customPaginate');
        $ordersList['incurred_fee'] = $incurred_fee;

        return $this->_responseSuccess('Success', new SearchOrdersShopResource($ordersList) );
    }
}

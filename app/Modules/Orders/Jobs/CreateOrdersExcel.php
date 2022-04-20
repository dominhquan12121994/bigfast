<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Created by PhpStorm.
 * User: Electric
 * Date: 3/5/2021
 * Time: 4:54 PM
 */

namespace App\Modules\Orders\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Modules\Orders\Models\Entities\OrderQueue;
use App\Modules\Orders\Models\Services\OrderServices;
use App\Modules\Orders\Models\Entities\OrderShopAddress;

use App\Helpers\StringHelper;

class CreateOrdersExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(OrderServices $orderServices)
    {
        try {
            DB::beginTransaction();

            $sender = OrderShopAddress::where('shop_id', $this->payload['shop_id'])->orderByDesc('default')->first();
            if (isset($this->payload['id_store'])) {
                if (is_numeric($this->payload['id_store'])) {
                    $sender = OrderShopAddress::find($this->payload['id_store']);
                }
            }

            if (!$sender) {
                throw new \Exception('Chưa có nơi gửi hàng');
            }
            if (empty($sender->name)) {
                throw new \Exception('Chưa có nơi gửi hàng');
            }
            if ($sender->shop_id !== $this->payload['shop_id']) {
                throw new \Exception('Địa chỉ gửi hàng không chính xác');
            }

            $payload = array(
                'user_type' => $this->payload['user_type'],
                'user_id' => $this->payload['user_id'],
                'shopId' => $this->payload['shop_id'],
                'senderId' => $sender->id,
                'receiverName' => $this->payload['ten_nguoi_nhan'],
                'receiverPhone' => $this->payload['so_dien_thoai'],
                'receiverAddress' => $this->payload['so_nhangongachhem_duongpho'],
                'receiverProvinces' => $this->payload['receiver_province'],
                'receiverDistricts' => $this->payload['receiver_district'],
                'receiverWards' => $this->payload['receiver_ward'],
                'address_refund' => $sender->id,
                'quantity_products' => 1,
                'addProductName' => array($this->payload['ten_hang_hoa']),
                'addProductPrice' => array($this->payload['gia_tri_hang_hoa'] ?: 1),
                'addProductSlg' => array($this->payload['so_luong']),
                'addProductCode' => array(''),
                'weight' => $this->payload['khoi_luong_gram_toi_da_100000_gram'],
                'length' => $this->payload['dai_cm'],
                'width' => $this->payload['rong_cm'],
                'height' => $this->payload['cao_cm'],
                'cod' => $this->payload['tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0'],
                'insurance_value' => $this->payload['gia_tri_hang_hoa'] ?: 0,
                'service_type' => array(1 => 'ghn', 2 => 'ghst', 3 => 'ghvt')[$this->payload['goi_cuoc_1_giao_hang_nhanh_2_giao_hang_sieu_toc_3_giao_hang_vu_tru']],
                'expect_pick' => now(),
                'payfee' => $this->payload['shop_tra_ship'] ? 'payfee_sender' : 'payfee_receiver',
                'client_code' => $this->payload['ma_don_hang_rieng'],
                'note1' => array(1 => 'choxemhang', 2 => 'choxemhangkhongthu', 3 => 'khongchoxemhang')[$this->payload['yeu_cau_don_hang_1_cho_thu_hang_2_cho_xem_khong_thu_3_khong_cho_xem']],
                'note2' => $this->payload['ghi_chu_them']
            );

            $dataRes = $orderServices->crudStore($payload);
            if (!$dataRes->result) {
                throw new \Exception($dataRes->message);
//                throw new \Exception('Lỗi tạo đơn vận chuyển');
            } else {
                $queue = OrderQueue::find($this->payload['queue_id']);
                $queue->status = 1;
                $queue->reason = "";
                $queue->save();
                print_r('=====================Thanh cong===================');
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $queue = OrderQueue::where('id', $this->payload['queue_id'])->update([
                'status' => 2,
                'reason' => $e->getMessage()
            ]);
            print_r('=====================That Bai===================');

        }
    }

    public function failed($exception)
    {
        $exception->getMessage();
        // etc...
    }
}

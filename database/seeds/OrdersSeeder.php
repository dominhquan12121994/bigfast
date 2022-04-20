<?php
/**
 * Copyright (c) 2021. Electric
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Helpers\OrderHelper;
use Spatie\Permission\Models\Role;

use App\Modules\Orders\Constants\OrderConstant;
use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Orders\Models\Entities\Orders;
use App\Modules\Orders\Models\Entities\OrderLog;
use App\Modules\Orders\Models\Entities\OrderExtra;
use App\Modules\Orders\Models\Entities\OrderReceiver;
use App\Modules\Orders\Models\Entities\OrderProduct;
use App\Modules\Orders\Models\Entities\OrderTrace;
use App\Modules\Orders\Models\Entities\OrderFee;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfOrders = 0;
        $faker = Faker::create();

        for($i = 0; $i<$numberOfOrders; $i++){
            $shop_id = rand(1, 10);
            $transport_fee = rand(10, 100) * 1000;
            $insurance_value = rand(10, 100) * 1000;
            $created_date = (int) date('Ymd', strtotime('-'.rand(1, 5).' day'));
            $order = Orders::create([
                'shop_id'           => $shop_id,
                'sender_id'         => $shop_id,
                'refund_id'         => $shop_id,
                'receiver_id'       => rand(1, 10),
                'lading_code'       => $this->generateLandingCode(), // gen unique
                'status'            => config('order.status.default'),
                'status_detail'     => config('order.status.default_detail'),
                'transport_fee'     => $transport_fee,
                'total_fee'         => $transport_fee,
                'cod'               => 0,
                'insurance_value'   => 0,
                'service_type'      => array('ghn', 'ghst', 'ghvt')[rand(0,2)],
                'payfee'            => array('payfee_sender', 'payfee_receiver')[rand(0,1)],
                'weight'            => rand(1, 100),
                'height'            => rand(1, 100),
                'width'             => rand(1, 100),
                'length'            => rand(1, 100),
                'created_date'      => $created_date,
                'last_change_date'  => $created_date
            ]);

//            OrderFee::create([
//                'shop_id'   => $shop_id,
//                'order_id'  => $order->id,
//                'fee_type'  => 'transport',
//                'date'      => $created_date,
//                'value'     => $transport_fee
//            ]);

            for($j = 0; $j<rand(1, 3); $j++) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'name' => $faker->name(),
                    'code' => OrderHelper::generateRandomString(4),
                    'price' => rand(10, 100) * 1000,
                    'quantity' => rand(1, 10)
                ]);
            }

            $expect_pick = date('d-m-Y' . ' 12:00:00', strtotime('+ ' . rand(1, 2) . ' day'));
            $expect_receiver = date('d-m-Y H:i:s', strtotime('+ ' . rand(1, 2) . ' day', strtotime($expect_pick)));

            OrderExtra::create([
                'id'                    => $order->id,
                'client_code'           => OrderHelper::generateRandomString(),
                'note1'                 => array('choxemhang', 'choxemhangkhongthu', 'khongchoxemhang')[rand(0,2)],
                'note2'                 => $faker->sentence(),
                'receiver_name'          => $faker->name(),
                'receiver_phone'         => $faker->tollFreePhoneNumber(),
                'receiver_address'       => $faker->address(),
                'receiver_p_id'          => 1,
                'receiver_d_id'          => 1,
                'receiver_w_id'          => 1,
                'expect_pick'            => $expect_pick,
                'expect_receiver'        => $expect_receiver,
            ]);

            $logOrder = OrderLog::create([
                'order_id'          => $order->id,
                'user_type'         => 'user',
                'user_id'           => rand(1, 10),
                'log_type'          => 'create_order',
                'status'            => 1,
                'status_detail'     => 11,
                'note1'             => $order->receiver->address,
                'note2'             => "",
                'logs'              => json_encode(array()),
                'timer'             => now()
            ]);

//            OrderTrace::create([
//                'order_id'  => $order->id,
//                'status'    => config('order.status.default'),
//                'log_id'    => $logOrder->id,
//                'timer'     => now(),
//                'note'      => 'Lên đơn hàng thành công'
//            ]);
        }
    }

    public function generateLandingCode()
    {
        // BF0102966905659
        $countOrders = Orders::count();
        $timer = date('dm');
        $randInt = rand(100, 999);
        $getNum = 966905659 - $countOrders;
        $prefix = config('order.prefix_lading_code', 'BF');

        $ladingCode = $prefix . $timer . $getNum . $randInt;

        return $ladingCode;
    }
}
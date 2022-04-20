<?php

use Illuminate\Database\Seeder;
use App\Modules\Orders\Models\Entities\OrderService;
use App\Modules\Orders\Models\Entities\OrderSetting;
use App\Modules\Orders\Constants\OrderSettingConstant;

class OrderServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        $dataFill = [
            [
                'alias' => 'ghn',
                'name' => 'Giao hàng nhanh',
                'description' => '',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'alias' => 'ghst',
                'name' => 'Giao hàng siêu tốc',
                'description' => '',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'alias' => 'ghvt',
                'name' => 'Giao hàng vũ trụ',
                'description' => '',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        OrderService::insert($dataFill);

        $route = OrderSettingConstant::route;
        $region = OrderSettingConstant::region;
        $fee_type = OrderSettingConstant::fee_type;
        
        $fillData = [];
        $dataFake = [
            'weight' => [
                'from' => 0,
                'to' => 3000
            ],
            'extra' => 4000,
            'time' => [
                'from' => 1,
                'to' => 2
            ],
        ];
        
        $extra1 = 1000;
        foreach ( $route as $index => $item ) {
            foreach (OrderService::get() as $service) {
                $extra2 = 4000;
                foreach ( $region as $key => $val ) {
                    foreach ($fee_type as $f => $fv) {
                        $dataFake['region'][$key][$f] = $extra1 + $extra2 + 50000;
                    }
                    $extra2 += 5000;
                }
                $fillData[] = [
                    'route' => $index,
                    'service' => $service->alias,
                    'result' => json_encode($dataFake),
                    'disable' => 'off',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                $extra1 += 2000;
            }
        }
        
        OrderSetting::insert($fillData);

        DB::commit();
    }
}
 
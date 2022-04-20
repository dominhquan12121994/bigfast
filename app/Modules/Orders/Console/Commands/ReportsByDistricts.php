<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Modules\Orders\Models\Entities\Orders;
use App\Modules\Orders\Models\Entities\ReportsByZone;

class ReportsByDistricts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:report-by-districts {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Orders Report By Districts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('=============BEGIN============');
        $this->info('Bắt đầu :' . date('H:i:s'));
        try {
            DB::beginTransaction();

            //Lấy danh sách tất cả các đơn
            $date = $this->argument('date') ? $this->argument('date') : date('Ymd');
            $orders = Orders::select(DB::raw('orders.shop_id, order_extras.receiver_d_id, orders.created_date, COUNT(order_extras.receiver_d_id) AS countTime'))
            ->where('orders.created_date', (int)$date )
            ->join('order_extras', 'orders.id', '=', 'order_extras.id')
            ->groupBy(['orders.shop_id','order_extras.receiver_d_id','orders.created_date'])
            ->get();

            //Lưu dữ liệu vào bảng order_reports_by_zone
            foreach ($orders as $order) {
                $update = ReportsByZone::updateOrCreate(
                    [
                        'shop_id' => $order->shop_id,
                        'date'    => $order->created_date,
                        'd_id'    => $order->receiver_d_id
                    ],
                    [
                        'count'   => $order->countTime
                    ]
                );
                $this->info('Lưu dữ liệu shop_id ' . $order->shop_id . ' ngày '  . $order->created_date);
                $update->save();
            }  

            $this->info('Kết thúc: ' . date('H:i:s'));
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $this->info($message);
        }

        $this->info('==============END=============');
        return;
    }
}
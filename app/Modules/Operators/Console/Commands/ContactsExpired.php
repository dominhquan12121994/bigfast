<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Operators\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Modules\Operators\Models\Entities\Contacts;

class ContactsExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contacts:expired {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Contacts Expired';

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
            $contacts = Contacts::select('*')
            ->where('expired', 0)
            ->whereNotNull('expired_at')
            ->whereNull('deleted_at')
            ->whereDate('expired_at', date('Y-m-d'))
            ->get();

            //Lưu dữ liệu vào bảng Contacts
            foreach ($contacts as $contact) {
                if ( $contact->status != 2 && time() > strtotime($contact->expired_at) ) {
                    $update = Contacts::where('id', $contact->id)->update(['expired' => 1]);
                    $this->info('Lưu dữ liệu contact_id ' . $contact->id);
                }
                if ( $contact->status == 2 && strtotime($contact->done_at) > strtotime($contact->expired_at) ) {
                    $update = Contacts::where('id', $contact->id)->update(['expired' => 1]);
                    $this->info('Lưu dữ liệu contact_id ' . $contact->id);
                }
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
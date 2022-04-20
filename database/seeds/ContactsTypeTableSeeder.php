<?php

use Illuminate\Database\Seeder;
use App\Modules\Operators\Models\Entities\ContactsType;

class ContactsTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Tư vấn',
                'parent_id' => 0,
                'level' => 0
            ],
            [
                'name' => 'Thay đổi thông tin đơn hàng',
                'parent_id' => 0,
                'level' => 0
            ],
            [
                'name' => 'Khiếu nại',
                'parent_id' => 0,
                'level' => 0
            ],
            [
                'name' => 'Vận đơn',
                'parent_id' => 0,
                'level' => 0
            ],
            [
                'name' => 'Khác',
                'parent_id' => 0,
                'level' => 0
            ],
        ];

        foreach ( $data as $val ) {
            ContactsType::create($val);
        }
    }
}

<?php

use Illuminate\Database\Seeder;
use App\Modules\Operators\Models\Entities\PrintTemplates;

class PrintTemplatesK80Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $val = [];
        $val[] = [
            'page_size' => 'K80',
            'type'      => 'doc',
            'html'      => '
            <hr />
            <div>
            <p>{__LOGO_DOANH_NGHIEP__}</p>

            <p>M&atilde; đơn h&agrave;ng:&nbsp;{__MA_DH__}</p>

            <p>{__MA_VACH_DH__}</p>

            <p>{__CLIENT_CODE__}</p>

            <p>Địa chỉ:&nbsp;{__QUAN_HUYEN_NGUOI_NHAN__}</p>

            <p>THU COD:&nbsp;{__TONG_THU__}</p>
            </div>
            ',
        ];

        PrintTemplates::insert($val);
    }
}

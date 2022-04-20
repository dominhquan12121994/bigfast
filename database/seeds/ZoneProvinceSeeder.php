<?php

use Illuminate\Database\Seeder;
use App\Helpers\StringHelper;
use App\Modules\Operators\Models\Entities\ZoneProvinces;

class ZoneProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrTmp = array();
        $arrData1 = array();
        $path = storage_path() . "/json/wards.json";
        $jsonData = json_decode(file_get_contents($path), true);

        foreach ($jsonData as $data) {
            if (isset($data['wc'])) {
                $data['pc'] = (int) $data['pc'];
                $data['dc'] = (int) $data['dc'];
                $data['wc'] = (int) $data['wc'];
                if ($data['pc'] <= 37) {
                    $data['zone'] = 'bac';
                } elseif ($data['pc'] <= 68) {
                    $data['zone'] = 'trung';
                } else {
                    $data['zone'] = 'nam';
                }
                if (!in_array($data['pc'], $arrTmp)) {
                    $arrTmp[] = $data['pc'];
                    $short_name = $data['p'];
                    $arrAliasClear = array('Tỉnh', 'Thành phố', 'Quận', 'Huyện', 'Thị xã', 'Xã', 'Phường', 'Thị trấn nông trường', 'Thị trấn', 'Thị trấn NT');
                    foreach ($arrAliasClear as $txtClear) {
                        $ptn = "/^" . $txtClear . "/";  // Regex
                        preg_match($ptn, $data['p'], $matches);
                        if (!empty($matches)) {
                            $short_name = preg_replace($ptn, '', $data['p']);
                            break;
                        }
                    }
                    $arrData1[] = array(
                        'code' => $data['pc'],
                        'alias' => StringHelper::vn_to_alias_zone($data['p']),
                        'name' => $data['p'],
                        'zone' => $data['zone'],
                        'short_name' => trim($short_name),
                    );
                }
            }
        }

        ZoneProvinces::insert($arrData1);
    }
}

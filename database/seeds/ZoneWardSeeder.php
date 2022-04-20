<?php
/**
 * Copyright (c) 2020. Electric
 */

use Illuminate\Database\Seeder;
use App\Helpers\StringHelper;
use App\Modules\Operators\Models\Entities\ZoneProvinces;
use App\Modules\Operators\Models\Entities\ZoneDistricts;
use App\Modules\Operators\Models\Entities\ZoneWards;

class ZoneWardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrTmp = array();
        $arrData3 = array();
        $path = storage_path() . "/json/wards.json";
        $jsonData = json_decode(file_get_contents($path), true);

        foreach ($jsonData as $data) {
            if (isset($data['wc'])) {
                $data['pc'] = (int)$data['pc'];
                $data['dc'] = (int)$data['dc'];
                $data['wc'] = (int)$data['wc'];
                if (!in_array($data['wc'], $arrTmp)) {
                    $arrTmp[] = $data['wc'];
                    $province = ZoneProvinces::where('code', $data['pc'])->first();
                    $district = ZoneDistricts::where('code', $data['dc'])->first();
                    if ($province && $district) {
                        $short_name = $data['w'];
                        $arrAliasClear = array('Tỉnh', 'Thành phố', 'Quận', 'Huyện', 'Thị xã', 'Xã', 'Phường', 'Thị trấn nông trường', 'Thị trấn', 'Thị trấn NT');
                        foreach ($arrAliasClear as $txtClear) {
                            $ptn = "/^" . $txtClear . "/";  // Regex
                            preg_match($ptn, $data['w'], $matches);
                            if (!empty($matches)) {
                                $short_name = preg_replace($ptn, '', $data['w']);
                                break;
                            }
                        }
                        $arrData3[] = array(
                            'p_id' => $province->id,
                            'd_id' => $district->id,
                            'code' => $data['wc'],
                            'alias' => StringHelper::vn_to_alias_zone($data['w']),
                            'name' => $data['w'],
                            'short_name' => trim($short_name),
                        );
                    }
                }
            }
        }

        $arrData4 = array_chunk($arrData3, 1000);
        foreach ($arrData4 as $arrData) {
            ZoneWards::insert($arrData);
        }
    }
}

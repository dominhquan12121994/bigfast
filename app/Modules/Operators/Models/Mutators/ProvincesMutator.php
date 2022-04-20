<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class ProvincesMutator
 * @package App\Modules\Operators\Models\Mutator
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Operators\Models\Mutators;

trait ProvincesMutator
{
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = normalizer_normalize($value);
    }

    public function setAliasAttribute($value)
    {
        $this->attributes['alias'] = normalizer_normalize($value);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = normalizer_normalize($value);
    }

    public function setShortNameAttribute($value)
    {
        $newStr = normalizer_normalize($value);
        $arrAliasClear = array('Tỉnh', 'Thành phố', 'Quận', 'Huyện', 'Thị xã', 'Xã', 'Phường', 'Thị trấn nông trường', 'Thị trấn', 'Thị trấn NT');
        foreach ($arrAliasClear as $txtClear) {
            $ptn = "/^" . $txtClear . "/";  // Regex
            preg_match($ptn, $value, $matches);
            if (!empty($matches)) {
                $newStr = preg_replace($ptn, '', $value);
                break;
            }
        }
        $this->attributes['short_name'] = trim($newStr);
    }
}

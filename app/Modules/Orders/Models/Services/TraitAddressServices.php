<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Services;

use Illuminate\Support\Facades\DB;

trait TraitAddressServices
{
    public static function getUniform($payload = array()) {
        $result = DB::table($payload['table'])->whereRaw('MATCH(' . $payload['column'] . ')
                AGAINST("' . $payload['keyword'] . '" IN NATURAL LANGUAGE MODE)')->limit(10)->get();
        return $result;
    }
}

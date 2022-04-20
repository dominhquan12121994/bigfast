<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Helpers;

class OrderHelper
{
    public static function generateRandomString($length = 8) {
        $characters = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}

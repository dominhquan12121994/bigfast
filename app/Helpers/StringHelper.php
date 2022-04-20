<?php
namespace App\Helpers;

class StringHelper
{
    public static function vn_to_str($str)
    {
        $str = normalizer_normalize($str);
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
            ''  => "'"
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = preg_replace('/[^A-Za-z0-9]/', '_', $str);
        $str = str_replace('____', '_', trim($str));
        $str = str_replace('___', '_', trim($str));
        $str = str_replace('__', '_', trim($str));
        $str = strtolower($str);

        if (substr($str, -1) === '_') $str = substr($str, 0, -1);
        return $str;
    }

//    Cấp tỉnh: Tỉnh/ Thành phố trực thuộc trung ương
//    Cấp huyện: Quận/ Huyện/ Thị xã/ Thành phố thuộc tỉnh/ Thành phố thuộc thành phố trực thuộc trung ương
//    Cấp xã: Xã/ Phường/ Thị trấn.
    public static function vn_to_alias_zone($str)
    {
        $str = normalizer_normalize($str);
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
            ''  => "'"
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = preg_replace('/[^A-Za-z0-9]/', ' ', $str);
        $str = strtolower($str);

        $newStr = $str;
        $arrAliasClear = array('tinh', 'thanhpho', 'tp', 'quan', 'huyen', 'thixa', 'xa', 'phuong', 'thitrannongtruong', 'thitrannt', 'thitran');
        foreach ($arrAliasClear as $txtClear) {
            $ptn = "/^" . $txtClear . "/";  // Regex
            preg_match($ptn, $str, $matches);
            if (!empty($matches)) {
                $newStr = preg_replace($ptn, '', $str);
                break;
            }
        }

        return trim($newStr);
    }

    public static function hiddenText($text, $type = null) {
        if ( $type == 'phone' ) {
            $hiddenText = substr($text, 0, -4);
        } elseif($type == 'address') {
            $numberHidden = strpos($text,",");
            $hiddenText = substr($text, 0, $numberHidden);
        } 
        else {
            $numberHidden = strrpos($text," ");
            $hiddenText = substr($text, 0, $numberHidden);
        }
        $newText = str_replace($hiddenText, '*******', $text);

		return $newText;
    }
}

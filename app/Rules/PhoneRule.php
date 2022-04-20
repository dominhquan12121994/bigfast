<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneRule implements Rule
{
    protected $_prefixMobile = array('03', '05', '07', '08', '09');
    protected $_prefixPhone3 = array('028','024');
    protected $_prefixPhone4 = array('0299','0297','0296','0294','0293','0292','0291','0290','0277','0276','0275','0274','0273','0272','0271','0270','0269','0263','0262','0261','0260','0259','0258','0257','0256','0255','0254','0252','0251','0239','0238','0237','0236','0235','0234','0233','0232','0229','0228','0227','0226','0225','0222','0221','0220','0219','0218','0216','0215','0214','0213','0212','0211','0210','0209','0208','0207','0206','0205','0204','0203');

    public function passes($attribute, $value)
    {
        $value = preg_replace('/[^0-9]/', '', $value);
        if (substr($value, 0, 1) !== '0') $value = '0' . $value;
        $strRule1 = implode('|', $this->_prefixMobile);
        $strRule2 = implode('|', $this->_prefixPhone3);
        $strRule3 = implode('|', $this->_prefixPhone4);
        return preg_match('/^(' . $strRule1 . ')[0-9]{8}$/', $value) || preg_match('/^(' . $strRule2 . ')[0-9]{8}$/', $value) || preg_match('/^(' . $strRule3 . ')[0-9]{7}$/', $value);
    }

    public function message()
    {
        return 'Số điện thoại không hợp lệ';
    }
}

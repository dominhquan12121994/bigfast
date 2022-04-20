<?php
/**
 * Created by PhpStorm.
 * @author Electric <huydien.it@gmail.com>
 * Date: 27/4/2020
 */

namespace App\Rules;



use Illuminate\Contracts\Validation\Rule;

class PasswordRule implements Rule
{
    public function passes($attribute, $password){
        return preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9_!@#$%&]{8,32})$/', $password);
    }

    public function message(){
        return 'Mật khẩu có độ dài từ 8 đến 32 ký tự, có ít nhất 1 chữ cái và 1 chữ số';
    }
}

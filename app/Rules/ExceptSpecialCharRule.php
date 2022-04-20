<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExceptSpecialCharRule implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match('/^[^\`\~\!\@\#\$\%\^\&\*\;\:\/\?]+$/', $value);
    }

    public function message()
    {
        return 'Trường :attribute vui lòng không nhập ký tự đặc biệt';
    }
}

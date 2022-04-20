<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Web\Auth;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Admin\AbstractAdminController;

use App\Rules\PasswordRule;
use App\Modules\Systems\Events\CreateLogEvents;

class ChangePasswordController extends AbstractAdminController
{
    public function changePassword() 
    {
        return view('Shops::auth.passwords.change');
    }

    public function updatePassword(Request $request) 
    {
        $type = 'shops';
        if (Auth::guard('shop')->check()) {
            $user = auth('shop')->user();
        }
        if (Auth::guard('shopStaff')->check()) {
            $user = auth('shopStaff')->user();
            $type = 'shop_staff';
        }

        $validator = Validator::make($request->all(), [
            'password_old' => 'required',
            'password' => array('string', 'confirmed', 'different:password_old', new PasswordRule),
        ], [
            'password.different' => 'Mật khẩu mới giống mật khẩu cũ',
            'password.confirmed' => 'Mật khẩu xác nhận không giống !',
        ]);

        if ($validator->fails()) {
            \Func::setToast('Thất bại', 'Cập nhật mật khẩu thất bại', 'error');
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        if (Hash::check($request->password_old, $user->password)) { 
            $user->fill([
                'password' => $request->password
            ])->save();

            //Lưu log
            event(new CreateLogEvents([], $type, 'change_password'));
         
            \Func::setToast('Thành công', 'Cập nhật thành công mật khẩu', 'notice');
            return redirect('/');
         
         } else {
            \Func::setToast('Thất bại', 'Cập nhật mật khẩu thất bại', 'error');
            return redirect()->back()->withInput()->withErrors('Mật khẩu cũ không chính xác');
        }
    }
}

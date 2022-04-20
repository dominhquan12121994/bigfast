<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Controllers\Api;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Validator;

use App\Modules\Systems\Constants\StatusCodeConstant;
use App\Http\Controllers\Api\AbstractApiController;

use App\Modules\Systems\Models\Entities\DeviceToken;
use App\Modules\Systems\Models\Entities\User;
use App\Modules\Shops\Events\LoginEvent;
use App\Modules\Systems\Events\CreateLogEvents;
use App\Rules\PasswordRule;

class AuthController extends AbstractApiController
{
    /**
     * Create shop
     *
     * @param  [string] name
     * @param  [string] phone
     * @param  [string] email
     * @param  [string] address
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:system_users',
            'email' => 'required|string|email|max:255|unique:system_users',
            'address' => 'string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $request['password']=Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());
        $token = $user->createToken('Admin-Api')->accessToken;
        $response = ['token' => $token];
        return response($response, 200);
    }

    /**
     * Login shop and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($fieldType, $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if (config('auth.one_device')) {
                    foreach ($user->tokens as $token) {
                        if ($token->name === 'Admin-Api')
                            $token->revoke();
                    }
                }

                $tokenResult = $user->createToken('Admin-Api', ['admin']);
                $token = $tokenResult->token;
                if ($request->remember_me)
                    $token->expires_at = Carbon::now()->addWeeks(1);

                    //Lưu log
                event(new CreateLogEvents([], 'users', 'users_login'));
                $token->save();

                if ($request->has('device_token')) {
                    if ($device_token = $request->input('device_token')) {
                        if (strlen($device_token) === 41 || strlen($device_token) === 163) {
                            DeviceToken::create(array(
                                'user_type' => 'admin',
                                'user_id' => $user->id,
                                'device_type' => 'app',
                                'device_token' => $device_token
                            ));
                            $user->device_token = $device_token;
                            if (config('auth.one_device')) {
                                event(new LoginEvent($user->id, 'admin', 'app', $device_token));
                            }
                        }
                    }
                }

                $onTime = false;
                $timeworks = config('auth.time_work');
                $now = time();
                foreach ($timeworks as $timework) {
                    if ($now > strtotime(date('Y/m/d') . $timework['begin']) && $now < strtotime(date('Y/m/d') . $timework['end'])) {
                        $onTime = true;
                        break;
                    }
                }

                return $this->_responseSuccess('Success', [
                    'online' => $onTime ? $user->online : 0,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->toArray(),
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => date('Y/m/d H:i:s', strtotime($tokenResult->token->expires_at)),
                    'current_time' => date('Y/m/d H:i:s'),
                    'timeworks' => $timeworks
                ]);
            } else {
//                return response()->json([
//                    'message' => 'Password mismatch'
//                ], 401);
                return $this->_responseError('Sai mật khẩu', array(), StatusCodeConstant::HTTP_UNAUTHORIZED);
            }
        } else {
//            return response()->json([
//                'message' => 'User does not exist'
//            ], 401);
            return $this->_responseError('Tài khoản không tồn tại', array(), StatusCodeConstant::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Logout shop (Revoke the token)
     *
     * @return [string] message
     */
    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();

        //Lưu log
        event(new CreateLogEvents([], 'users', 'users_logout'));
        DeviceToken::where('user_type', 'admin')
            ->where('user_id', $request->user()->id)
            ->where('device_type', 'app')->delete();

        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function updatePassword(Request $request) 
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'password_old' => 'required',
            'password' => array('string', 'confirmed', 'different:password_old', new PasswordRule),
        ], [
            'password.different' => 'Mật khẩu mới giống mật khẩu cũ',
            'password.confirmed' => 'Mật khẩu xác nhận không giống !',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        if (Hash::check($request->password_old, $user->password)) {
            $user->fill([
                'password' => Hash::make($request->password)
            ])->save();

            $token = $request->user()->token();
            $token->revoke();

            //Lưu log
            event(new CreateLogEvents([], 'users', 'change_password'));
            DeviceToken::where('user_type', 'admin')
                ->where('user_id', $request->user()->id)
                ->where('device_type', 'app')->delete();
         
            return $this->_responseSuccess('Bạn đã đổi mật khẩu thành công');
         
         } else {
            return $this->_responseError('Mật khẩu cũ không chính xác');
        }
    }

    /**
     * Get the authenticated shop
     *
     * @return [json] shop object
     */
    public function get(Request $request)
    {
        $user = $request->user();

        $onTime = false;
        $timeworks = config('auth.time_work');
        $now = time();
        foreach ($timeworks as $timework) {
            if ($now > strtotime(date('Y/m/d') . $timework['begin']) && $now < strtotime(date('Y/m/d') . $timework['end'])) {
                $onTime = true;
                break;
            }
        }

        return $this->_responseSuccess('Success', [
            'online' => $onTime ? $user->online : 0,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'role' => $user->getRoleNames()->toArray(),
            'current_time' => date('d/m/Y H:i:s'),
            'timeworks' => $timeworks
        ]);
    }

    public function joinWork(Request $request)
    {
        $user = $request->user();
        $user->online = $user->online ? 0 : 1;
        $user->save();

        return $this->_responseSuccess('Success', $user);
    }
}

<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Api;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Validator;

use App\Rules\PasswordRule;
use App\Rules\PhoneRule;

use App\Http\Controllers\Api\AbstractApiController;
use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Orders\Models\Entities\OrderShopAddress;
use App\Modules\Systems\Events\CreateLogEvents;

use App\Modules\Systems\Models\Entities\DeviceToken;
use App\Modules\Shops\Events\LoginEvent;

use App\Modules\Orders\Models\Services\ShopServices;

class AuthController extends AbstractApiController
{
    protected $_shopServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ShopServices $shopServices)
    {
        parent::__construct();
        $this->_shopServices = $shopServices;
    }

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
            'phone' => array('required', 'unique:order_shops', new PhoneRule()),
            'email' => 'required|email|unique:order_shops|max:255',
            'name' => 'required|max:255',
            'password' => array('required', new PasswordRule(), 'confirmed' ),
        ],
        [
            'required' => ':attribute là bắt buộc',
            'unique' => ':attribute đã tồn tại',
            'email.email' => 'Địa chỉ email sai định dạng',
            'password.confirmed' => 'Xác nhận mật khẩu không đúng',
        ],
        [
            'name' => 'Tên shop',
            'phone' => 'Số điện thoại',
            'email' => 'Địa chỉ email',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 401);
        }

        $shopName = preg_replace('!\s+!', ' ', trim($request->input('name')));
        $request->merge(array(
            'name' => $shopName
        ));

        $dataRes = $this->_shopServices->crudStore($request);
        if (!$dataRes->result) {
            return response()->json([
                'message' => $dataRes->error
            ], 401);
        }

        $shop = OrderShop::where('email', $request->input('email') )->first();
        if ($shop) {
            $tokenResult = $shop->createToken('Shop-Api', ['shop']);
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();
        } else {
            return response()->json([
                'message' => 'Đăng ký tài khoản thất bại!'
            ], 401);
        }

        return response()->json([
            'message' => 'Đăng ký tài khoản thành công!',
            'full_information' => false,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ], 200);
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
        $login = request()->input('email');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        request()->merge([$fieldType => $login]);

        $validateMail = '';
        $full_information = true;
        if ($fieldType === 'email') {
            $validateMail = '|email';
        }

        $request->validate([
            'email' => 'required|string' . $validateMail,
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

//        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
//
//        request()->merge([$fieldType => $login]);

        $shop = OrderShop::where($fieldType, $request->email)->first();
        if ($shop) {
            if (Hash::check($request->password, $shop->password)) {
                $tokenResult = $shop->createToken('Shop-Api', ['shop']);
                $token = $tokenResult->token;
                if ($request->remember_me)
                    $token->expires_at = Carbon::now()->addWeeks(1);

                //Lưu log 
                event(new CreateLogEvents([], 'shops', 'shop_login'));

                $token->save();

                if ($request->has('device_token')) {
                    if ($device_token = $request->input('device_token')) {
                        if (strlen($device_token) === 41 || strlen($device_token) === 163) {
                            DeviceToken::create(array(
                                'user_type' => 'shop',
                                'user_id' => $shop->id,
                                'device_type' => 'app',
                                'device_token' => $device_token
                            ));
                            $shop->device_token = $device_token;
                            if (config('auth.one_device')) {
                                event(new LoginEvent($shop->id, 'shop', 'app', $device_token));
                            }
                        }
                    }
                }

                //Check info shop
                $roles = $shop->getRoleNames()[0];
                if ($roles === 'shop') {
                    $shop_id = $shop->id;
                    $address = OrderShopAddress::where('shop_id', $shop_id)->first();
                    if (!$address) {
                        $full_information = false;
                    }
                }

                return response()->json([
                    'full_information' => $full_information,
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ]);
            } else {
                return response()->json([
                    'message' => 'Tài khoản đăng nhập sai mật khẩu'
                ], 401);
            }
        } else {
            return response()->json([
                'message' => 'Tài khoản cửa hàng không tồn tại'
            ], 401);
        }
    }

    /**
     * Logout shop (Revoke the token)
     *
     * @return [string] message
     */
    public function logout (Request $request) {
        $token = $request->user()->token();

        //Lưu log 
        event(new CreateLogEvents([], 'shops', 'shop_logout'));

        $token->revoke();

        DeviceToken::where('user_type', 'shop')
            ->where('user_id', $request->user()->id)
            ->where('device_type', 'app')->delete();

        $response = ['message' => 'Tài khoản đã đăng xuất thành công!'];
        return response($response, 200);
    }

    public function updatePassword(Request $request) 
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'password_old' => 'required',
            'password' => array('string', new PasswordRule, 'confirmed', 'different:password_old'),
        ], [
            'password.different' => 'Mật khẩu mới giống mật khẩu cũ',
            'password.confirmed' => 'Mật khẩu xác nhận không giống !',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        if (Hash::check($request->password_old, $user->password)) { 
            $user->fill([
                'password' => $request->password
            ])->save();

            $token = $request->user()->token();
            $token->revoke();

            //Lưu log
            event(new CreateLogEvents([], 'shops', 'change_password'));
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
        return response()->json($request->user());
    }
}
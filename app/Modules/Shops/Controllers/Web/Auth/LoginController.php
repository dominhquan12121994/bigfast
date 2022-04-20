<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Web\Auth;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Http\Controllers\Web\AbstractWebController;
use App\Modules\Orders\Constants\OrderConstant;
use App\Modules\Orders\Constants\ShopConstant;
use App\Modules\Systems\Models\Entities\DeviceToken;
use App\Modules\Shops\Events\LoginEvent;
use App\Modules\Systems\Events\CreateLogEvents;

class LoginController extends AbstractWebController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/orders';

    protected $username;

    protected $staff = false;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:shop')->except('logout');

        $this->username = $this->findUsername();
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        $branchs = ShopConstant::branchs;
        $scales = ShopConstant::scales;
        $purposes = ShopConstant::purposes;

        return view('Shops::auth.login', array(
            'tab_active' => 'login',
            'branchs' => $branchs,
            'scales' => $scales,
            'purposes' => $purposes,
        ));
    }

    public function findUsername()
    {
        $login = request()->input('email');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    public function login(Request $request)
    {
        if ($request->has('checkStaff')) {
            $this->staff = true;
            $this->redirectTo = '/order-staff';
        }

        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        $branchs = ShopConstant::branchs;
        $scales = ShopConstant::scales;
        $purposes = ShopConstant::purposes;

        session()->flashInput($request->input());
        return view('Shops::auth.login', array(
            'tab_active' => 'login',
            'branchs' => $branchs,
            'scales' => $scales,
            'purposes' => $purposes,
        ));
//        return redirect($this->redirectTo);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $tokenResult = $user->createToken('Shop-Web', ['shop']);
        $token = $tokenResult->token;
        $token->save();

        if ($request->has('device_token')) {
            if ($device_token = $request->input('device_token')) {
                if (strlen($device_token) === 41 || strlen($device_token) === 163) {
                    DeviceToken::create(array(
                        'user_type' => 'shop',
                        'user_id' => $user->id,
                        'device_type' => 'web',
                        'device_token' => $device_token
                    ));
                    $user->device_token = $device_token;
                    if (config('auth.one_device')) {
                        event(new LoginEvent($user->id, 'shop', 'web', $device_token));
                    }
                }
            }
        }

        //Lưu log
        event(new CreateLogEvents([], 'shops', 'shop_login'));
//        Cookie::queue('token', $tokenResult->accessToken, 525600); // 1 year

        $user->passport_token = $tokenResult->accessToken;
        $user->save();

        if (config('auth.one_device')) {
//            Auth::logoutOtherDevices($request->input('password'));
        }
    }

    public function logout(Request $request)
    {
//        Cookie::queue('token', null, -1);

        DeviceToken::where('user_type', 'shop')
            ->where('user_id', $this->guard()->id())
            ->where('device_type', 'web')->delete();

        if (config('auth.one_device')) {
            foreach ($this->guard()->user()->tokens as $token) {
                if ($token->name === 'Shop-Web')
                    $token->revoke();
            }
        }

        //Lưu log
        event(new CreateLogEvents([], 'shops', 'shop_logout'));

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function username()
    {
        return $this->username;
    }

    protected function guard()
    {
        if (Auth::guard('shopStaff')->check() || $this->staff) {
            return Auth::guard('shopStaff');
        } else {
            return Auth::guard('shop');
        }
    }
}

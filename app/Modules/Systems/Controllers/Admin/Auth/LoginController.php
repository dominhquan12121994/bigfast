<?php

namespace App\Modules\Systems\Controllers\Admin\Auth;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
//use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Http\Controllers\Admin\AbstractAdminController;
use App\Modules\Systems\Models\Entities\DeviceToken;
use App\Modules\Shops\Events\LoginEvent;
use App\Modules\Systems\Events\CreateLogEvents;

class LoginController extends AbstractAdminController
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
    protected $redirectTo = '/admin/orders';

    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');

        $this->username = $this->findUsername();
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        try {
            $accessToken = Cookie::get('token');
            $client = new \GuzzleHttp\Client(); //route('api.user.get')
            $res = $client->request('GET', 'http://bigfast.local/api/user/get', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$accessToken
                ]
            ]);
            if ($res->getStatusCode() === 200) {
                $result = json_decode($res->getBody());
                $user_id = $result->data->id;
                Auth::guard('admin')->loginUsingId($user_id);
                return redirect('/admin');
            }
        } catch (\Throwable $e) { }

        return view('Systems::auth.login');
    }

    public function findUsername()
    {
        $login = request()->input('email');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        request()->merge([$fieldType => $login]);

        return $fieldType;
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
        $tokenResult = $user->createToken('Admin-Web', ['admin']);
        $token = $tokenResult->token;
        $token->save();

        if ($request->has('device_token')) {
            if ($device_token = $request->input('device_token')) {
                if (strlen($device_token) === 41 || strlen($device_token) === 163) {
                    DeviceToken::create(array(
                        'user_type' => 'admin',
                        'user_id' => $user->id,
                        'device_type' => 'web',
                        'device_token' => $device_token
                    ));
                    $user->device_token = $device_token;
                    if (config('auth.one_device')) {
                        event(new LoginEvent($user->id, 'admin', 'web', $device_token));
                    }
                }
            }
        }

        //Lưu log 
        event(new CreateLogEvents([], 'users', 'users_login'));
        Cookie::queue('token', $tokenResult->accessToken, 525600); // 1 year

        $user->passport_token = $tokenResult->accessToken;
        $user->save();

        if (config('auth.one_device')) {
//            Auth::logoutOtherDevices($request->input('password'));
        }
    }

    public function logout(Request $request)
    {
        Cookie::queue('token', null, -1);

        DeviceToken::where('user_type', 'admin')
            ->where('user_id', $this->guard()->id())
            ->where('device_type', 'web')->delete();

        if (config('auth.one_device')) {
            foreach ($this->guard()->user()->tokens as $token) {
                if ($token->name === 'Admin-Web')
                    $token->revoke();
            }
        }

        //Lưu log 
        event(new CreateLogEvents([], 'users', 'users_logout'));

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function username()
    {
        return $this->username;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $messageBag = new MessageBag;
        $messageBag->add('error', 'Tài khoản không chính xác.');

        return redirect()->back()->withErrors($messageBag)->withInput();
    }
}

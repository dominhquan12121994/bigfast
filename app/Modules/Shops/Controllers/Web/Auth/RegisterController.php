<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Web\Auth;

//use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\AbstractWebController;
use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Orders\Models\Entities\OrderShopBank;
use App\Modules\Orders\Models\Entities\OrderShopAddress;

use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

use App\Rules\PhoneRule;

class RegisterController extends AbstractWebController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/orders';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:shop')->except('logout');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('Shops::auth.login', array('tab_active' => 'register'));
    }

    protected function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:order_shops'],
            'phone' =>  array('required', 'string', 'unique:order_shops', new PhoneRule()),
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        event(new Registered($shop = $this->create($request->all())));

        $this->guard()->login($shop);

        return redirect($this->redirectPath());
    }

    protected function create(array $data)
    {
        $token = md5(config('app.name').'-'.$data['phone'].'-'.$data['email'].'-'.date('Y'));
        $token = substr_replace($token, '-', 6, 0);
        $token = substr_replace($token, '-', 13, 0);
        $token = substr_replace($token, '-', 21, 0);
        $token = substr_replace($token, '-', 29, 0);
        $shop =  OrderShop::create([
            'api_token' => $token,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'menuroles' => 'shop',
            'password' => $data['password'],
        ]);
        $role = Role::where('name', '=', 'shop')->first();
        $shop->assignRole($role);

        $branch = '';
        $scale = '';
        $purpose = '';
        if ( isset($data['branch']) ) {
            $branch = implode(',', $data['branch']);
        }
        if ( isset($data['scale']) ) {
            $scale = $data['scale'];
        }
        if ( isset($data['purpose']) ) {
            $purpose = $data['purpose'];
        }

        OrderShopBank::create([
            'id' => $shop->id,
            'bank_name' => '',
            'bank_branch' => '',
            'stk_name' => '',
            'stk' => '',
            'cycle_cod' => 'friday',
            'cycle_cod_day' => 7,
            'date_reconcile' => date('Y-m-d', strtotime('-1 day')),
            'scale' => $scale,
            'purpose' => $purpose,
            'branch' => $branch,
        ]);

        OrderShopAddress::create([
            'shop_id' => $shop->id,
            'p_id' => 1,
            'd_id' => 1,
            'w_id' => 1,
            'type' => 'send',
            'name' => '',
            'phone' => '',
            'address' => '',
        ]);

        return $shop;
    }

    protected function guard()
    {
        return Auth::guard('shop');
    }
}

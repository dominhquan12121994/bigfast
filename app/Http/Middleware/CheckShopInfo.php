<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Http\Middleware;

use Closure;
use Session;
use Validator;

use App\Modules\Orders\Models\Entities\OrderShopBank;
use App\Modules\Orders\Models\Entities\OrderShopAddress;

class CheckShopInfo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $shop = auth('shop')->user();
        if ($shop) {
            $roles = $shop->getRoleNames()[0];
            if ($roles === 'shop') {
                $shop_id = $shop->id;
                $address = OrderShopAddress::where('shop_id', $shop_id)->first();
				if (!$address) {
					Session::flash('message', "Bạn cần cập nhật đầy đủ thông tin có dấu * trước khi sử dụng dịch vụ của chúng tôi");
                    return redirect()->route('shop.profile.edit');
				}
                if (in_array('', $address->toArray())) {
                    Session::flash('message', "Bạn cần cập nhật đầy đủ thông tin có dấu * trước khi sử dụng dịch vụ của chúng tôi");
                    return redirect()->route('shop.profile.edit');
                }
            }
        }
        return $next($request);
    }
}

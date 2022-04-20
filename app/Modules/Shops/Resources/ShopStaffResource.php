<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Resources;

use Illuminate\Support\Collection;
use App\Http\Resources\AbstractResource;

use App\Modules\Operators\Constants\ContactsConstant;
use App\Modules\Systems\Constants\PermissionConstant;

/**
 * Class ContactResource
 * package App\Modules\Transport\Resources
 * author HuyDien <huydien.itgmail.com>
 */
class ShopStaffResource extends AbstractResource
{
    /**
     * @param $request
     * @return array
     * @author HuyDien <huydien.it@gmail.com>
     */
    public function toArray($request)
    {
        if ($this->resource instanceof Collection) {
            return $this->resource->map(function($item){
                return array(
                    "id" => $item->id,
                    "phone" => $item->phone,
                    "email" => $item->email,
                    "name" => $item->name,
                    "role" => collect($item->getRoleNames())->transform(function($item) {
                        return PermissionConstant::rolesShop[$item]['name'] ?? $item;
                    })
                );
            });
        }
    }
}
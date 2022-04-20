<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Resources;

use Illuminate\Support\Collection;
use App\Http\Resources\AbstractResource;

use App\Modules\Orders\Constants\ShopConstant;

/**
 * Class WardsResource
 * @package App\Modules\Transport\Resources
 * @author HuyDien <huydien.it@gmail.com>
 */
class ShopInfoResource extends AbstractResource
{
    /**
     * @param $request
     * @return array
     * @author HuyDien <huydien.it@gmail.com>
     */
    public function toArray($request)
    {
        $data = array();
        if (isset($this->resource->shop)) {
            $data['shop'] = $this->resource->shop->toArray();
        }

        if (isset($this->resource->bank)) {
            $data['bank'] = array(
                'bank_name' => $this->resource->bank->bank_name,
                'stk_name' => $this->resource->bank->stk_name,
                'stk' => $this->resource->bank->stk,
                'bank_branch' => $this->resource->bank->bank_branch,
                'cycle_cod' => $this->resource->bank->cycle_cod,
                'purpose' => ShopConstant::purposes[$this->resource->bank->purpose] ?? $this->resource->bank->purpose,
                'scale' => ShopConstant::scales[$this->resource->bank->scale] ?? $this->resource->bank->scale,
                'branch' => $this->resource->bank->branch !== '' ? explode(',', $this->resource->bank->branch) : []
            );
        }

        if (isset($this->resource->address)) {
            $data['address'] = $this->resource->address->transform(function ($item) {
                return array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'phone' => $item->phone,
                    'address' => $item->address,
                    'p_id' => $item->p_id,
                    'd_id' => $item->d_id,
                    'w_id' => $item->w_id,
                    'default' => $item->default,
                );
            })->toArray();
        }

        if (isset($this->resource->cycle_cod_list)) {
            foreach ( $this->resource->cycle_cod_list as $key => $item ) {
                $data['filter']['cycle_cod'][] = array(
                    'type' => $key,
                    'name' => $item['name'],
                );
            }
        }

        if (isset($this->resource->full_information)) {
            $data['full_information'] = $this->resource->full_information;
        }

        $ary_purposes = [];
        $ary_scales = [];
        $ary_branchs = [];
        $purposes = ShopConstant::purposes;
        $scales = ShopConstant::scales;
        $branchs = ShopConstant::branchs;

        foreach ( $purposes as $key => $item ) {
            $ary_purposes[] = [
                'key' => $key,
                'name' => $item
            ];
        }
        foreach ( $scales as $key => $item ) {
            $ary_scales[] = [
                'key' => $key,
                'name' => $item
            ];
        }
        foreach ( $branchs as $key => $item ) {
            $ary_branchs[] = [
                'key' => $key,
                'name' => $item['name']
            ];
        }

        $data['filter']['purposes'] = $ary_purposes;
        $data['filter']['scales'] = $ary_scales;
        $data['filter']['branchs'] = $ary_branchs;

        return $data;
    }
}
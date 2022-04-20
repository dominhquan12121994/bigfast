<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Resources;

use Illuminate\Support\Collection;
use App\Http\Resources\AbstractResource;

/**
 * Class WardsResource
 * @package App\Modules\Transport\Resources
 * @author HuyDien <huydien.it@gmail.com>
 */
class WardsResource extends AbstractResource
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
                    'id' => $item->id,
                    'name' => $item->name
                );
            });
        }
    }
}

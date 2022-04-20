<?php

/**
 * Class OrdersMutator
 * @package App\Modules\Orders\Models\Mutator
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Mutators;

trait OrdersMutator
{
    public function setLadingCodeAttribute($value)
    {
        $this->attributes['lading_code'] = normalizer_normalize($value);
    }
}

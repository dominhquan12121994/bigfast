<?php
/**
 * Class OrderShopMutator
 * @package App\Modules\Orders\Models\Mutator
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Mutators;

use Illuminate\Support\Facades\Hash;

trait OrderShopMutator
{
    /**
     * Set the shop's password
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = normalizer_normalize($value);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = normalizer_normalize($value);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = normalizer_normalize($value);
    }
}

<?php

namespace App\Models\Entities;

//use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
//use Jenssegers\Mongodb\Eloquent\Model as Model;


abstract class AbstractModel extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

//    public function get($attributeName){
//        return isset($this->attributes[$attributeName]) ? $this->attributes[$attributeName] : null;
//    }
//
//    public function getFriendlyCreatedAt(){
//        return $this->get('created_at') ? $this->get('created_at')->format('d-m-Y H:i:s') : null;
//    }
//
//    public function getFriendlyUpdatedAt(){
//        return $this->get('updated_at') ? $this->get('updated_at')->format('d-m-Y H:i:s') : null;
//    }
}

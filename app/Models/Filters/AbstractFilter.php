<?php

namespace App\Models\Filters;

use Illuminate\Http\Request;

abstract class AbstractFilter
{
    protected $builder;

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function apply($builder){
        $this->builder = $builder;
        foreach ($this->_getAllRequest() as $key => $value){
            if(method_exists($this, $key)){
                call_user_func_array([$this, $key], array_filter([$value]));
            }
        }
        return $this->builder;
    }

    protected function _getAllRequest(){
        return $this->request instanceof Request ? $this->request->all() : $this->request;
    }
}

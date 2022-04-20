<?php
namespace App\Helpers;

class Func
{
    public static function getMicroTime(){
        return microtime(true) * 1000;
    }

    public static function logError($message){
        \Log::error($message);
    }

    public static function setToast($title = 'Thành công', $message = '', $type = 'notice'){
        request()->session()->flash('message', $message);
        request()->session()->flash('title', $title);
        request()->session()->flash('type', $type);
    }
}

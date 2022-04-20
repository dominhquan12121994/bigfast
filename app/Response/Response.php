<?php
namespace App\Response;

use App\Response\Json\Error;
use App\Response\Json\Message;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Str;

class Response{
    const TYPE_JSON = 'json';
    const TYPE_HTML = 'html';

    public function __construct(ResponseFactory $factory)
    {
        $responseType = $this->_getResponseType();
        $responseInstanceForResponseType = $this->_getInstanceForResponseType($responseType);
        $this->_bindMacros($responseInstanceForResponseType, $factory);
    }

    protected function _getInstanceForResponseType($responseType){
        switch ($responseType){
            case self::TYPE_JSON: return array(Error::class, Message::class);
            case self::TYPE_HTML: return array(Error::class, Message::class);
        }
    }

    protected function _getResponseType(){
        if(request()->isJson() || request()->wantsJson() || Str::contains(request()->getRequestUri(), '/api') ){
            return self::TYPE_JSON;
        }
        if(request()->has('__responseType') && is_string(request()->get('__responseType'))){
            return self::TYPE_HTML;
        }
        return self::TYPE_HTML;
    }

    protected function _bindMacros($responseInstances, $factory){
        foreach ($responseInstances as $responseInstance){
            (new $responseInstance)->run($factory);
        }
    }
}

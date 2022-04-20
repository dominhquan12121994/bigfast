<?php
namespace App\Response\Json;

use App\Response\Contracts\ResponseAbstract;
use App\Response\Contracts\ResponseInterface;
use Illuminate\Routing\ResponseFactory;

class Error extends ResponseAbstract implements ResponseInterface{
    public function run(ResponseFactory $factory)
    {
        $factory->macro('responseError', function ($messages = array(), $appendData = array(), $statusCode = 400) use ($factory){
            $messages = is_array($messages) ? $messages : array($messages);
            $firstMessage = reset($messages);
            $response = array(
                'status_code' => $statusCode,
                'success' => false,
                'message' => $firstMessage,
                'errors' => $messages,
                'data' => $appendData
            );

            return $factory->make($response, $statusCode);

        });
    }
}

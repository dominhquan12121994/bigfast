<?php
namespace App\Response\Json;

use App\Response\Contracts\ResponseAbstract;
use App\Response\Contracts\ResponseInterface;
use Illuminate\Routing\ResponseFactory;

class Message extends ResponseAbstract implements ResponseInterface{
    public function run(ResponseFactory $factory)
    {
        $factory->macro('responseMessage', function ($messages = 'Success', $appendData = array(), $statusCode = 200, $success = true) use ($factory){
            $response = array(
                'status_code' => $statusCode,
                'success' => $success,
                'message' => $messages,
                'data' => $appendData
            );

            return $factory->make($response, $statusCode);
        });
    }
}

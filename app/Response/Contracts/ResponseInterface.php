<?php
namespace App\Response\Contracts;

use Illuminate\Routing\ResponseFactory;

interface ResponseInterface{
    public function run(ResponseFactory $factory);
}

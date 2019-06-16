<?php

namespace Test\Example;

use App\Model\Request;
use Tal\ServerResponse;

class ResponseCreator
{
    public function serverTime(): ServerResponse
    {
        return new ServerResponse(200, [
            'Content-Type', 'application/json'
        ], json_encode(date('c')));
    }

    public function request(Request $request): ServerResponse
    {
        return new ServerResponse(200);
    }
}

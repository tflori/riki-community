<?php

namespace Test\Example;

use App\Http\Controller\AbstractController;
use Tal\ServerResponse;

class CustomController extends AbstractController
{
    public static function doSomething(): ServerResponse
    {
        return new ServerResponse(200);
    }
}

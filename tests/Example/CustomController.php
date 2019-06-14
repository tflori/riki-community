<?php

namespace Test\Example;

use App\Http\Controller\AbstractController;
use Psr\Http\Message\ServerRequestInterface;
use Tal\ServerResponse;

class CustomController extends AbstractController
{
    const HELLO_WORLD_HTML = '<!DOCTYPE html><html><body>Hello World!</body></html>';
    const HELLO_WORLD_XML = '<?xml version="1.0" encoding="UTF-8" ?><Message>Hello World!</Message>';
    const HELLO_WORLD_JSON = '"Hello World!"';

    public $request;

    public function helloWorld(ServerRequestInterface $request)
    {
        switch ($this->getPreferredContentType($request, ['text/html', 'application/xml', 'application/json'])) {
            case 'text/html':
                return new ServerResponse(200, [], self::HELLO_WORLD_HTML);
            case 'application/xml':
                return new ServerResponse(200, [], self::HELLO_WORLD_XML);
            case 'application/json':
                return new ServerResponse(200, [], self::HELLO_WORLD_JSON);
        }
        return new ServerResponse(500);
    }

    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public static function doSomething()
    {
        return new ServerResponse(200);
    }
}

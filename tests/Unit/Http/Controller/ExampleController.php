<?php

namespace Test\Unit\Http\Controller;

use App\Http\Controller\AbstractController;
use Tal\ServerResponse;

class ExampleController extends AbstractController
{
    const HELLO_WORLD_HTML = '<!DOCTYPE html><html><body>Hello World!</body></html>';
    const HELLO_WORLD_XML = '<?xml version="1.0" encoding="UTF-8" ?><Message>Hello World!</Message>';
    const HELLO_WORLD_JSON = '"Hello World!"';

    public function helloWorld()
    {
        switch ($this->getPreferredContentType(['text/html', 'application/xml', 'application/json'])) {
            case 'text/html':
                return new ServerResponse(200, [], self::HELLO_WORLD_HTML);
            case 'application/xml':
                return new ServerResponse(200, [], self::HELLO_WORLD_XML);
            case 'application/json':
                return new ServerResponse(200, [], self::HELLO_WORLD_JSON);
        }
    }
}

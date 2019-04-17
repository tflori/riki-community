<?php

namespace Test\Unit\Http\Controller;

use App\Http\Controller\AbstractController;
use Psr\Http\Message\ServerRequestInterface;
use Tal\ServerResponse;

class ExampleController extends AbstractController
{
    const HELLO_WORLD_HTML = '<!DOCTYPE html><html><body>Hello World!</body></html>';
    const HELLO_WORLD_XML = '<?xml version="1.0" encoding="UTF-8" ?><Message>Hello World!</Message>';
    const HELLO_WORLD_JSON = '"Hello World!"';

    public $request;

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
        return new ServerResponse(500);
    }

    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function validate(array $fields, $source = 'query', &$data = [], &$errors = [])
    {
        return parent::validate($fields, $source, $data, $errors);
    }

    public function getQuery(string $key = null, $default = null)
    {
        return parent::getQuery($key, $default);
    }

    public function getPost(string $key = null, $default = null)
    {
        return parent::getPost($key, $default);
    }

    public function getJson(bool $assoc = true, int $depth = 512, int $options = 0)
    {
        return parent::getJson($assoc, $depth, $options);
    }
}

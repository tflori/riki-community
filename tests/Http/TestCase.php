<?php

namespace Test\Http;

use App\Http\HttpKernel;
use Tal\Psr7Extended\ServerResponseInterface;
use Tal\ServerRequest;

abstract class TestCase extends \Test\TestCase
{
    protected function get(string $uri, array $query = [], array $headers = []): ServerResponseInterface
    {
        $request = (new ServerRequest('get', $uri, []))
            ->withQueryParams($query);

        foreach ($headers as $header => $value) {
            $request = $request->withHeader($header, $value);
        }

        $kernel = new HttpKernel();
        return $this->app->run($kernel, $request);
    }
}

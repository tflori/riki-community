<?php

namespace Test\Http;

use App\Http\HttpKernel;
use App\Model\Request;
use Tal\Psr7Extended\ServerResponseInterface;

abstract class TestCase extends \Test\TestCase
{
    protected function get(string $uri, array $query = [], array $headers = []): ServerResponseInterface
    {
        $request = (new Request('get', $uri, []))
            ->withQueryParams($query);

        foreach ($headers as $header => $value) {
            $request = $request->withHeader($header, $value);
        }

        $kernel = new HttpKernel($this->app);
        return $this->app->run($kernel, $request);
    }
}

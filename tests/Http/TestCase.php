<?php

namespace Test\Http;

use App\Http\HttpKernel;
use App\Model\Request;
use function GuzzleHttp\Psr7\stream_for;
use Tal\ServerResponse;

abstract class TestCase extends \Test\TestCase
{
    protected function call(string $method, string $uri, array $query = [], array $headers = []): ServerResponse
    {
        $request = (new Request($method, $uri, []))
            ->withQueryParams($query);

        foreach ($headers as $header => $value) {
            $request = $request->withHeader($header, $value);
        }

        $kernel = new HttpKernel($this->app);
        return $this->app->run($kernel, $request);
    }

    protected function get(string $uri, array $query = [], array $headers = []): ServerResponse
    {
        return $this->call('get', $uri, $query, $headers);
    }

    protected function post(string $uri, array $query = [], $body = null, array $headers = []): ServerResponse
    {
        $request = (new Request('post', $uri))
            ->withQueryParams($query);

        foreach ($headers as $header => $value) {
            $request = $request->withHeader($header, $value);
        }

        if (is_array($body)) {
            $request = $request->withParsedBody($body);
        } elseif (is_string($body)) {
            $request = $request->withBody(stream_for($body));
        } else {
            $request = $request->withBody($body);
        }

        $kernel = new HttpKernel($this->app);
        return $this->app->run($kernel, $request);
    }
}

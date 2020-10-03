<?php

namespace Test\Unit\Service;

use App\Model\Request;
use App\Service\Url;
use Mockery as m;
use Psr\Http\Message\UriInterface;
use Test\TestCase;

class UrlTest extends TestCase
{
    /** @test */
    public function appendsTheParams()
    {
        $path = '/request/path';
        $params = ['search' => 'term1,term2'];
        $url = new Url('http://localhost', null);

        $url = $url->local($path, $params);

        self::assertSame('/request/path?search=term1%2Cterm2', $url);
    }

    /** @test */
    public function prependsTheBasePathFromFallbackUrl()
    {
        $path = '/request/path';
        $url = new Url('http://localhost/community', null);

        $url = $url->local($path);

        self::assertSame('/community/request/path', $url);
    }

    /** @test */
    public function prependsTheBasePathFromRequest()
    {
        $request = m::mock(Request::class);
        $path = '/request/path';
        $url = new Url('http://localhost/community', $request);

        $request->shouldReceive('getBase')->once()->andReturn('/foobar');

        $url = $url->local($path);

        self::assertSame('/foobar/request/path', $url);
    }

    /** @test */
    public function prependsTheSchemeAndHostFromFallbackUrl()
    {
        $path = '/request/path';
        $url = new Url('https://riki.w00tserver.org', null);

        $url = $url->absolute($path);

        self::assertSame('https://riki.w00tserver.org/request/path', $url);
    }

    /** @test */
    public function prependsTheSchemeAndHostFromRequest()
    {
        $request = m::mock(Request::class);
        $uri = m::mock(UriInterface::class);
        $path = '/request/path';
        $url = new Url('http://localhost', $request);

        $request->shouldReceive('getProtocol')->once()->andReturn('https');
        $request->shouldReceive('getUri')->once()->andReturn($uri);
        $uri->shouldReceive('getAuthority')->once()->andReturn('riki.local:44380');
        $request->shouldReceive('getBase')->once()->andReturn('/');

        $url = $url->absolute($path);

        self::assertSame('https://riki.local:44380/request/path', $url);
    }
}

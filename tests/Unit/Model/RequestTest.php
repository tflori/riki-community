<?php

namespace Test\Unit\Model;

use App\Model\Gate;
use App\Model\Request;
use App\Service\ValidatorMessages;
use InvalidArgumentException;
use Mockery as m;
use Tal\ServerRequest;
use Test\Example\CustomController;
use Test\TestCase;
use Verja\Error;
use function GuzzleHttp\Psr7\stream_for;

class RequestTest extends TestCase
{
    /** @test */
    public function preferredContentTypeDefaultsToFirstPossible()
    {
        $request = new Request('GET', '/any/path');

        $preferred = $request->getPreferredContentType(['text/html', 'application/xml', 'application/json']);

        self::assertSame('text/html', $preferred);
    }

    /** @test */
    public function preferredContentTypeFallsBackToFirstPossible()
    {
        $request = (new Request('GET', '/any/path'))
            ->withHeader('Accept', 'image/web');

        $preferred = $request->getPreferredContentType(['text/html', 'application/xml', 'application/json']);

        self::assertSame('text/html', $preferred);
    }

    /** @test */
    public function preferredContentTypeIsMatchedAccept()
    {
        $request = (new Request('GET', '/any/path'))
            ->withHeader('Accept', 'image/web, application/json');

        $preferred = $request->getPreferredContentType(['text/html', 'application/xml', 'application/json']);

        self::assertSame('application/json', $preferred);
    }

    /** @test */
    public function preferredContentTypeGetsSortedByQuality()
    {
        $request = (new Request('GET', '/any/path'))
            ->withHeader('Accept', 'image/web;q=1, application/json;q=0.1, application/xml;q=0.5');

        $preferred = $request->getPreferredContentType(['text/html', 'application/xml', 'application/json']);

        self::assertSame('application/xml', $preferred);
    }

    /** @test */
    public function preferredContentTypeEqualQualityWillRemainOrder()
    {
        $request = (new Request('GET', '/any/path'))
            ->withHeader('Accept', 'application/json;q=0.2, application/xml;q=0.5, text/html;q=0.5');

        $preferred = $request->getPreferredContentType(['text/html', 'application/xml', 'application/json']);

        self::assertSame('application/xml', $preferred);
    }

    /** @test */
    public function preferredContentTypeIgnoresOtherParameters()
    {
        $request = (new Request('GET', '/any/path'))
            ->withHeader('Accept', 'text/html, application/xml;version=1.1');

        $preferred = $request->getPreferredContentType(['text/html', 'application/xml', 'application/json']);

        self::assertSame('text/html', $preferred);
    }

    /** @test */
    public function validateCreatesAGateForAcceptedFieldsWithMessages()
    {
        $this->app->shouldReceive('get')->with('gate', ['foo', 'bar'], ['foo' => 'bar'])->once()->passthru();

        $request = new Request('GET', '/any/path');

        $request->validate(['foo', 'bar'], 'query', ['foo' => 'bar']);
    }

    /** @test */
    public function validateValidatesQueryByDefault()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->with(['foo' => 42])->once();
        $this->app->instance('gate', $gate);

        $request = m::mock(Request::class)->makePartial();
        $request->shouldReceive('getQuery')->with()->once()->andReturn(['foo' => 42]);

        $request->validate(['foo', 'bar']);
    }

    /** @test */
    public function validateValidatesAnyGetter()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->with(['foo' => 42])->once();
        $this->app->instance('gate', $gate);

        $request = m::mock(Request::class)->makePartial();
        $request->shouldReceive('getPost')->with()->once()->andReturn(['foo' => 42]);

        $request->validate(['foo', 'bar'], 'post'); // post -> getPost
    }

    /** @test */
    public function validateValidatesCustomArrays()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->with(['foo' => 42])->once();
        $this->app->instance('gate', $gate);

        $request = m::mock(Request::class)->makePartial();
        $request->shouldNotReceive('getQuery');

        $request->validate(['foo', 'bar'], ['foo' => 42]);
    }

    /** @test */
    public function validateSetsDataOnSuccess()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('getData')->with()->once()->andReturn(['foo' => 42]);
        $this->app->instance('gate', $gate);

        /** @var Request|m\Mock $request */
        $request = m::mock(Request::class)->makePartial();

        list($valid, $data) = $request->validate(['foo'], [], []);

        self::assertSame(42, $data['foo']);
    }

    /** @test */
    public function validateSetsErrorsOnFailure()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->once()->andReturn(false);
        $gate->shouldReceive('getErrorMessages')->with()->once()->andReturn(['foo' => ['What ever...']]);
        $this->app->instance('gate', $gate);

        /** @var Request|m\Mock $request */
        $request = m::mock(Request::class)->makePartial();

        list($valid, $data, $errors) = $request->validate(['foo'], [], []);

        self::assertSame('What ever...', $errors['foo'][0]);
    }

    /** @test */
    public function validateThrowsWhenTheGetterisNotAvailable()
    {
        self::expectException(InvalidArgumentException::class);

        $request = m::mock(Request::class)->makePartial();

        $request->validate([], 'foo');
    }

    /** @test */
    public function getQueryReturnsTheCompleteQuery()
    {
        $request = (new Request('GET', '/any/path'))
            ->withQueryParams(['foo' => 42, 'bar' => 23]);

        $query = $request->getQuery();

        self::assertSame(['foo' => 42, 'bar' => 23], $query);
    }

    /** @test */
    public function getQueryReturnsSpecificParameter()
    {
        $request = (new Request('GET', '/any/path'))
            ->withQueryParams(['foo' => 42, 'bar' => 23]);

        $query = $request->getQuery('foo');

        self::assertSame(42, $query);
    }

    /** @test */
    public function getQueryReturnsTheDefaultValue()
    {
        $request = new Request('GET', '/any/path');

        $query = $request->getQuery('foo', 42);

        self::assertSame(42, $query);
    }

    /** @test */
    public function getPostReturnsTheCompletePost()
    {
        $request = (new Request('POST', '/any/path'))
            ->withParsedBody(['foo' => 42, 'bar' => 23]);

        $post = $request->getPost();

        self::assertSame(['foo' => 42, 'bar' => 23], $post);
    }

    /** @test */
    public function getPostReturnsSpecificParameter()
    {
        $request = (new Request('POST', '/any/path'))
            ->withParsedBody(['foo' => 42, 'bar' => 23]);

        $post = $request->getPost('foo');

        self::assertSame(42, $post);
    }

    /** @test */
    public function getPostReturnsTheDefaultValue()
    {
        $request = new Request('POST', '/any/path');

        $post = $request->getPost('foo', 42);

        self::assertSame(42, $post);
    }

    /** @test */
    public function getJsonReturnsTheRequestBodyJsonDecoded()
    {
        $data = [
            'foo' => 42,
            'bar' => 23,
            'baz' => null,
        ];
        $request = (new Request('POST', '/any/path'))
            ->withBody(stream_for(json_encode($data)));

        self::assertSame($data, $request->getJson());
    }

    /** @test */
    public function getJsonThrowsWhenBodyIsInvalid()
    {
        $request = (new Request('POST', '/any/path'))
            ->withBody(stream_for("{foo:'bar'}")); // this is not json but javascript

        self::expectException(InvalidArgumentException::class);

        $request->getJson();
    }

    /** @test */
    public function getJsonThrowsNotWhenTheJsonIsNull()
    {
        $request = (new Request('POST', '/any/path'))
            ->withBody(stream_for(json_encode(null)));

        self::assertNull($request->getJson());
    }

    /** @test */
    public function getIpReturnsTheRemoteAddr()
    {
        $request = (new Request('GET', '/any/path', [], null, '1.1', [
            'REMOTE_ADDR' => '172.19.0.9',
        ]));

        $ip = $request->getIp();

        self::assertSame('172.19.0.9', $ip);
    }

    /** @dataProvider provideIpHeaders
     * @test */
    public function getIpReturnsTheRealIpByDefault(array $header, string $remoteAddr, string $expected)
    {
        $request = (new Request('GET', '/any/path', $header, null, '1.1', [
            'REMOTE_ADDR' => $remoteAddr,
        ]));

        $ip = $request->getIp();

        self::assertSame($expected, $ip);
    }

    public function provideIpHeaders()
    {
        return [
            [['X-Real-Ip' => '8.8.8.8'], '10.0.0.1', '8.8.8.8'],
            [['X-Forwarded-For' => '8.8.8.8'], '10.0.0.1', '8.8.8.8'],
            [[ // prefers x-real-ip
                'X-Forwarded-For' => '23.0.4.2',
                'X-Real-Ip' => '8.8.8.8',
            ], '10.0.0.1', '8.8.8.8'],
            [[ // uses the last entry
                'X-Forwarded-For' => '23.0.4.2, 8.8.8.8',
            ], '10.0.0.1', '8.8.8.8'],
        ];
    }

    /** @test */
    public function getIpReturnsTheRemoteAddrWhenProxyIsUntrusted()
    {
        /** @var Request|m\MockInterface $request */
        $request = m::mock(Request::class)->makePartial();
        $request->__construct('GET', '/any/path', [
            'X-Forwarded-For' => '23.0.4.2'
        ], null, '1.1', [
            'REMOTE_ADDR' => '8.8.8.8',
        ]);
        $request->shouldReceive('isTrustedForward')->once()->andReturnFalse();

        $ip = $request->getIp();

        self::assertSame('8.8.8.8', $ip);
    }

    /** @test */
    public function getReturnsTheValueFromQuery()
    {
        $request = (new Request('GET', '/any/path'))
            ->withQueryParams(['foo' => 'bar']);

        self::assertSame('bar', $request->get('foo'));
    }

    /** @test */
    public function getReturnsTheValueFromPost()
    {
        $request = (new Request('GET', '/any/path'))
            ->withQueryParams(['foo' => 'bar'])
            ->withParsedBody(['foo' => 'baz']);

        self::assertSame('baz', $request->get('foo'));
    }

    /** @test */
    public function getReturnsTheValueFromJson()
    {
        $request = (new Request('GET', '/any/path'))
            ->withQueryParams(['foo' => 'bar'])
            ->withBody(stream_for(json_encode(['foo' => 'baz'])));

        self::assertSame('baz', $request->get('foo'));
    }

    /** @test */
    public function getReturnsTheMergedArray()
    {
        $request = (new Request('GET', '/any/path'))
            ->withQueryParams(['foo' => 'bar'])
            ->withBody(stream_for(json_encode(['answer' => 42])));

        self::assertSame([
            'foo' => 'bar',
            'answer' => 42,
        ], $request->get());
    }

    /** @test */
    public function getProtocolReturnsTheProtocolTheClientUsed()
    {
        // by default (on cli) it is http
        $request = new Request('GET', '/any/path');

        $protocol = $request->getProtocol();

        self::assertSame('http', $protocol);
    }

    /** @test */
    public function getProtocolReadsServerParamHttps()
    {
        $request = new Request('GET', '/any/path', [], null, '1.1', ['HTTPS' => 'on']);

        $protocol = $request->getProtocol();

        self::assertSame('https', $protocol);
    }

    /** @test */
    public function getProtocolAcceptsXForwardedProtoForTrustedProxies()
    {
        /** @var m\MockInterface|Request $request */
        $request = m::mock(Request::class)->makePartial();
        $request->__construct('GET', '/any/path', ['X-Forwarded-Proto' => 'foobar']);
        $request->shouldReceive('isTrustedForward')->once()->andReturn(true);

        $protocol = $request->getProtocol();

        self::assertSame('foobar', $protocol);
    }

    /** @test */
    public function getProtocolOffMeansNoSsl()
    {
        $request = new Request('GET', '/any/path', [], null, '1.1', ['HTTPS' => 'off']);

        $protocol = $request->getProtocol();

        self::assertSame('http', $protocol);
    }

    /** @test */
    public function isTrustedForwardIsTrueWhenTrustedProxiesIsNull()
    {
        $this->app->config->trustedProxies = null;
        $request = new Request('GET', '/any/path');

        $trusted = $request->isTrustedForward();

        self::assertTrue($trusted);
    }

    /** @test */
    public function isTrustedForwardIsFalseWhenNoProxiesAreTrusted()
    {
        $this->app->config->trustedProxies = [];
        $request = new Request('GET', '/any/path');

        $trusted = $request->isTrustedForward();

        self::assertFalse($trusted);
    }

    /** @test */
    public function isTrustedForwardIsTrueWhenTheRemoteAddrMatches()
    {
        $this->app->config->trustedProxies = ['127.0.0.1'];
        $request = new Request('GET', '/any/path', [], null, '1.1', ['REMOTE_ADDR' => '127.0.0.1']);

        $trusted = $request->isTrustedForward();

        self::assertTrue($trusted);
    }

    /** @test */
    public function isTrustedForwardIsTrueWhenTheRemoteAddrMatchesAnyRange()
    {
        $this->app->config->trustedProxies = ['127.0.0.1', '10.23.42.0/24'];
        $request = new Request('GET', '/any/path', [], null, '1.1', ['REMOTE_ADDR' => '10.23.42.1']);

        $trusted = $request->isTrustedForward();

        self::assertTrue($trusted);
    }

    /** @test */
    public function isSslSecuredIsTrueWhenTheProtocolIsHttps()
    {
        /** @var m\MockInterface|Request $request */
        $request = m::mock(Request::class)->makePartial();
        $request->__construct('GET', '/any/path');
        $request->shouldReceive('getProtocol')->once()->andReturn('https');

        $secured = $request->isSslSecured();

        self::assertTrue($secured);
    }
}

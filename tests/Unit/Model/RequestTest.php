<?php

namespace Test\Unit\Model;

use App\Model\Request;
use App\Service\ValidatorMessages;
use InvalidArgumentException;
use Mockery as m;
use Tal\ServerRequest;
use Test\Example\CustomController;
use Test\TestCase;
use Verja\Error;
use Verja\Gate;
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
    public function validateCreatesAGateForAcceptedFields()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('accepts')->with(['foo', 'bar'])->once();
        $this->app->instance('verja', $gate);

        $request = new Request('GET', '/any/path');

        $request->validate(['foo', 'bar']);
    }

    /** @test */
    public function validateValidatesQueryByDefault()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->with(['foo' => 42])->once();
        $this->app->instance('verja', $gate);

        $request = m::mock(Request::class)->makePartial();
        $request->shouldReceive('getQuery')->with()->once()->andReturn(['foo' => 42]);

        $request->validate(['foo', 'bar']);
    }

    /** @test */
    public function validateValidatesAnyGetter()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->with(['foo' => 42])->once();
        $this->app->instance('verja', $gate);

        $request = m::mock(Request::class)->makePartial();
        $request->shouldReceive('getPost')->with()->once()->andReturn(['foo' => 42]);

        $request->validate(['foo', 'bar'], 'post'); // post -> getPost
    }

    /** @test */
    public function validateValidatesCustomArrays()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->with(['foo' => 42])->once();
        $this->app->instance('verja', $gate);

        $request = m::mock(Request::class)->makePartial();
        $request->shouldNotReceive('getQuery');

        $request->validate(['foo', 'bar'], ['foo' => 42]);
    }

    /** @test */
    public function validateReturnsDataOnSuccess()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('getData')->with()->once()->andReturn(['foo' => 42]);
        $this->app->instance('verja', $gate);

        $request = m::mock(Request::class)->makePartial();

        list($valid, $data) = $request->validate(['foo'], []);

        self::assertSame(42, $data['foo']);
    }

    /** @test */
    public function validateReturnsErrorsOnFailure()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->once()->andReturn(false);
        $gate->shouldReceive('getErrors')->with()->once()->andReturn($errors = ['foo' => [new Error('WHAT_EVER', '')]]);
        $this->app->instance('verja', $gate);
        $messages = m::mock(ValidatorMessages::class);
        $this->app->shouldReceive('make')->with(ValidatorMessages::class, $errors, [])
            ->once()->andReturn($messages);
        $messages->shouldReceive('getMessages')->with()
            ->once()->andReturn(['foo' => ['What ever...']]);

        $request = m::mock(Request::class)->makePartial();

        list($valid, $errors) = $request->validate(['foo'], []);

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
}

<?php

namespace Test\Unit\Http\Controller;

use function GuzzleHttp\Psr7\stream_for;
use InvalidArgumentException;
use Mockery as m;
use App\Http\Controller\ErrorController;
use Tal\ServerRequest;
use Tal\ServerResponse;
use Test\TestCase;
use Verja\Gate;

class AbstractControllerTest extends TestCase
{
    /** @test */
    public function throwsWhenActionNotFound()
    {
        $controller = new ErrorController('anyMethod');

        self::expectException(\Exception::class);
        self::expectExceptionMessage('Action anyMethod is unknown in ' . ErrorController::class);

        $controller->handle(m::mock(ServerRequest::class));
    }

    /** @test */
    public function callsActionWithArguments()
    {
        $controller = m::mock(ErrorController::class, ['unexpectedError'])->makePartial();
        $request = m::mock(ServerRequest::class);
        $exception = new \Exception('Foo Bar');

        $request->shouldReceive('getAttribute')->with('arguments')
            ->once()->andReturn(['exception' => $exception])->ordered();
        $controller->shouldReceive('unexpectedError')->with($exception)
            ->once()->andReturn(m::mock(ServerResponse::class))->ordered();

        $controller->handle($request);
    }

    /** @test */
    public function preferredContentTypeDefaultsToFirstPossible()
    {
        $controller = new ExampleController('helloWorld');
        $request = new ServerRequest('GET', '/any/path');

        $response = $controller->handle($request);

        self::assertSame(ExampleController::HELLO_WORLD_HTML, $response->getBody()->getContents());
    }

    /** @test */
    public function preferredContentTypeFallsBackToFirstPossible()
    {
        $controller = new ExampleController('helloWorld');
        $request = (new ServerRequest('GET', '/any/path'))
            ->withHeader('Accept', 'image/web');

        $response = $controller->handle($request);

        self::assertSame(ExampleController::HELLO_WORLD_HTML, $response->getBody()->getContents());
    }

    /** @test */
    public function preferredContentTypeIsMatchedAccept()
    {
        $controller = new ExampleController('helloWorld');
        $request = (new ServerRequest('GET', '/any/path'))
            ->withHeader('Accept', 'image/web, application/json');

        $response = $controller->handle($request);

        self::assertSame(ExampleController::HELLO_WORLD_JSON, $response->getBody()->getContents());
    }

    /** @test */
    public function preferredContentTypeGetsSortedByQuality()
    {
        $controller = new ExampleController('helloWorld');
        $request = (new ServerRequest('GET', '/any/path'))
            ->withHeader('Accept', 'image/web;q=1, application/json;q=0.1, application/xml;q=0.5');

        $response = $controller->handle($request);

        self::assertSame(ExampleController::HELLO_WORLD_XML, $response->getBody()->getContents());
    }

    /** @test */
    public function preferredContentTypeEqualQualityWillRemainOrder()
    {
        $controller = new ExampleController('helloWorld');
        $request = (new ServerRequest('GET', '/any/path'))
            ->withHeader('Accept', 'application/json;q=0.2, application/xml;q=0.5, text/html;q=0.5');

        $response = $controller->handle($request);

        self::assertSame(ExampleController::HELLO_WORLD_XML, $response->getBody()->getContents());
    }

    /** @test */
    public function preferredContentTypeIgnoresOtherParameters()
    {
        $controller = new ExampleController('helloWorld');
        $request = (new ServerRequest('GET', '/any/path'))
            ->withHeader('Accept', 'text/html, application/xml;version=1.1');

        $response = $controller->handle($request);

        self::assertSame(ExampleController::HELLO_WORLD_HTML, $response->getBody()->getContents());
    }

    /** @test */
    public function validateCreatesAGateForAcceptedFields()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('accepts')->with(['foo', 'bar'])->once();
        $this->app->instance('verja', $gate);

        $controller = new ExampleController();
        $controller->request = new ServerRequest('GET', '/any/path');
        $controller->validate(['foo', 'bar']);
    }

    /** @test */
    public function validateValidatesQueryByDefault()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->with(['foo' => 42])->once();
        $this->app->instance('verja', $gate);

        $controller = m::mock(ExampleController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('getQuery')->with()->once()->andReturn(['foo' => 42]);
        $controller->validate(['foo', 'bar']);
    }

    /** @test */
    public function validateValidatesAnyGetter()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->with(['foo' => 42])->once();
        $this->app->instance('verja', $gate);

        $controller = m::mock(ExampleController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('getPost')->with()->once()->andReturn(['foo' => 42]);
        $controller->validate(['foo', 'bar'], 'post'); // post -> getPost
    }

    /** @test */
    public function validateValidatesCustomArrays()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->with(['foo' => 42])->once();
        $this->app->instance('verja', $gate);

        $controller = new ExampleController();
        $controller->validate(['foo', 'bar'], ['foo' => 42]);
    }

    /** @test */
    public function validateSetsDataOnSuccess()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('getData')->with()->once()->andReturn(['foo' => 42]);
        $this->app->instance('verja', $gate);

        $controller = new ExampleController();
        $controller->validate(['foo'], [], $data);

        self::assertSame(42, $data['foo']);
    }

    /** @test */
    public function validateSetsErrorOnFailure()
    {
        $gate = m::mock(Gate::class)->makePartial();
        $gate->shouldReceive('validate')->once()->andReturn(false);
        $gate->shouldReceive('getErrors')->with()->once()->andReturn(['foo' => 'What ever...']);
        $this->app->instance('verja', $gate);

        $controller = new ExampleController();
        $controller->validate(['foo'], [], $data, $errors);

        self::assertSame('What ever...', $errors['foo']);
    }

    /** @test */
    public function validateThrowsWhenTheGetterisNotAvailable()
    {
        self::expectException(InvalidArgumentException::class);

        $controller = new ExampleController();
        $controller->validate([], 'foo');
    }

    /** @test */
    public function getQueryReturnsTheCompleteQuery()
    {
        $request = (new ServerRequest('GET', '/any/path'))
            ->withQueryParams(['foo' => 42, 'bar' => 23]);
        $controller = new ExampleController();
        $controller->setRequest($request);

        $query = $controller->getQuery();

        self::assertSame(['foo' => 42, 'bar' => 23], $query);
    }

    /** @test */
    public function getQueryReturnsSpecificParameter()
    {
        $request = (new ServerRequest('GET', '/any/path'))
            ->withQueryParams(['foo' => 42, 'bar' => 23]);
        $controller = new ExampleController();
        $controller->setRequest($request);

        $query = $controller->getQuery('foo');

        self::assertSame(42, $query);
    }

    /** @test */
    public function getQueryReturnsTheDefaultValue()
    {
        $request = new ServerRequest('GET', '/any/path');
        $controller = new ExampleController();
        $controller->setRequest($request);

        $query = $controller->getQuery('foo', 42);

        self::assertSame(42, $query);
    }

    /** @test */
    public function getPostReturnsTheCompletePost()
    {
        $request = (new ServerRequest('POST', '/any/path'))
            ->withParsedBody(['foo' => 42, 'bar' => 23]);
        $controller = new ExampleController();
        $controller->setRequest($request);

        $post = $controller->getPost();

        self::assertSame(['foo' => 42, 'bar' => 23], $post);
    }

    /** @test */
    public function getPostReturnsSpecificParameter()
    {
        $request = (new ServerRequest('POST', '/any/path'))
            ->withParsedBody(['foo' => 42, 'bar' => 23]);
        $controller = new ExampleController();
        $controller->setRequest($request);

        $post = $controller->getPost('foo');

        self::assertSame(42, $post);
    }

    /** @test */
    public function getPostReturnsTheDefaultValue()
    {
        $request = new ServerRequest('POST', '/any/path');
        $controller = new ExampleController();
        $controller->setRequest($request);

        $post = $controller->getPost('foo', 42);

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
        $request = (new ServerRequest('POST', '/any/path'))
            ->withBody(stream_for(json_encode($data)));
        $controller = new ExampleController();
        $controller->setRequest($request);

        self::assertSame($data, $controller->getJson());
    }

    /** @test */
    public function getJsonThrowsWhenBodyIsInvalid()
    {
        $request = (new ServerRequest('POST', '/any/path'))
            ->withBody(stream_for("{foo:'bar'}")); // this is not json but javascript
        $controller = new ExampleController();
        $controller->setRequest($request);

        self::expectException(InvalidArgumentException::class);

        $controller->getJson();
    }

    /** @test */
    public function getJsonThrowsNotWhenTheJsonIsNull()
    {
        $request = (new ServerRequest('POST', '/any/path'))
            ->withBody(stream_for(json_encode(null)));
        $controller = new ExampleController();
        $controller->setRequest($request);

        self::assertNull($controller->getJson());
    }
}

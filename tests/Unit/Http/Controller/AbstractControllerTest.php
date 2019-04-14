<?php

namespace Test\Unit\Http\Controller;

use Mockery as m;
use App\Http\Controller\ErrorController;
use Tal\ServerRequest;
use Tal\ServerResponse;
use Test\TestCase;

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
}

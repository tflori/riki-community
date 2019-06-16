<?php

namespace Test\Unit\Http;

use App\Http\Controller\ErrorController;
use App\Http\RequestHandler;
use App\Model\Request;
use Mockery as m;
use Tal\ServerResponse;
use Test\Example\ResponseCreator;
use Test\TestCase;

class RequestHandlerTest extends TestCase
{
    /** @test */
    public function throwsWhenMethodNotFound()
    {
        $handler = new RequestHandler($this->app, ErrorController::class, 'anyMethod');

        self::expectException(\Exception::class);
        self::expectExceptionMessage('Action anyMethod is unknown in ' . ErrorController::class);

        $handler->handle(m::mock(Request::class));
    }

    /** @test */
    public function makesControllersWithAppAndRequest()
    {
        $handler = new RequestHandler($this->app, ErrorController::class, 'unexpectedError');
        $request = new Request('GET', '/');

        $this->app->shouldReceive('make')->with(ErrorController::class, $this->app, $request)
            ->once()->passthru();

        $handler->handle($request);
    }

    /** @test */
    public function resolvesOtherClassesWithMake()
    {
        $handler = new RequestHandler($this->app, ResponseCreator::class, 'serverTime');
        $request = new Request('GET', '/');

        $this->app->shouldReceive('make')->with(ResponseCreator::class)
            ->once()->passthru();

        $handler->handle($request);
    }

    /** @test */
    public function callsActionWithArguments()
    {
        $handler = new RequestHandler($this->app, ErrorController::class, 'unexpectedError');
        $request = m::mock(Request::class);
        $controller = m::mock(ErrorController::class, [$this->app, $request])->makePartial();
        $this->app->instance(ErrorController::class, $controller);
        $exception = new \Exception('Foo Bar');

        $request->shouldReceive('getAttribute')->with('arguments')
            ->once()->andReturn(['exception' => $exception])->ordered();
        $controller->shouldReceive('unexpectedError')->with($exception)
            ->once()->andReturn(m::mock(ServerResponse::class))->ordered();

        $handler->handle($request);
    }

    /** @test */
    public function prependsRequestIfNecessary()
    {
        $handler = new RequestHandler($this->app, ResponseCreator::class, 'request');
        $request = new Request('GET', '/');
        $controller = m::mock(ResponseCreator::class);
        $this->app->instance(ResponseCreator::class, $controller);

        $controller->shouldReceive('request')->with($request)
            ->once()->passthru();

        $handler->handle($request);
    }
}

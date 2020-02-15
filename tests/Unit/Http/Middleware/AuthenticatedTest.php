<?php

namespace Test\Unit\Http\Middleware;

use App\Http\Middleware\Authenticated;
use App\Http\RequestHandler;
use App\Model\Request;
use Community\Model\User;
use Mockery as m;
use Tal\ServerResponse;
use Test\TestCase;

class AuthenticatedTest extends TestCase
{
    /** @test */
    public function executesTheHandlerWhenAUserIsAuthenticated()
    {
        $this->signIn();
        $requestHandler = m::mock(RequestHandler::class);
        $request = new Request('GET', '/any');
        $requestHandler->shouldReceive('handle')->with($request)
            ->once()->andReturn(new ServerResponse());

        $middleware = new Authenticated($this->app);
        $middleware->process($request, $requestHandler);
    }

    /** @test */
    public function returnsAnErrorWhenNoUserIsLoggedIn()
    {
        $requestHandler = m::mock(RequestHandler::class);
        $request = new Request('GET', '/any');

        $middleware = new Authenticated($this->app);
        $response = $middleware->process($request, $requestHandler);

        self::assertSame(401, $response->getStatusCode());
    }

    /** @test */
    public function returnsAnErrorsWhenUserIsNotActivated()
    {
        $this->signIn(['accountStatus' => User::PENDING]);
        $requestHandler = m::mock(RequestHandler::class);
        $request = new Request('GET', '/any');

        $middleware = new Authenticated($this->app);
        $response = $middleware->process($request, $requestHandler);

        self::assertSame(401, $response->getStatusCode());
    }
}

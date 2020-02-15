<?php

namespace Test\Unit\Http\Middleware;

use App\Http\Middleware\VerifiedCsrfToken;
use App\Http\RequestHandler;
use App\Model\Request;
use Mockery as m;
use Tal\ServerResponse;
use Test\TestCase;

class VerifiedCsrfTokenTest extends TestCase
{
    /** @test */
    public function executesTheHandlerWithValidToken()
    {
        $auth = $this->app->auth;
        $middleware = new VerifiedCsrfToken($this->app);
        $token = $auth->getCsrfToken();
        $request = (new Request('GET', '/any'))
            ->withQueryParams(['csrf_token' => $token]);

        $requestHandler = m::mock(RequestHandler::class);
        $requestHandler->shouldReceive('handle')->with(m::type(Request::class))
            ->once()->andReturn(new ServerResponse());

        $middleware->process($request, $requestHandler);
    }

    /** @test */
    public function verifiesPreviouslyCreatedToken()
    {
        $auth = $this->app->auth;
        $middleware = new VerifiedCsrfToken($this->app);
        $token = $auth->getCsrfToken();
        $request = (new Request('GET', '/any'))
            ->withQueryParams(['csrf_token' => $token]);

        $requestHandler = m::mock(RequestHandler::class);
        $requestHandler->shouldReceive('handle')->with(m::type(Request::class))
            ->once()->andReturn(new ServerResponse());

        // create a second token in the meantime
        $auth->getCsrfToken();

        $middleware->process($request, $requestHandler);
    }

    /** @test */
    public function tokensAreInvalidAfterUsage()
    {
        $auth = $this->app->auth;
        $middleware = new VerifiedCsrfToken($this->app);
        $token = $auth->getCsrfToken();
        $request = (new Request('GET', '/any'))
            ->withQueryParams(['csrf_token' => $token]);

        $requestHandler = m::mock(RequestHandler::class);
        $requestHandler->shouldReceive('handle')->with(m::type(Request::class))
            ->once()->andReturn(new ServerResponse());

        $middleware->process($request, $requestHandler);
        $response = $middleware->process($request, $requestHandler);

        self::assertSame(400, $response->getStatusCode());
    }
}

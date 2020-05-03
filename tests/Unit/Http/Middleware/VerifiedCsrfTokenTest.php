<?php

namespace Test\Unit\Http\Middleware;

use App\Http\Controller\AuthController;
use App\Http\HttpKernel;
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
        $middleware = new VerifiedCsrfToken($this->app);
        $token = $middleware->createToken();
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
        $middleware = new VerifiedCsrfToken($this->app);
        $token = $middleware->createToken();
        $request = (new Request('GET', '/any'))
            ->withQueryParams(['csrf_token' => $token]);

        $requestHandler = m::mock(RequestHandler::class);
        $requestHandler->shouldReceive('handle')->with(m::type(Request::class))
            ->once()->andReturn(new ServerResponse());

        // create a second token in the meantime
        $middleware->createToken();

        $middleware->process($request, $requestHandler);
    }

    /** @test */
    public function tokensAreInvalidAfterUsage()
    {
        $middleware = new VerifiedCsrfToken($this->app);
        $token = $middleware->createToken();
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

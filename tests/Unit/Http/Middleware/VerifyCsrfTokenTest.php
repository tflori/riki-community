<?php

namespace Test\Unit\Http\Middleware;

use App\Http\Controller\AuthController;
use App\Http\HttpKernel;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\RequestHandler;
use App\Model\Request;
use Mockery as m;
use Tal\ServerResponse;
use Test\TestCase;

class VerifyCsrfTokenTest extends TestCase
{
    /** @test */
    public function verifiesCreatedToken()
    {
        $middleware = new VerifyCsrfToken($this->app);
        $token = $middleware->createToken();
        $request = (new Request('GET', '/any'))
            ->withQueryParams(['csrf_token' => $token]);

        $requestHandler = m::mock(RequestHandler::class);
        $requestHandler->shouldReceive('handle')->with(m::type(Request::class))
            ->once()->andReturnUsing(function (Request $request) {
                self::assertTrue($request->getAttribute('csrfTokenVerified'));
                return new ServerResponse();
            });

        $middleware->process($request, $requestHandler);
    }

    /** @test */
    public function verifiesPreviouslyCreatedToken()
    {
        $middleware = new VerifyCsrfToken($this->app);
        $token = $middleware->createToken();
        $request = (new Request('GET', '/any'))
            ->withQueryParams(['csrf_token' => $token]);

        $requestHandler = m::mock(RequestHandler::class);
        $requestHandler->shouldReceive('handle')->with(m::type(Request::class))
            ->once()->andReturnUsing(function (Request $request) {
                self::assertTrue($request->getAttribute('csrfTokenVerified'));
                return new ServerResponse();
            });

        // create a second token in the meantime
        $middleware->createToken();

        $middleware->process($request, $requestHandler);
    }

    /** @test */
    public function tokensAreInvalidAfterUsage()
    {
        $middleware = new VerifyCsrfToken($this->app);
        $token = $middleware->createToken();
        $request = (new Request('GET', '/any'))
            ->withQueryParams(['csrf_token' => $token]);

        $requestHandler = m::mock(RequestHandler::class);
        $requestHandler->shouldReceive('handle')->with(m::type(Request::class))
            ->twice()->andReturnUsing(function (Request $request) {
                self::assertTrue($request->getAttribute('csrfTokenVerified'));
                return new ServerResponse();
            }, function (Request $request) {
                self::assertNotTrue($request->getAttribute('csrfTokenVerified'));
                return new ServerResponse();
            });

        $middleware->process($request, $requestHandler);
        $middleware->process($request, $requestHandler);
    }
}

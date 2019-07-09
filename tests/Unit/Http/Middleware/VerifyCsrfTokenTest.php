<?php

namespace Test\Unit\Http\Middleware;

use App\Http\Controller\AuthController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\RequestHandler;
use App\Model\Request;
use Mockery as m;
use Tal\ServerResponse;
use Test\TestCase;

class VerifyCsrfTokenTest extends TestCase
{
    /** @test */
    public function verifiesCsrfToken()
    {
        $this->app->session->set('csrfToken', $token = 'fooBar');
        $request = (new Request('GET', '/any'))
            ->withQueryParams(['csrf_token' => 'fooBar']);

        $requestHandler = m::mock(RequestHandler::class);
        $requestHandler->shouldReceive('handle')->with(m::type(Request::class))
            ->once()->andReturnUsing(function (Request $request) {
                self::assertTrue($request->getAttribute('csrfTokenVerified'));
                return new ServerResponse();
            });

        $middleware = new VerifyCsrfToken($this->app);
        $middleware->process($request, $requestHandler);
    }

    /** @test */
    public function verifiesTokenFromResponse()
    {
        $token = json_decode(
            (new AuthController($this->app, new Request('GET', '/auth/token')))
                ->getCsrfToken()
                ->getBody()
        );
        $request = (new Request('GET', '/any'))
            ->withQueryParams(['csrf_token' => $token]);

        $requestHandler = m::mock(RequestHandler::class);
        $requestHandler->shouldReceive('handle')->with(m::type(Request::class))
            ->once()->andReturnUsing(function (Request $request) {
                self::assertTrue($request->getAttribute('csrfTokenVerified'));
                return new ServerResponse();
            });

        $middleware = new VerifyCsrfToken($this->app);
        $middleware->process($request, $requestHandler);
    }
}

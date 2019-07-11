<?php

namespace Test\Unit\Http\Controller\UserController;

use App\Http\Controller\UserController;
use App\Model\Request;
use Community\Model\Token\ActivationCode;
use Community\Model\User;
use function GuzzleHttp\Psr7\stream_for;
use Test\TestCase;

class ActivationTest extends TestCase
{
    /** @test */
    public function activateRequiresAuthentication()
    {
        $request = (new Request('POST', '/user/activate'));
        $controller = new UserController($this->app, $request);

        $response = $controller->activate($request);

        self::assertSame(401, $response->getStatusCode());
    }

    /** @test */
    public function activateRequiresVerifiedCsrfToken()
    {
        $this->signIn(['accountStatus' => User::PENDING]);
        $request = (new Request('POST', '/user/activate', ['Accept' => 'application/json']));
        $controller = new UserController($this->app, $request);

        $response = $controller->activate($request);

        self::assertSame(400, $response->getStatusCode());
        self::assertArraySubset([
            'message' => 'Invalid request token'
        ], json_decode($response->getBody(), true));
    }

    /** @test */
    public function activateFailsWithInvalidToken()
    {
        $this->signIn(['accountStatus' => User::PENDING]);
        $request = (new Request('POST', '/user/activate', ['Accept' => 'application/json']))
            ->withBody(stream_for(json_encode(['token' => 'foobar'])))
            ->withAttribute('csrfTokenVerified', true);
        $controller = new UserController($this->app, $request);

        $response = $controller->activate($request);

        self::assertSame(400, $response->getStatusCode());
        self::assertArraySubset([
            'message' => 'Invalid activation code',
        ], json_decode($response->getBody(), true));
    }

    /** @test */
    public function activateRequiresStatusPending()
    {
        $user = $this->signIn(['accountStatus' => User::DISABLED]);
        $request = (new Request('POST', '/user/activate', ['Accept' => 'application/json']))
            ->withBody(stream_for(json_encode(['token' => 'foobar'])))
            ->withAttribute('csrfTokenVerified', true);
        $controller = new UserController($this->app, $request);
        $this->addFetcherResult(ActivationCode::class, [
            '/token"? *= \'foobar/',
        ], $user);

        $response = $controller->activate($request);

        self::assertSame(400, $response->getStatusCode());
        self::assertArraySubset([
            'message' => 'Account disabled',
        ], json_decode($response->getBody(), true));
    }

    /** @test */
    public function activateActivatesAndReturnsTheUser()
    {
        $user = $this->signIn(['accountStatus' => User::PENDING]);
        $this->addFetcherResult(ActivationCode::class, [
            '/token"? *= \'foobar/',
        ], $user);

        $request = (new Request('POST', '/user/activate', ['Accept' => 'application/json']))
            ->withBody(stream_for(json_encode(['token' => 'foobar'])))
            ->withAttribute('csrfTokenVerified', true);
        $controller = new UserController($this->app, $request);
        $response = $controller->activate($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(User::ACTIVATED, $user->accountStatus);
        self::assertArraySubset([
            'id' => $user->id,
            'accountStatus' => User::ACTIVATED,
        ], json_decode($response->getBody(), true));
    }
}

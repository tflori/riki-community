<?php

namespace Test\Unit\Http\Controller\UserController;

use App\Http\Controller\UserController;
use App\Model\Request;
use Community\Model\Token\ActivationCode;
use Community\Model\Token\ActivationToken;
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
        ], new ActivationCode());

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

    /** @test */
    public function activateByTokenRequiresValidToken()
    {
        $request = (new Request('GET', '/user/activate/foobar123'));
        $controller = new UserController($this->app, $request);
        $response = $controller->activateByToken($request, 'foobar123');

        self::assertSame(400, $response->getStatusCode());
        self::assertContains('Invalid activation token', (string)$response->getBody());
    }

    /** @test */
    public function activateByTokenActivatesTheUser()
    {
        $this->addFetcherResult(ActivationToken::class, [
            '/token"? *= \'foobar123/',
        ], new ActivationToken(['user_id' => 23]));
        $this->addFetcherResult(User::class, [
            '/id"? *= 23',
        ], $user = $this->ormCreateMockedEntity(User::class, [
            'id' => 23,
            'account_status' => User::PENDING,
        ]));

        $user->shouldReceive('activate')->with()
            ->once();

        $request = (new Request('GET', '/user/activate/foobar123'));
        $controller = new UserController($this->app, $request);
        $controller->activateByToken($request, 'foobar123');
    }

    /** @test */
    public function activateByTokenRedirectsToHome()
    {
        $this->addFetcherResult(ActivationToken::class, [
            '/token"? *= \'foobar123/',
        ], new ActivationToken(['user_id' => 23]));
        $this->addFetcherResult(User::class, [
            '/id"? *= 23',
        ], $user = $this->ormCreateMockedEntity(User::class, [
            'id' => 23,
            'account_status' => User::PENDING,
        ]));

        $request = (new Request('GET', '/user/activate/foobar123'));
        $controller = new UserController($this->app, $request);
        $response = $controller->activateByToken($request, 'foobar123');

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/', $response->getHeader('Location')[0]);
    }

    /** @test */
    public function activateByTokenRequiresPendingUser()
    {
        $this->addFetcherResult(ActivationToken::class, [
            '/token"? *= \'foobar123/',
        ], new ActivationToken(['user_id' => 23]));
        $this->addFetcherResult(User::class, [
            '/id"? *= 23',
        ], $user = $this->ormCreateMockedEntity(User::class, [
            'id' => 23,
            'account_status' => User::ACTIVATED,
        ]));

        $request = (new Request('GET', '/user/activate/foobar123'));
        $controller = new UserController($this->app, $request);
        $response = $controller->activateByToken($request, 'foobar123');

        self::assertSame(400, $response->getStatusCode());
    }
}

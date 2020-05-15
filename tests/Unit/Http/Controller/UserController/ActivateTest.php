<?php

namespace Test\Unit\Http\Controller\UserController;

use App\Http\Controller\UserController;
use App\Model\Request;
use Community\Model\Token\ActivationCode;
use Community\Model\User;
use function GuzzleHttp\Psr7\stream_for;
use Test\TestCase;

class ActivateTest extends TestCase
{
    /** @test */
    public function requiresAuthentication()
    {
        $request = (new Request('POST', '/user/activate'));
        $controller = new UserController($this->app, $request);

        $response = $controller->activate($request);

        self::assertSame(401, $response->getStatusCode());
    }

    /** @test */
    public function failsWithInvalidToken()
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
    public function requiresStatusPending()
    {
        $user = $this->signIn(['accountStatus' => User::DISABLED]);
        $this->ormAddResult(ActivationCode::class, new ActivationCode(['user_id' => $user->id]))
            ->where('token', 'foobar');

        $request = (new Request('POST', '/user/activate', ['Accept' => 'application/json']))
            ->withBody(stream_for(json_encode(['token' => 'foobar'])))
            ->withAttribute('csrfTokenVerified', true);
        $controller = new UserController($this->app, $request);
        $response = $controller->activate($request);

        self::assertSame(400, $response->getStatusCode());
        self::assertArraySubset([
            'message' => 'Account disabled',
        ], json_decode($response->getBody(), true));
    }

    /** @test */
    public function activatesAndReturnsTheUser()
    {
        $user = $this->signIn(['accountStatus' => User::PENDING]);
        $this->ormExpectUpdate($user);
        $this->ormAddResult(ActivationCode::class, new ActivationCode(['user_id' => $user->id]))
            ->where('token', 'foobar');

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

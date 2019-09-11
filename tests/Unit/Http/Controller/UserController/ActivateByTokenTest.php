<?php

namespace Test\Unit\Http\Controller\UserController;

use App\Http\Controller\UserController;
use App\Model\Request;
use Community\Model\Token\ActivationToken;
use Community\Model\User;
use Test\TestCase;

class ActivateByTokenTest extends TestCase
{
    /** @test */
    public function requiresValidToken()
    {
        $request = (new Request('GET', '/user/activate/foobar123'));
        $controller = new UserController($this->app, $request);
        $response = $controller->activateByToken($request, 'foobar123');

        self::assertSame(400, $response->getStatusCode());
        self::assertContains('Invalid activation token', (string)$response->getBody());
    }

    /** @test */
    public function activatesTheUser()
    {
        $user = $this->ormCreateMockedEntity(User::class, [
            'id' => 23,
            'account_status' => User::PENDING,
        ]);
        $this->ormAddResult(ActivationToken::class, new ActivationToken(['user_id' => 23]))
            ->where('token', 'foobar123');

        $user->shouldReceive('activate')->with()
            ->once();

        $request = (new Request('GET', '/user/activate/foobar123'));
        $controller = new UserController($this->app, $request);
        $controller->activateByToken($request, 'foobar123');
    }

    /** @test */
    public function redirectsToHome()
    {
        $this->ormCreateMockedEntity(User::class, [
            'id' => 23,
            'account_status' => User::PENDING,
        ]);
        $this->ormAddResult(ActivationToken::class, new ActivationToken(['user_id' => 23]))
            ->where('token', 'foobar123');

        $request = (new Request('GET', '/user/activate/foobar123'));
        $controller = new UserController($this->app, $request);
        $response = $controller->activateByToken($request, 'foobar123');

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/', $response->getHeader('Location')[0]);
    }

    /** @test */
    public function requiresPendingUser()
    {
        $this->ormCreateMockedEntity(User::class, [
            'id' => 23,
            'account_status' => User::ACTIVATED,
        ]);
        $this->ormAddResult(ActivationToken::class, new ActivationToken(['user_id' => 23]))
            ->where('token', 'foobar123');

        $request = (new Request('GET', '/user/activate/foobar123'));
        $controller = new UserController($this->app, $request);
        $response = $controller->activateByToken($request, 'foobar123');

        self::assertSame(400, $response->getStatusCode());
        self::assertContains('Account disabled', (string)$response->getBody());
    }
}

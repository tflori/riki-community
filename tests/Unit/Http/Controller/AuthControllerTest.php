<?php

namespace Test\Unit\Http\Controller;

use App\Http\Controller\AuthController;
use App\Model\Request;
use Community\Model\User;
use Test\TestCase;

class AuthControllerTest extends TestCase
{
    /** @test */
    public function requestsTheUserByEmail()
    {
        $request = new Request('POST', '/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'asdf123',
        ]));
        $this->expectFetch(User::class, ['/email"?\s*=\s*\'john.doe@example.com\'/']);
        $controller = new AuthController($this->app, $request);

        $controller->authenticate();
    }

    /** @test */
    public function returnsBadRequestErrorWhenUserNotFound()
    {
        $request = new Request('POST', '/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'asdf123',
        ]));
        $this->expectFetch(User::class, ['/email"?\s*=\s*\'john.doe@example.com\'/']);
        $controller = new AuthController($this->app, $request);

        $response = $controller->authenticate();

        self::assertSame(400, $response->getStatusCode());
    }

    /** @test */
    public function returnsBadRequestWhenPasswordDoesNotMatch()
    {
        $request = new Request('POST', '/auth', [], json_encode([
            'email' => 'john.doe@example.com',
            'password' => 'asdf123',
        ]));
        $user = new User([
            'email' => 'john.doe@example.com',
            'password' => password_hash('foo bar', PASSWORD_BCRYPT, ['cost' => 4]),
            'created' => date('c'),
            'updated' => date('c'),
        ]);
        $this->expectFetch(User::class, ['/email"?\s*=\s*\'john.doe@example.com\'/'], $user);
        $controller = new AuthController($this->app, $request);

        $response = $controller->authenticate();

        self::assertSame(400, $response->getStatusCode());
    }
}
